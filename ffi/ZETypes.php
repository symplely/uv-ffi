<?php

declare(strict_types=1);

use FFI\CData;

if (!\class_exists('ZendExecutor')) {
    final class ZendExecutor extends ZE
    {
        /**  This should be equal to ZEND_MM_ALIGNMENT */
        const MM_ALIGNMENT = 8;

        protected $isZval = false;

        /**
         * Represents `EG(executor_globals)` _macro_.
         *- Trick here is to look at internal structures and steal pointer to our value from current frame
         *
         * @return self
         */
        public static function init(): ZendExecutor
        {
            $value = static::executor_globals();
            return static::init_value($value->current_execute_data->prev_execute_data)
                ->with_current($value->current_execute_data);
        }


        public static function class_table(): HashTable
        {
            return HashTable::init_value(static::executor_globals()->class_table);
        }

        public static function function_table(): HashTable
        {
            return HashTable::init_value(static::executor_globals()->function_table);
        }

        public static function objects_store(): ZendObjectsStore
        {
            return ZendObjectsStore::init_value(static::executor_globals()->objects_store);
        }

        /**
         * Returns the previous execution data entry (aka stack)
         */
        public function previous_state(): ZendExecutor
        {
            if ($this->ze_other_ptr->prev_execute_data === null) {
                return \ze_ffi()->zend_error(\E_WARNING, 'There is no previous execution data.');
            }

            return static::init_value($this->ze_other_ptr->prev_execute_data)
                ->with_current($this->ze_other->prev_execute_data);
        }

        public function current_state(): ZendExecutor
        {
            return static::init_value($this->ze_other)
                ->with_current($this->ze_other->prev_execute_data);
        }

        /**
         * Returns the "return value"
         */
        public function return_value(): Zval
        {
            if (!\is_null($this->ze_other_ptr->return_value) && $this->ze_other_ptr->return_value->u1->v->type < 30)
                return Zval::init_value($this->ze_other_ptr->return_value);

            return Zval::init();
        }

        public function with_current(CData $ptr)
        {
            $this->ze_other = $ptr;

            return $this;
        }

        /**
         * Returns the current function or method
         *
         * @return ZendFunction
         */
        public function func(): ZendFunction
        {
            if ($this->ze_other_ptr->func === null) {
                return \ze_ffi()->zend_error(\E_WARNING, 'Function creation is not available in the current context');
            }

            if ($this->ze_other_ptr->func->common->scope === null) {
                $func = ZendFunction::init_value($this->ze_other_ptr->func);
            } else {
                $func = ZendMethod::init_value($this->ze_other_ptr->func);
            }

            return $func;
        }

        /**
         * Returns an execution state with scope, variables, etc.
         * - `zend_execute_data` provides information about current stack frame
         *
         * @return zend_execute_data            CData
         *
         * @property zend_op* $opline;          executed opline
         * @property zend_execute_data* $call;  current call
         * @property zval* $return_value;
         * @property zend_function* $func;      executed function
         * @property zval $This;                $This->u2->num_args
         * @property zend_execute_data* $prev_execute_data;
         * @property zend_array* $symbol_table;
         * @property void** $run_time_cache;     cache op_array->run_time_cache
         */
        public function execution_state(): CData
        {
            return $this->ze_other_ptr;
        }

        public function number_arguments(): int
        {
            return $this->ze_other_ptr->This->u2->num_args;
        }

        /**
         * Returns call variable from the stack by number.
         * Represents `ZEND_CALL_VAR_NUM()` _macro_.
         *
         * @param int $variableNum Variable number - a calls zend_execute_data
         *
         * @return CData zval* pointer
         */
        public function call_variable_number(int $variableNum): CData
        {
            // (((zval*)(call)) + (ZEND_CALL_FRAME_SLOT + ((int)(n))))
            $pointer = \ze_ffi()->cast('zval *', $this->ze_other_ptr);

            return $pointer + static::call_frame_slot() + $variableNum;
        }

        /**
         * Returns call variable from the stack.
         * Represents `ZEND_CALL_VAR()` _macro_.
         *
         * @param int $variableOffset Variable offset
         *
         * @return CData zval* pointer
         */
        public function call_variable(int $variableOffset): CData
        {
            // ((zval*)(((char*)(call)) + ((int)(n))))
            return \ze_ffi()->cast(
                'zval *',
                (\ze_ffi()->cast('char *', $this->ze_other_ptr) + $variableOffset)
            );
        }

        /**
         * Returns the argument by it's index.
         * Represents `ZEND_CALL_ARG()` _macro_.
         *
         * Argument index is starting from 0.
         */
        public function call_argument(int $argumentIndex): Zval
        {
            if ($argumentIndex >= $this->number_arguments())
                return \ze_ffi()->zend_error(\E_WARNING, "Argument index is greater than available arguments");

            $pointer = $this->call_variable_number($argumentIndex);
            return Zval::init_value($pointer);
        }

        /**
         * Returns execution arguments as array of values
         *
         * @return CData[]
         */
        public function call_arguments(): array
        {
            $arguments = [];
            $totalArguments = $this->number_arguments();
            for ($index = 0; $index < $totalArguments; $index++) {
                $arguments[] = $this->call_argument($index);
            }

            return $arguments;
        }

        /**
         * Returns the current object scope
         *
         * This contains following: this + call_info + num_args
         */
        public function This(): Zval
        {
            return Zval::init_value(\ffi_ptr($this->ze_other_ptr->This));
        }

        /**
         * Set a new fake scope and returns previous value (to restore it later)
         *
         * @return CData|null
         */
        public function fake_scope(?CData $newScope): ?CData
        {
            $oldScope = $this->ze_other_ptr->fake_scope;
            $this->ze_other_ptr->fake_scope = $newScope;

            return $oldScope;
        }

        /**
         * Returns the current symbol table.
         *         *
         * @return HashTable
         */
        public function symbol_table(): HashTable
        {
            return HashTable::init_value($this->ze_other_ptr->symbol_table);
        }

        /**
         * Calculates the call frame slot size.
         * Represents `ZEND_CALL_FRAME_SLOT()` _macro_.
         */
        private static function call_frame_slot(): int
        {
            static $slotSize;
            if ($slotSize === null) {
                $alignedSizeOfExecuteData = static::aligned_size(\FFI::sizeof(\ze_ffi()->type('zend_execute_data')));
                $alignedSizeOfZval = static::aligned_size(\FFI::sizeof(\ze_ffi()->type('zval')));

                $slotSize = \intdiv(($alignedSizeOfExecuteData + $alignedSizeOfZval) - 1, $alignedSizeOfZval);
            }

            return $slotSize;
        }
    }
}

if (!\class_exists('ZendModule')) {
    final class ZendModule extends ZE
    {
        protected $isZval = false;

        protected \ReflectionExtension $reflection;

        public function __call($method, $args)
        {
            if (\method_exists($this->reflection, $method)) {
                return $this->reflection->$method(...$args);
            } else {
                throw new \Error("$method does not exist");
            }
        }

        public function addReflection(string $name)
        {
            $this->reflection = new \ReflectionExtension($name);
        }

        public static function init(string $name): self
        {
            /** @var Zval */
            $ext = HashTable::init_value(static::module_registry())
                ->find($name);
            if ($ext === null) {
                return \ze_ffi()->zend_error(\E_WARNING, "Module %s should be in the engine.", $name);
            }

            if ($ext()->u1->v->type !== ZE::IS_PTR) {
                return \ze_ffi()->zend_error(\E_WARNING, 'Pointer entry available only for the type IS_PTR');
            }

            $extPtr = $ext()->value->ptr;

            return static::init_value(\ze_ffi()->cast('zend_module_entry*', $extPtr));
        }

        public static function init_value(CData $ptr): self
        {
            $method = (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();
            $method->update($ptr);

            $method->addReflection($ptr->name);

            return $method;
        }

        /**
         * Returns the size of module itself
         *
         * Typically, this should be equal to Core::type('zend_module_entry')
         */
        public function size(): int
        {
            return $this->ze_other_ptr->size;
        }

        /**
         * Returns the size of module global structure
         */
        public function globals_size(): int
        {
            return $this->ze_other_ptr->globals_size;
        }

        /**
         * Returns a pointer (if any) to global memory area or null if extension doesn't use global memory structure
         */
        public function globals_ptr(): ?CData
        {
            if (ZEND_THREAD_SAFE) {
                return $this->ze_other_ptr->globals_id_ptr;
            } else {
                return $this->ze_other_ptr->globals_ptr;
            }
        }

        /**
         * Was module started or not
         */
        public function module_started(): bool
        {
            return (bool) $this->ze_other_ptr->module_started;
        }

        /**
         * Is module was compiled/designed for debug mode
         *
         * @see ZEND_DEBUG_BUILD
         */
        public function is_debug(): bool
        {
            return (bool) $this->ze_other_ptr->zend_debug;
        }

        /**
         * Is module compiled with thread safety or not
         *
         * @see ZEND_THREAD_SAFE
         */
        public function is_zts(): bool
        {
            return (bool) $this->ze_other_ptr->zts;
        }

        /**
         * Returns the module ordinal number
         */
        public function module_number(): int
        {
            return $this->ze_other_ptr->module_number;
        }

        /**
         * Returns the api version
         */
        public function zend_api(): int
        {
            return $this->ze_other_ptr->zend_api;
        }

        public function __debugInfo()
        {
            if (!isset($this->ze_other_ptr)) {
                return [];
            }

            $result  = [];
            $methods = (new \ReflectionClass(self::class))->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $methodName  = $method->getName();
                $hasZeroArgs = $method->getNumberOfRequiredParameters() === 0;
                if ((\strpos($methodName, 'get') === 0) && $hasZeroArgs) {
                    $friendlyName          = \lcfirst(\substr($methodName, 3));
                    $result[$friendlyName] = $this->$methodName();
                }

                if ((\strpos($methodName, 'is') === 0) && $hasZeroArgs) {
                    $friendlyName          = \lcfirst(\substr($methodName, 2));
                    $result[$friendlyName] = $this->$methodName();
                }
            }

            return $result;
        }
    }
}

if (!\class_exists('ZendFunction')) {
    class ZendFunction extends ZE
    {
        protected $isZval = false;

        /** @var \ReflectionFunction */
        protected object $reflection;

        public function __call($method, $args)
        {
            if (\method_exists($this->reflection, $method)) {
                return $this->reflection->$method(...$args);
            } else {
                throw new \Error("$method does not exist");
            }
        }

        public static function init(string ...$arguments): self
        {
            $functionName = \reset($arguments);

            /** @var Zval */
            $zvalFunction = HashTable::init_value(static::executor_globals()->function_table)
                ->find(\strtolower($functionName));

            if ($zvalFunction === null) {
                return \ze_ffi()->zend_error(\E_WARNING, "Function %s should be in the engine.", $functionName);
            }

            $zendFunc = self::init_value($zvalFunction->func());
            $zendFunc->addReflection($functionName);

            return $zendFunc;
        }

        public static function init_value(CData $ptr): self
        {
            if ($ptr->type === ZE::ZEND_INTERNAL_FUNCTION) {
                $functionPtr = $ptr->function_name;
            } else {
                $functionPtr = $ptr->common->function_name;
            }

            $function = (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();
            $function->update($ptr);

            if ($functionPtr !== null) {
                $string = ZendString::init_value($functionPtr);
                $function->addReflection($string->value());
            }

            return $function;
        }

        public function addReflection(string ...$arguments)
        {
            $this->reflection = new \ReflectionFunction(\reset($arguments));
        }

        public function __debugInfo(): array
        {
            return [
                'name' => $this->reflection->getName(),
            ];
        }

        public function getName()
        {
            return ZendString::init_value($this->ze_other_ptr->name)->value();
        }

        /**
         * Returns a pointer to the common structure (to work natively with zend_function and zend_internal_function)
         */
        protected function getPointer(): CData
        {
            // For zend_internal_function we have same fields directly in current structure
            if ($this->reflection->isInternal())
                return $this->ze_other_ptr;

            // zend_function uses "common" struct to store all important fields
            return $this->ze_other_ptr->common;
        }

        /**
         * Returns the hash key for function or method
         */
        protected function getHash(): string
        {
            return $this->reflection->name;
        }
    }
}

if (!\class_exists('ZendMethod')) {
    final class ZendMethod extends ZendFunction
    {
        /** @var \ReflectionMethod */
        protected object $reflection;

        public function __call($method, $args)
        {
            if (\method_exists($this->reflection, $method)) {
                return $this->reflection->$method(...$args);
            } else {
                throw new \Error("$method does not exist");
            }
        }

        public static function init(string ...$arguments): self
        {
            $className = \array_shift($arguments);
            $methodName = \reset($arguments);

            /** @var Zval */
            $zvalClass = HashTable::init_value(static::executor_globals()->class_table)
                ->find(\strtolower($className));
            if ($zvalClass === null) {
                return \ze_ffi()->zend_error(\E_WARNING, "Class %s should be in the engine.", $className);
            }

            if ($zvalClass()->u1->v->type !== ZE::IS_PTR) {
                return \ze_ffi()->zend_error(\E_WARNING, 'Class entry available only for the type IS_PTR');
            }

            $classPtr = $zvalClass()->value->ce;

            /** @var Zval */
            $zvalMethod = HashTable::init_value(\ffi_ptr($classPtr->function_table))
                ->find(\strtolower($methodName));

            if ($zvalMethod === null) {
                return \ze_ffi()->zend_error(\E_WARNING, "Method %s was not found in the class.", $methodName);
            }

            $method = self::init_value($zvalMethod->func());
            $method->addReflection($className, $methodName);

            return $method;
        }

        public function addReflection(string ...$arguments)
        {
            $className = \array_shift($arguments);
            $this->reflection = new \ReflectionMethod($className, \reset($arguments));
        }

        public static function init_value(CData $ptr): self
        {
            if ($ptr->type !== ZE::ZEND_INTERNAL_FUNCTION) {
                $functionNamePtr = $ptr->common->function_name;
                $scopeNamePtr    = $ptr->common->scope->name;
            } else {
                $functionNamePtr = $ptr->function_name;
                $scopeNamePtr    = $ptr->scope->name;
            }

            $scopeName = ZendString::init_value($scopeNamePtr);
            $functionName = ZendString::init_value($functionNamePtr);

            $method = (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();
            $method->update($ptr);
            $method->addReflection($scopeName->value(), $functionName->value());

            return $method;
        }

        /**
         * Gets the declaring class
         */
        public function declaringClass()
        {
            if ($this->getPointer()->scope === null) {
                return \ze_ffi()->zend_error(\E_WARNING, 'Not in a class scope');
            }

            return self::init_value($this->getPointer()->scope);
        }

        public function __debugInfo(): array
        {
            return [
                'name'  => $this->reflection->getName(),
                'class' => $this->declaringClass()->getName()
            ];
        }

        /**
         * Returns the hash key for function or method
         */
        protected function getHash(): string
        {
            return $this->reflection->class . '::' . $this->reflection->name;
        }
    }
}

if (!\class_exists('ZendCompiler')) {
    final class ZendCompiler extends ZE
    {
        protected $isZval = false;

        /**
         * Represents `CG(compiler_globals)` _macro_.
         *
         * @return self
         */
        public static function init(): ZendCompiler
        {
            return static::init_value(static::compiler_globals());
        }

        public static function class_table(): HashTable
        {
            return HashTable::init_value(static::compiler_globals()->class_table);
        }

        /**
         * Returns a `HashTable` with all registered functions
         *
         * @return HashTable
         */
        public static function function_table(): HashTable
        {
            return HashTable::init_value(static::compiler_globals()->function_table);
        }
    }
}

if (!\class_exists('ZendResource')) {
    final class ZendResource extends ZE
    {
        protected $isZval = false;

        /**
         * Returns the internal type identifier for this resource.
         *
         * @param int $newType - Changes the internal type identifier for this resource
         * - Low-level API, can bring a segmentation fault
         * @return int|void
         * @internal
         */
        public function type(int $newType = null)
        {
            if (\is_null($newType))
                return $this->ze_other_ptr->type;

            $this->ze_other_ptr->type = $newType;
        }

        /**
         * Returns a resource handle.
         *
         * @param int $newHandle Changes object internal handle to another one
         * @return int|void
         * @internal
         */
        public function handle(int $newHandle = null)
        {
            if (\is_null($newHandle))
                return $this->ze_other_ptr->handle;

            $this->ze_other_ptr->handle = $newHandle;
        }

        /**
         * Returns the low-level raw data, associated with this resource.
         */
        public function ptr(): CData
        {
            return  $this->ze_other_ptr->ptr;
        }

        public function __debugInfo(): array
        {
            $info = [
                'type'     => $this->type(),
                'handle'   => $this->handle(),
                'refcount' => $this->gc_refcount(),
                'data'     => $this->ptr()
            ];

            return $info;
        }

        public static function init($argument): ZendResource
        {
            $current = Zval::constructor($argument);
            if ($current()->u1->v->type !== ZE::IS_RESOURCE) {
                return \ze_ffi()->zend_error(\E_WARNING, 'Resource creation available only for the type IS_RESOURCE');
            }

            return static::init_value($current()->value->res);
        }
    }
}

if (!\class_exists('ZendString')) {
    final class ZendString extends ZE
    {
        protected $isZval = false;

        public static function init($string): ZendString
        {
            $current = ZendExecutor::init()->call_argument(0);
            if ($current()->u1->v->type !== ZE::IS_STRING) {
                return \ze_ffi()->zend_error(\E_WARNING, 'String creation available only for the type IS_STRING');
            }

            return static::init_value($current()->value->str[0]);
        }

        /**
         * Returns a hash for given string
         */
        public function hash(): int
        {
            return $this->ze_other_ptr->h;
        }

        /**
         * Returns a string length
         */
        public function length(): int
        {
            return $this->ze_other_ptr->len;
        }

        /**
         * Returns a PHP representation of engine string
         */
        public function value(): string
        {
            $zval = Zval::new(ZE::IS_STRING, $this->ze_other_ptr[0]);
            $zval->native_value($realString);
            \ffi_free($zval());

            return $realString;
        }

        /**
         * This methods releases a string entry
         *
         * @see zend_string.h:zend_string_release function
         */
        public function release(): void
        {
            if (!$this->is_variable(ZE::GC_IMMUTABLE)) {
                if ($this->gc_delRef() === 0) {
                    ffi_free($this->ze_other_ptr);
                }
            }
        }

        /**
         * Creates a copy of string value
         *
         * @see zend_string.h::zend_string_copy function
         *
         * @return self
         */
        public function copy(): self
        {
            if (!$this->is_variable(ZE::GC_IMMUTABLE)) {
                $this->gc_addRef();
            }

            return $this;
        }

        public function __debugInfo(): array
        {
            return [
                'value'    => $this->value(),
                'length'   => $this->length(),
                'refcount' => $this->gc_refcount(),
                'hash'     => $this->hash(),
            ];
        }
    }
}

if (!\class_exists('ZendReference')) {
    final class ZendReference extends ZE
    {
        protected $isZval = false;

        public static function init(&$reference): ZendReference
        {
            $current = ZendExecutor::init()->call_argument(0);
            if ($current()->u1->v->type !== ZE::IS_REFERENCE) {
                return \ze_ffi()->zend_error(\E_WARNING, 'Reference creation available only for the type IS_REFERENCE');
            }

            return static::init_value($current()->value->ref);
        }

        /**
         * Returns the internal value, stored for this reference
         */
        public function internal_value(): Zval
        {
            return Zval::init_value($this->ze_other_ptr->val);
        }

        public function __debugInfo(): array
        {
            $info = [
                'refcount' => $this->gc_refcount(),
                'value'    => $this->internal_value()
            ];

            return $info;
        }
    }
}

if (!\class_exists('ZendClosure')) {
    final class ZendClosure extends ZE
    {
        protected $isZval = false;

        public static function init(\Closure $closure): self
        {
            $zval = ZendExecutor::init()->call_argument(0);
            if ($zval()->u1->v->type !== ZE::IS_OBJECT) {
                return \ze_ffi()->zend_error(\E_WARNING, 'Object entry available only for the type IS_OBJECT');
            }

            return static::init_value(\ze_ffi()->cast('zend_closure*', $zval()->value->obj));
        }

        /**
         * Returns a `ZendObject` that represents this closure
         */
        public function closure_object(): ZendObject
        {
            return ZendObject::init_value($this->ze_other_ptr->std);
        }

        /**
         * Returns the called scope (if present), otherwise null for unbound closures
         */
        public function called_scope(): ?string
        {
            if ($this->ze_other_ptr->called_scope === null) {
                return null;
            }

            $calledScopeName = ZendString::init($this->ze_other_ptr->called_scope->name);

            return $calledScopeName->value();
        }

        /**
         * Changes the scope of closure to another one
         * @internal
         */
        public function change(?string $newScope): void
        {
            // If we have a null value, then just clean this scope internally
            if ($newScope === null) {
                $this->ze_other_ptr->called_scope = null;
                return;
            }

            $name = \strtolower($newScope);

            $zvalClass = ZendExecutor::class_table()->find($name);
            if ($zvalClass === null) {
                \ze_ffi()->zend_error(\E_WARNING, "Class %s was not found", $newScope);
                return;
            }

            if ($zvalClass()->u1->v->type !== ZE::IS_PTR) {
                \ze_ffi()->zend_error(\E_WARNING, 'Class entry available only for the type IS_PTR');
                return;
            }

            $this->ze_other_ptr->called_scope = $zvalClass()->value->ce;
        }

        /**
         * Changes the current $this, bound to the closure
         *
         * @param object $object New object
         *
         * @internal
         */
        public function changeThis(object $object): void
        {
            $zval = ZendExecutor::init()->call_argument(0);
            $objectZval = $zval();
            \FFI::memcpy($this->ze_other_ptr->this_ptr, $objectZval[0], \FFI::sizeof(\ze_ffi()->type('zval')));
        }

        /**
         * Returns `zend_function` data for this closure
         */
        public function func(): CData
        {
            return $this->ze_other_ptr->func;
        }
    }
}

if (!\class_exists('ZendObjectsStore')) {
    final class ZendObjectsStore extends ZE implements \Countable, \ArrayAccess
    {
        protected $isZval = false;
        /**
         * @see zend_objects_API.h:OBJ_BUCKET_INVALID macro
         */
        const OBJ_BUCKET_INVALID = 1;

        public function count(): int
        {
            return $this->ze_other_ptr->top - 1;
        }

        public function offsetExists($offset): bool
        {
            $isValidOffset = ($offset >= 0) && ($offset < $this->ze_other_ptr->top);
            $isExists      = $isValidOffset && $this->is_object_valid($this->ze_other_ptr->object_buckets[$offset]);

            return $isExists;
        }

        /**
         * Returns an object from the storage by it's id or null if this object was released
         *
         * @param int $offset Identifier of object
         *
         * @see spl_object_id()
         */
        public function offsetGet($offset): ?ZendObject
        {
            if (!\is_int($offset)) {
                throw new \InvalidArgumentException('Object identifier should be an integer');
            }
            if ($offset < 0 || $offset > $this->ze_other_ptr->top - 1) {
                // We use -2 because exception object also increments index by one
                throw new \OutOfBoundsException("Index {$offset} is out of bounds 0.." . ($this->ze_other_ptr->top - 2));
            }
            $object = $this->ze_other_ptr->object_buckets[$offset];

            // Object can be invalid, for that case we should return null
            if (!$this->is_object_valid($object)) {
                return null;
            }

            $objectEntry = ZendObject::init_value($object);

            return $objectEntry;
        }

        public function offsetSet($offset, $value): void
        {
            throw new \LogicException('Object store is read-only structure');
        }

        public function offsetUnset($offset): void
        {
            throw new \LogicException('Object store is read-only structure');
        }

        /**
         * Returns the free head (aka next handle)
         */
        public function next_handle(): int
        {
            return $this->ze_other_ptr->free_list_head;
        }

        /**
         * Detaches existing object from the object store
         *
         * - This call doesn't invokes object destructors, only detaches an object from the store.
         *
         * @see zend_objects_API.h:SET_OBJ_INVALID macro
         * @internal
         */
        public function detach(int $offset): void
        {
            if ($offset < 0 || $offset > $this->ze_other_ptr->top - 1) {
                // We use -2 because exception object also increments index by one
                throw new \OutOfBoundsException("Index {$offset} is out of bounds 0.." . ($this->ze_other_ptr->top - 2));
            }
            $rawPointer        = \ze_ffi()->cast('zend_uintptr_t', $this->ze_other_ptr->object_buckets[$offset]);
            $invalidPointer    = $rawPointer->cdata | self::OBJ_BUCKET_INVALID;
            $rawPointer->cdata = $invalidPointer;

            $this->ze_other_ptr->object_buckets[$offset] = \ze_ffi()->cast('zend_object*', $rawPointer);
        }

        /**
         * Checks if the given object pointer is valid or not
         *
         * @see zend_objects_API.h:IS_OBJ_VALID macro
         */
        private function is_object_valid(?CData $objectPointer): bool
        {
            if ($objectPointer === null) {
                return false;
            }

            $rawPointer = \ze_ffi()->cast('zend_uintptr_t', $objectPointer);
            $isValid    = ($rawPointer->cdata & self::OBJ_BUCKET_INVALID) === 0;

            return $isValid;
        }
    }
}

if (!\class_exists('ZendObject')) {
    final class ZendObject extends ZE
    {
        protected $isZval = false;
        private HashTable $properties;

        public static function init(object $instance): self
        {
            $refValue = Zval::constructor($instance);
            if ($refValue()->u1->v->type !== ZE::IS_OBJECT) {
                return \ze_ffi()->zend_error(\E_WARNING, 'Object entry available only for the type IS_OBJECT');
            }

            return static::init_value($refValue()->value->obj);
        }

        public static function init_value(CData $ptr): self
        {
            /** @var static */
            $object = (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();
            $object->update($ptr);

            return $object;
        }

        /**
         * Changes the class of object to another one
         *
         * @internal
         */
        public function change(string $newClass): void
        {
            $zvalClass = ZendExecutor::class_table()->find(strtolower($newClass));
            if ($zvalClass === null) {
                \ze_ffi()->zend_error(\E_WARNING, "Class %s was not found", $newClass);
                return;
            }

            if ($zvalClass()->u1->v->type !== self::IS_PTR) {
                \ze_ffi()->zend_error(\E_WARNING, 'Class entry available only for the type IS_PTR');
                return;
            }

            $this->ze_other_ptr->ce = $zvalClass()->value->ce;
        }

        /**
         * Returns an object `handle`, this should be equal to **spl_object_id()**.
         *
         * @param integer|null $newHandle - if set, changes object internal handle to another one
         * @return integer|void
         */
        public function handle(int $newHandle = null): int
        {
            if (\is_null($newHandle))
                return $this->pointer->handle;

            $this->pointer->handle = $newHandle;
        }

        /**
         * Returns a PHP instance of object, associated with this entry
         */
        public function native_value(): object
        {
            $entry = Zval::new(ZE::IS_OBJECT, $this->pointer[0]);
            $entry->native_value($realObject);
            \ffi_free($entry());

            return $realObject;
        }

        public function update(CData $ptr, bool $isOther = false): self
        {
            $this->ze_other_ptr = $ptr;
            if ($this->ze_other_ptr->properties !== null) {
                $this->properties = HashTable::init_value($this->ze_other_ptr->properties);
            }

            return $this;
        }

        public function getName(): string
        {
            return ZendString::init($this->ze_other_ptr->name)->value();
        }

        public function __debugInfo(): array
        {
            $info = [
                'class'    => $this->getName(),
                'handle'   => $this->handle(),
                'refcount' => $this->gc_refcount()
            ];

            if (isset($this->properties)) {
                $info['properties'] = $this->properties;
            }

            return $info;
        }
    }
}

if (\PHP_ZTS && !\class_exists('TsHashTable')) {
    final class TsHashTable extends HashTable
    {
        public static function module_registry(): TsHashTable
        {
            return static::init_value(\ffi_ptr(\ze_ffi()->module_registry));
        }
    }
}


if (!\class_exists('HashTable')) {
    class HashTable extends ZE implements \IteratorAggregate
    {
        protected $isZval = false;

        /**
         * Retrieve an external iterator
         *
         * @return Traversable An instance of an object implementing **Iterator** or **Traversable**
         */
        public function getIterator(): \Iterator
        {
            $iterator = function () {
                $index = 0;
                while ($index < $this->ze_other_ptr->nNumOfElements) {
                    $item = $this->ze_other_ptr->arData[$index];
                    $index++;
                    if ($item->val->u1->v->type === ZE::IS_UNDEF) {
                        continue;
                    }
                    $key = $item->key !== null ? ZendString::init_value($item->key)->value() : null;
                    yield $key => Zval::init_value($item->val);
                }
            };

            return $iterator();
        }

        /**
         * Represents `zend_hash_str_find_ptr()` inline _macro_.
         *
         * @param string $key
         * @return Zval
         */
        public function find_ptr(string $key): Zval
        {
            $string = ZendString::init($key);
            return Zval::init_value((\PHP_ZTS)
                    ? \ze_ffi()->zend_ts_hash_str_find($this->ze_other_ptr, $string->value(), $string->length())
                    : \ze_ffi()->zend_hash_str_find($this->ze_other_ptr, $string->value(), $string->length())
            );
        }

        /**
         * Performs search by key in the HashTable.
         *
         * @param string $key Key to find
         *
         * @return Zval|null Value or null if not found
         */
        public function find(string $key): ?Zval
        {
            $string = ZendString::init($key);
            $pointer = (\PHP_ZTS)
                ? \ze_ffi()->zend_ts_hash_find($this->ze_other_ptr, \ffi_ptr($string()))
                : \ze_ffi()->zend_hash_find($this->ze_other_ptr, \ffi_ptr($string()));

            if ($pointer !== null) {
                $pointer = Zval::init_value($pointer);
            }

            return $pointer;
        }

        /**
         * Deletes a value by key from the HashTable.
         *
         * @param string $key Key in the hash to delete
         * @internal
         */
        public function delete(string $key): self
        {
            $string = ZendString::init($key);
            $result = (\PHP_ZTS)
                ? \ze_ffi()->zend_ts_hash_del($this->ze_other_ptr, \ffi_ptr($string()))
                : \ze_ffi()->zend_hash_del($this->ze_other_ptr, \ffi_ptr($string()));

            if ($result === ZE::FAILURE) {
                return \ze_ffi()->zend_error(\E_WARNING, "Can not delete an item with key %s", $key);
            }

            return $this;
        }

        /**
         * Adds new value to the HashTable
         */
        public function add(string $key, Zval $value): self
        {
            $string = ZendString::init($key);
            $result = (\PHP_ZTS)
                ? \ze_ffi()->zend_ts_hash_add($this->ze_other_ptr, \ffi_ptr($string()), $value())
                : \ze_ffi()->zend_hash_add_or_update(
                    $this->ze_other_ptr,
                    \ffi_ptr($string()),
                    $value(),
                    ZE::HASH_ADD_NEW
                );

            if ($result === ZE::FAILURE) {
                return \ze_ffi()->zend_error(\E_WARNING, "Can not add an item with key %s", $key);
            }

            return $this;
        }

        public function __debugInfo()
        {
            return \iterator_to_array($this->getIterator());
        }
    }
}

if (!\class_exists('Resource')) {
    class Resource extends ZE
    {
        protected $isZval = false;
        protected $fd = [];
        protected ?int $index = null;

        private ?Zval $zval = null;

        /** @var Resource|PhpStream */
        private static $instances = null;

        public function __destruct()
        {
            $this->free();
        }

        public function add(Zval $zval, int $fd): self
        {
            $this->index = $fd;
            if (!isset(static::$instances[$fd])) {
                $this->zval = $zval;
                static::$instances[$fd] = $this;
            }

            return $this;
        }

        public function clear(int $handle): void
        {
            if (!\is_null($this->fd) && isset($this->fd[$handle])) {
                [$fd, $res] = $this->fd[$handle];
                unset($this->fd[$fd], $this->fd[$res]);
                static::$instances[$fd] = static::$instances[$res] = null;
            } elseif (isset(static::$instances[$handle])) {
                static::$instances[$handle] = null;
            }

            if (\count($this->fd) === 0) {
                $zval = $this->zval;
                $this->zval = null;
                static::$instances = null;
                $zval->free();
            }
        }

        public function get_zval(): ?Zval
        {
            return $this->zval;
        }

        public function add_pair(Zval $zval, int $fd1, int $resource1, int $fd0 = null, int $resource0 = null)
        {
            $this->zval = $zval;
            $this->index = $fd1;
            $this->fd[$fd1] = $this->fd[$resource1] = [$fd1, $resource1];
            static::$instances[$fd1] = static::$instances[$resource1] = $this;
            if (!\is_null($fd0) && !\is_null($resource0)) {
                $this->fd[$fd0] = $this->fd[$resource0] = [$fd0, $resource0];
                static::$instances[$fd0] = static::$instances[$resource0] = $this;
            }

            return $this;
        }

        public function get_pair(int $fd): ?int
        {
            return $this->fd[$fd][0] ?? null;
        }

        public static function is_valid(int $fd): bool
        {
            return isset(static::$instances[$fd]) && static::$instances[$fd] instanceof static;
        }

        /**
         * @param integer $handle
         * @param boolean $getZval
         * @param boolean $getPair
         * @return Zval|int|CData|null
         */
        public static function get_fd(int $handle, bool $getZval = false, bool $getPair = false)
        {
            if (static::is_valid($handle)) {
                $resource = static::$instances[$handle];
                if ($getZval)
                    return $resource->get_zval();
                elseif ($getPair)
                    return $resource->get_pair($handle);
                else
                    return $resource();
            }
        }

        public static function remove_fd(int $handle): void
        {
            if (static::is_valid($handle)) {
                $object = static::$instances[$handle];
                $object->clear($handle);
            }
        }

        public static function init(string $type = 'uv_file'): self
        {
            return new static($type, false);
        }
    }
}


if (!\class_exists('PhpStream')) {
    final class PhpStream extends Resource
    {
        public static function init(string $type = null): self
        {
            return new static('struct _php_stream', false);
        }

        /**
         * Represents `php_stream_to_zval()` _macro_.
         *
         * Use this to assign the stream to a zval and tell the stream that is
         * has been exported to the engine; it will expect to be closed automatically
         * when the resources are auto-destructed.
         *
         * @param php_stream $ptr
         * @return Zval
         */
        public static function init_stream(CData $ptr): Zval
        {
            if (!\is_typeof($ptr, 'struct _php_stream*')) {
                return \ze_ffi()->zend_error(
                    \E_WARNING,
                    'Only STREAM resource type is accepted, detected: (%s)',
                    \ffi_str_typeof($ptr)
                );
            }

            $res = \zend_register_resource($ptr, \ze_ffi()->php_file_le_stream());
            $zval = Zval::init();
            $zval->macro(ZE::RES_P, $res);

            return $zval;
        }

        /**
         * @param string $path filename or URL to be opened for _reading_, _writing_, or both depending on the value of `mode`
         * @param string $mode
         * @param integer $options
         * - `ZE::USE_PATH `- Relative paths will be applied to the locations specified in the .ini option include_path. This option is specified by the built-in fopen() function when the third parameter is passed as TRUE.
         * - `ZE::STREAM_USE_URL` - When set, only remote URLs will be opened. Wrappers that are not flagged as remote URLs such as file://, php://, compress.zlib://, and compress.bzip2:// will result in failure.
         * - `ZE::ENFORCE_SAFE_MODE` - Despite the naming of this constant, safe mode checks are only truly enforced if this option is set, and the corresponding safe_mode ini directive has been enabled. Excluding this option causes safe_mode checks to be skipped regardless of the INI setting.
         * - `ZE::REPORT_ERRORS` - If an error is encountered during the opening of the specified resource, an error will only be generated if this flag is passed.
         * - `ZE::STREAM_MUST_SEEK` - Some streams, such as socket transports, are never seekable; others, such as file handles, are only seekable under certain circumstances. If a calling scope specifies this option and the wrapper determines that it cannot guarantee seekability, it will refuse to open the stream.
         * - `ZE::STREAM_WILL_CAST` - If the calling scope will require the stream to be castable to a stdio or posix file descriptor, it should pass this option to the open_wrapper function so that it can fail gracefully before I/O operations have begun.
         * - `ZE::STREAM_ONLY_GET_HEADERS` - Indicates that only metadata will be requested from the stream. In practice this is used by the http wrapper to populate the http_response_headers global variable without actually fetching the contents of the remote file.
         * - `ZE::STREAM_DISABLE_OPEN_BASEDIR` - Like the safe_mode check, this option, even when absent, still requires the open_basedir ini option to be enabled for checks to be performed. Specifying it as an option simply allows the default check to be bypassed.
         * - `ZE::STREAM_OPEN_PERSISTENT` - Instructs the streams layer to allocate all internal structures persistently and register the associated resource in the persistent list.
         * - `ZE::IGNORE_PATH` - If not specified, the default include path will be searched. Most URL wrappers ignore this option.
         * - `ZE::IGNORE_URL` - When provided, only local files will be opened by the streams layer. All is_url wrappers will be ignored.
         * @param object|null $opened
         * @param object|null $context
         * @return self
         */
        public static function open_wrapper(
            string $path,
            string $mode,
            int $options,
            ?object $opened = null,
            ?object $context = null
        ): self {
            return static::init_value(
                \ze_ffi()->_php_stream_open_wrapper_ex(
                    $path,
                    $mode,
                    $options,
                    $opened,
                    $context
                )
            );
        }

        /**
         * Represents `ext-uv` _macro_ `PHP_UV_FD_TO_ZVAL()`.
         *
         * @param int $fd
         * @param string $mode
         * @return resource
         */
        public static function fd_to_zval($fd, $mode = 'wb+', bool $getZval = false)
        {
            $zval = PhpStream::get_fd($fd);
            if (!$zval instanceof Zval) {
                $stream = \ze_ffi()->_php_stream_fopen_from_fd($fd, $mode, null);
                try {
                    $zval = PhpStream::init_stream($stream);
                    /** @var PhpStream */
                    $php_stream = static::init_value($stream);
                    $php_stream->add($zval, $fd);
                } catch (\Throwable $e) {
                    return \ze_ffi()->_php_stream_free($stream, ZE::PHP_STREAM_FREE_CLOSE);
                }
            }

            if ($getZval)
                return $zval;

            return \zval_native($zval);
        }

        public static function php_stream_from_zval(Zval $pZval)
        {
            if (($stream = \ze_ffi()->cast(
                'php_stream*',
                \ze_ffi()->zend_fetch_resource2_ex(
                    $pZval(),
                    "stream",
                    \ze_ffi()->php_file_le_stream(),
                    \ze_ffi()->php_file_le_pstream()
                )
            )) == NULL) {
                return;
            }

            return static::init_value($stream);
        }

        public static function php_stream_from_res(ZendResource $res)
        {
            if (($stream = \ze_ffi()->cast('php_stream*', \ze_ffi()->zend_fetch_resource2(
                $res(),
                "stream",
                \ze_ffi()->php_file_le_stream(),
                \ze_ffi()->php_file_le_pstream()
            ))) == NULL) {
                return;
            }

            return static::init_value($stream);
        }

        /**
         * Represents `ext-uv` _function_ `php_uv_zval_to_fd()`.
         *
         * @param Zval $ptr
         * @return int `fd`
         */
        public static function zval_to_fd(Zval $ptr): int
        {
            $fd = -1;
            if ($ptr->macro(ZE::TYPE_P) === ZE::IS_RESOURCE) {
                $handle = $ptr()->value->res->handle;
                $zval_fd = Resource::get_fd($handle, true);
                if ($zval_fd instanceof Zval)
                    return Resource::get_fd($handle)[0];

                $zval_fd = \fd_type();
                $fd = $zval_fd();
                $stream = \ze_ffi()->cast(
                    'php_stream*',
                    \ze_ffi()->zend_fetch_resource2($ptr()->value->res, 'stream', \ze_ffi()->php_file_le_stream(), \ze_ffi()->php_file_le_pstream())
                );

                if (\is_cdata($stream)) {
                    if (
                        (\ze_ffi()->_php_stream_cast(
                            $stream,
                            ZE::PHP_STREAM_AS_FD | ZE::PHP_STREAM_CAST_INTERNAL,
                            \ffi_void($fd),
                            1
                        ) != ZE::SUCCESS)
                        || $fd < 0
                    ) {
                        $fd = -1;
                    }
                } else {
                    \ze_ffi()->zend_error(\E_WARNING, "unhandled resource type detected.");
                    $fd = -1;
                }

                if ($fd === -1)
                    unset($zval_fd);
            } elseif ($ptr->macro(ZE::TYPE_P) === ZE::IS_LONG) {
                $fd = $ptr->macro(ZE::LVAL_P);
                if ($fd < 0) {
                    $fd = -1;
                }

                /* make sure that a valid resource handle was passed - issue #36 */
                $err = \uv_guess_handle($fd);
                if ($err == UV::UNKNOWN_HANDLE) {
                    \ze_ffi()->zend_error(E_WARNING, "invalid resource type detected");
                    $fd = -1;
                }
            }

            if (\is_cdata($fd)) {
                $zval_fd->add($ptr, $handle);
                return $fd[0];
            }

            return $fd;
        }

        /**
         * Represents `ext-uv` _macro_ `PHP_UV_CHECK_VALID_FD()`.
         *
         * @param Zval $fd
         * @param Zval $stream
         * @return mixed
         */
        public static function check_valid_fd($fd, $stream)
        {
            if ($fd < 0) {
                \ze_ffi()->zend_error(\E_WARNING, "invalid variable passed. can't convert to fd.");
                $stream->free();
                return false;
            }

            if ($fd->macro(ZE::TYPE_INFO_P) === ZE::IS_UNDEF) {
                $fd->copy($stream());
            }

            $fd->native_value($resource);

            return $resource;
        }

        /**
         * Represents `ext-uv` _function_ `php_uv_zval_to_valid_poll_fd()`.
         *
         * @param Zval $ptr
         * @return php_socket_t
         */
        public static function zval_to_poll_fd(Zval $ptr)
        {
            /*
	php_socket_t fd = -1;
	php_stream *stream;

	/* Validate Checks

#if !defined(PHP_WIN32) || (defined(HAVE_SOCKETS) && !defined(COMPILE_DL_SOCKETS)) || PHP_VERSION_ID >= 80000
	php_socket *socket;
#endif
	/* TODO: is this correct on windows platform?
	if (Z_TYPE_P(ptr) == IS_RESOURCE) {
		if ((stream = (php_stream *) zend_fetch_resource_ex(ptr, NULL, php_file_le_stream()))) {
			/* make sure only valid resource streams are passed - plainfiles and most php streams are invalid
			if (stream->wrapper && !strcmp((char *)stream->wrapper->wops->label, "PHP") && (!stream->orig_path || (strncmp(stream->orig_path, "php://std", sizeof("php://std") - 1) && strncmp(stream->orig_path, "php://fd", sizeof("php://fd") - 1)))) {
				php_error_docref(NULL, E_WARNING, "invalid resource passed, this resource is not supported");
				return -1;
			}

			/* Some streams (specifically STDIO and encrypted streams) can be cast to FDs
			if (php_stream_cast(stream, PHP_STREAM_AS_FD_FOR_SELECT | PHP_STREAM_CAST_INTERNAL, (void*)&fd, 1) == SUCCESS && fd >= 0) {
				if (stream->wrapper && !strcmp((char *)stream->wrapper->wops->label, "plainfile")) {
#ifndef PHP_WIN32
					struct stat stat;
					fstat(fd, &stat);
					if (!S_ISFIFO(stat.st_mode))
#endif
					{
						php_error_docref(NULL, E_WARNING, "invalid resource passed, this plain files are not supported");
						return -1;
					}
				}
				return fd;
			}

			fd = -1;
#if PHP_VERSION_ID < 80000 && (!defined(PHP_WIN32) || (defined(HAVE_SOCKETS) && !defined(COMPILE_DL_SOCKETS)))
		} else if (php_sockets_le_socket_ptr && (socket = (php_socket *) zend_fetch_resource_ex(ptr, NULL, php_sockets_le_socket_ptr()))) {
			fd = socket->bsd_socket;
#endif
		} else {
			php_error_docref(NULL, E_WARNING, "unhandled resource type detected.");
			fd = -1;
		}
#if PHP_VERSION_ID >= 80000 && (!defined(PHP_WIN32) || (defined(HAVE_SOCKETS) && !defined(COMPILE_DL_SOCKETS)))
	} else if (socket_ce && Z_TYPE_P(ptr) == IS_OBJECT && Z_OBJCE_P(ptr) == socket_ce && (socket = (php_socket *) ((char *)(Z_OBJ_P(ptr)) - XtOffsetOf(php_socket, std)))) {
		fd = socket->bsd_socket;
#endif
	}

	return fd;
*/
        }
    }
}

if (!\class_exists('Zval')) {
    final class Zval extends ZE
    {
        /**
         * Zval `value` constructor for a copy.
         *
         * @param mixed $argument to be extracted
         * @return Zval
         */
        public static function constructor($argument): Zval
        {
            $current = ZendExecutor::init()->call_argument(0);
            $value = Zval::new($current()->u1->type_info, $current()[0]);
            $current->copy($value());

            return $value;
        }

        /**
         * Creates a blank `Zval` _instance_.
         * @return self
         */
        public static function init(): self
        {
            return new static('struct _zval_struct');
        }

        /**
         * Type-friendly getter to return `zend_function/zend_internal_function` directly.
         */
        public function func(): CData
        {
            if ($this->ze_ptr->u1->v->type !== ZE::IS_PTR) {
                return \ze_ffi()->zend_error(\E_WARNING, 'Function entry available only for the type IS_PTR');
            }

            $function = $this->ze_ptr->value->func;
            // If we have an internal function, then we should cast it to the zend_internal_function
            if ($function->type === ZE::ZEND_INTERNAL_FUNCTION) {
                $function = \ze_ffi()->cast('zend_internal_function *', $function);
            }

            return $function;
        }

        /**
         * Returns _native_ value for `userland`.
         *
         * @param mixed $returnValue
         */
        public function native_value(&$returnValue): void
        {
            $reference = ZendReference::init($returnValue);
            $zval = $reference->internal_value();

            $this->copy($zval());
        }

        /**
         * _Change_ the existing value of `userland` to another one.
         *
         * @param mixed $newValue Value to change to
         */
        public function change_value($newValue): void
        {
            $changeZval = ZendExecutor::init()->call_argument(0);
            $this->copy($changeZval());
        }

        /**
         * Represents `ZVAL_COPY()` _macro_.
         *
         * @param CData $dstZval
         * @return void
         */
        public function copy(CData $dstZval): void
        {
            $typeInfo = $this->ze_ptr->u1->type_info;
            $gc = $this->gc();

            // Content of ZVAL_COPY_VALUE_EX()
            if (\PHP_INT_SIZE === 4) {
                $w2 = $this->ze_ptr->value->ww->w2;
                $dstZval->value->counted = $gc;
                $dstZval->value->ww->w2  = $w2;
                $dstZval->u1->type_info  = $typeInfo;
            } elseif (\PHP_INT_SIZE === 8) {
                $dstZval->value->counted = $gc;
                $dstZval->u1->type_info  = $typeInfo;
            } else {
                \ze_ffi()->zend_error(\E_ERROR, 'Unknown SIZEOF_SIZE_T');
            }

            if ($this->is_type_info_refcounted($typeInfo)) {
                $this->gc_addRef();
            }
        }

        public function __debugInfo(): array
        {
            $this->native_value($nativeValue);

            return [
                'type'  => self::name($this->ze_ptr->u1->v->type),
                'value' => $nativeValue
            ];
        }

        /**
         * Creates a new zval from it's type and value.
         *
         * @param int   $type Value type
         * @param CData $value Value, should be zval-compatible
         *
         * @return Zval
         */
        public static function new(int $type, CData $value, bool $isPersistent = false): Zval
        {
            // Allocate non-owned Zval
            $entry = \ze_ffi()->new('zval', false, $isPersistent);

            $entry->u1->type_info = $type;
            $entry->value->zv = \ze_ffi()->cast('zval', $value);

            return static::init_value(\ffi_ptr($entry));
        }

        /**
         * Represents various **accessor** macros.
         *
         * @param string|int $accessor One of:
         * -          Macro                 Return/Set type
         * - `ZE::TRUE`   for `ZVAL_TRUE()`
         * - `ZE::FALSE`   for `ZVAL_FALSE()`
         * - `ZE::NULL`   for `ZVAL_NULL()`
         * - `ZE::UNDEF`   for `ZVAL_UNDEF()`
         * - `ZE::BOOL`   for `ZVAL_BOOL()`   `unsigned char`
         * -
         * - `ZE::TYPE_P`   for `Z_TYPE_P()`  `unsigned char`
         * - `ZE::TYPE_INFO_P`  for `Z_TYPE_INFO_P()` `unsigned char`
         * - `ZE::TYPE_INFO_REFCOUNTED`  for `Z_TYPE_INFO_REFCOUNTED()` `boolean`
         * - `ZE::LVAL_P`   for `Z_LVAL_P()`  `zend_long`
         * - `ZE::DVAL_P`   for `Z_DVAL_P()`  `double`
         * - `ZE::STR_P`    for `Z_STR_P()`   `zend_string *`
         * - `ZE::STRVAL_P` for `Z_STRVAL_P()`  `char *`
         * - `ZE::STRLEN_P` for `Z_STRLEN_P()`  `size_t`
         * - `ZE::ARR_P`    for `Z_ARR_P()`   `HashTable *`
         * - `ZE::ARRVAL_P` for `Z_ARRVAL_P()`    `HashTable *`
         * - `ZE::OBJ_P`    for `Z_OBJ_P()`   `zend_object *`
         * - `ZE::OBJCE_P`  for `Z_OBJCE_P()` `zend_class_entry *`
         * - `ZE::RES_P`    for `Z_RES_P()`   `zend_resource *`
         * - `ZE::REF_P`    for `Z_REF_P()`   `zend_reference *`
         * - `ZE::REFVAL_P` for `Z_REFVAL_P()`  `zval *`
         * - `ZE::COUNTED_P`  for `Z_COUNTED_P()` `*`
         *
         * @param mixed|CData|null $valuePtr a `value/pointer` to set to.
         * @return self|mixed|CData|bool|int
         */
        public function macro($accessor, $valuePtr = null)
        {
            switch ($accessor) {
                case ZE::TYPE_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->u1->v->type;
                    break;
                case ZE::TYPE_INFO_REFCOUNTED:
                    return ((\is_null($valuePtr) ? $this->ze_ptr->u1->type_info : $valuePtr) & ZE::Z_TYPE_FLAGS_MASK) != 0;
                case ZE::LVAL_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->lval;

                    $this->ze_ptr->value->lval = $valuePtr;
                    $this->ze_ptr->u1->type_info = ZE::IS_LONG;
                    break;
                case ZE::DVAL_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->dval;

                    $this->ze_ptr->value->dval = $valuePtr;
                    $this->ze_ptr->u1->type_info = ZE::IS_DOUBLE;
                    break;
                case ZE::STR_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->str;

                    $this->ze_ptr->value->str = $valuePtr;
                    $this->ze_ptr->u1->type_info = ($this->gc_flags($valuePtr) & ZE::IS_STR_INTERNED)
                        ? ZE::IS_INTERNED_STRING_EX
                        : ZE::IS_STRING_EX;
                    break;
                case ZE::ARR_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->arr;

                    $this->ze_ptr->value->arr = $valuePtr;
                    $this->ze_ptr->u1->type_info = ZE::IS_ARRAY_EX;
                    break;
                case ZE::RES_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->res;

                    $this->ze_ptr->value->res = $valuePtr;
                    $this->ze_ptr->u1->type_info = ZE::IS_RESOURCE_EX;
                    break;
                case ZE::OBJ_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->obj;

                    $this->ze_ptr->value->obj = $valuePtr;
                    $this->ze_ptr->u1->type_info = ZE::IS_OBJECT_EX;
                    break;
                case ZE::COUNTED_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->counted;
                    break;
                case ZE::PTR_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->ptr;
                    break;
                case ZE::ARR_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->arr;
                    break;
                case ZE::REFVAL_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->ref->val;
                    break;
                case ZE::REF_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->value->ref;

                    $this->ze_ptr->value->ref = $valuePtr;
                    $this->ze_ptr->u1->type_info = ZE::IS_REFERENCE_EX;
                    break;
                case ZE::TYPE_INFO_P:
                    if (\is_null($valuePtr))
                        return $this->ze_ptr->u1->type_info;

                    $accessor = $valuePtr;
                case ZE::TRUE:
                case ZE::FALSE:
                case ZE::NULL:
                case ZE::UNDEF:
                case ZE::BOOL:
                    $valuePtr = $accessor === ZE::BOOL
                        ? ($valuePtr ? ZE::IS_TRUE : ZE::IS_FALSE)
                        : $accessor;

                    $this->ze_ptr->u1->type_info = $valuePtr;
                    break;
            }

            return $this;
        }
    }
}
