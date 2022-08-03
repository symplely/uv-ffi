<?php

declare(strict_types=1);

use FFI\CData;

abstract class ZE
{
    /** Wrappers support */
    const IGNORE_PATH                     = 0x00000000;
    const USE_PATH                        = 0x00000001;
    const IGNORE_URL                      = 0x00000002;
    const REPORT_ERRORS                   = 0x00000008;

    /** If you don't need to write to the stream, but really need to
     * be able to seek, use this flag in your options. */
    const STREAM_MUST_SEEK                = 0x00000010;
    /** If you are going to end up casting the stream into a FILE* or
     * a socket, pass this flag and the streams/wrappers will not use
     * buffering mechanisms while reading the headers, so that HTTP
     * wrapped streams will work consistently.
     * If you omit this flag, streams will use buffering and should end
     * up working more optimally.
     * */
    const STREAM_WILL_CAST                = 0x00000020;

    /** this flag applies to php_stream_locate_url_wrapper */
    const STREAM_LOCATE_WRAPPERS_ONLY     = 0x00000040;

    /** this flag is only used by include/require functions */
    const STREAM_OPEN_FOR_INCLUDE         = 0x00000080;

    /** this flag tells streams to ONLY open urls */
    const STREAM_USE_URL                  = 0x00000100;

    /** this flag is used when only the headers from HTTP request are to be fetched */
    const STREAM_ONLY_GET_HEADERS         = 0x00000200;

    /** don't apply open_basedir checks */
    const STREAM_DISABLE_OPEN_BASEDIR     = 0x00000400;

    /** get (or create) a persistent version of the stream */
    const STREAM_OPEN_PERSISTENT          = 0x00000800;

    /** use glob stream for directory open in plain files stream */
    const STREAM_USE_GLOB_DIR_OPEN        = 0x00001000;

    /** don't check allow_url_fopen and allow_url_include */
    const STREAM_DISABLE_URL_PROTECTION   = 0x00002000;

    /** assume the path passed in exists and is fully expanded, avoiding syscalls */
    const STREAM_ASSUME_REALPATH          = 0x00004000;

    /** Allow blocking reads on anonymous pipes on Windows. */
    const STREAM_USE_BLOCKING_PIPE        = 0x00008000;

    /** call ops->close */
    const PHP_STREAM_FREE_CALL_DTOR         = 1;
    /** pefree(stream) */
    const PHP_STREAM_FREE_RELEASE_STREAM    = 2;
    /** tell ops->close to not close it's underlying handle */
    const PHP_STREAM_FREE_PRESERVE_HANDLE   = 4;
    /** called from the resource list dtor */
    const PHP_STREAM_FREE_RSRC_DTOR         = 8;
    /** manually freeing a persistent connection */
    const PHP_STREAM_FREE_PERSISTENT        = 16;
    /** don't close the enclosing stream instead */
    const PHP_STREAM_FREE_IGNORE_ENCLOSING  = 32;
    /** keep associated zend_resource */
    const PHP_STREAM_FREE_KEEP_RSRC         = 64;

    const PHP_STREAM_FREE_CLOSE             = (self::PHP_STREAM_FREE_CALL_DTOR | self::PHP_STREAM_FREE_RELEASE_STREAM);
    const PHP_STREAM_FREE_CLOSE_CASTED      = (self::PHP_STREAM_FREE_CLOSE | self::PHP_STREAM_FREE_PRESERVE_HANDLE);
    const PHP_STREAM_FREE_CLOSE_PERSISTENT  = (self::PHP_STREAM_FREE_CLOSE | self::PHP_STREAM_FREE_PERSISTENT);

    const PHP_STREAM_FLAG_NO_SEEK            = 0x1;
    const PHP_STREAM_FLAG_NO_BUFFER          = 0x2;

    const PHP_STREAM_FLAG_EOL_UNIX            = 0x0; /* also includes DOS */
    const PHP_STREAM_FLAG_DETECT_EOL          = 0x4;
    const PHP_STREAM_FLAG_EOL_MAC             = 0x8;

    /** coerce the stream into some other form */
    /** cast as a stdio FILE * */
    const PHP_STREAM_AS_STDIO = 0;
    /** cast as a POSIX fd or socketd */
    const PHP_STREAM_AS_FD = 1;
    /** cast as a socketd */
    const PHP_STREAM_AS_SOCKETD = 2;
    /** cast as fd/socket for select purposes */
    const PHP_STREAM_AS_FD_FOR_SELECT = 3;

    /** try really, really hard to make sure the cast happens (avoid using this flag if possible) */
    const PHP_STREAM_CAST_TRY_HARD = 0x80000000;
    /** stream becomes invalid on success */
    const PHP_STREAM_CAST_RELEASE = 0x40000000;
    /** stream cast for internal use */
    const PHP_STREAM_CAST_INTERNAL = 0x20000000;
    const PHP_STREAM_CAST_MASK = (self::PHP_STREAM_CAST_TRY_HARD | self::PHP_STREAM_CAST_RELEASE | self::PHP_STREAM_CAST_INTERNAL);

    const GC_COLLECTABLE = (1 << 4);
    /** used for recursion detection */
    const GC_PROTECTED = (1 << 5);
    /** can't be changed in place */
    const GC_IMMUTABLE = (1 << 6);
    /** allocated using malloc */
    const GC_PERSISTENT = (1 << 7);
    /** persistent, but thread-local */
    const GC_PERSISTENT_LOCAL = (1 << 8);

    const GC_TYPE_MASK = 0x0000000f;
    const GC_FLAGS_MASK = 0x000003f0;
    const GC_INFO_MASK = 0xfffffc00;
    const GC_FLAGS_SHIFT = 0;
    const GC_INFO_SHIFT = 10;

    const T_YIELD               = 268;
    const T_YIELD_FROM          = 270;
    const T_AWAIT               = self::T_YIELD;

    const ZEND_YIELD            = 160;
    const ZEND_GENERATOR_RETURN = 161;
    const ZEND_GENERATOR_CREATE = 139;
    const ZEND_YIELD_FROM       = 166;
    const ZEND_VM_LAST_OPCODE   = 199;
    const ZEND_AWAIT            = self::ZEND_YIELD;

    const SPEC_START_MASK         = 0x0000ffff;
    const SPEC_EXTRA_MASK         = 0xfffc0000;
    const SPEC_RULE_OP1           = 0x00010000;
    const SPEC_RULE_OP2           = 0x00020000;
    const SPEC_RULE_OP_DATA       = 0x00040000;
    const SPEC_RULE_RETVAL        = 0x00080000;
    const SPEC_RULE_QUICK_ARG     = 0x00100000;
    const SPEC_RULE_SMART_BRANCH  = 0x00200000;
    const SPEC_RULE_COMMUTATIVE   = 0x00800000;
    const SPEC_RULE_ISSET         = 0x01000000;
    const SPEC_RULE_OBSERVER      = 0x02000000;

    const IS_TYPE_REFCOUNTED      = (1 << 0);
    const IS_TYPE_COLLECTABLE     = (1 << 1);
    const Z_TYPE_FLAGS_SHIFT      = 8;

    /** array flags */
    const IS_ARRAY_IMMUTABLE      = self::GC_IMMUTABLE;
    const IS_ARRAY_PERSISTENT     = self::GC_PERSISTENT;

    /** object flags (zval.value->gc.u.flags) */
    const IS_OBJ_WEAKLY_REFERENCED  = self::GC_PERSISTENT;
    const IS_OBJ_DESTRUCTOR_CALLED  = (1 << 8);
    const IS_OBJ_FREE_CALLED        = (1 << 9);

    const HASH_UPDATE          = (1 << 0);
    const HASH_ADD             = (1 << 1);
    const HASH_UPDATE_INDIRECT = (1 << 2);
    const HASH_ADD_NEW         = (1 << 3);
    const HASH_ADD_NEXT        = (1 << 4);

    /** used for casts */
    const _IS_BOOL                = 17;
    const _IS_NUMBER              = 18;

    const Z_TYPE_MASK             = 0xff;
    const Z_TYPE_FLAGS_MASK       = 0xff00;

    /**
     * Type of zend_function.type
     */
    const ZEND_INTERNAL_FUNCTION    = 1;
    const ZEND_USER_FUNCTION        = 2;
    const ZEND_EVAL_CODE            = 4;

    const ZEND_INTERNAL_CLASS       = 1;
    const ZEND_USER_CLASS           = 2;

    /**
     * User opcode handler return values
     */
    /** execute next opcode */
    const ZEND_USER_OPCODE_CONTINUE    = 0;
    /** exit from executor (return from function) */
    const ZEND_USER_OPCODE_RETURN      = 1;
    /** call original opcode handler */
    const ZEND_USER_OPCODE_DISPATCH    = 2;
    /** enter into new op_array without recursion */
    const ZEND_USER_OPCODE_ENTER       = 3;
    /** return to calling op_array within the same executor */
    const ZEND_USER_OPCODE_LEAVE       = 4;
    /** call original handler of returned opcode */
    const ZEND_USER_OPCODE_DISPATCH_TO = 0x100;

    const SUCCESS     = 0;
    const FAILURE     = -1;

    const BOOL        = 'bool';
    const UNDEF       = self::IS_UNDEF;
    const NULL        = self::IS_NULL;
    const FALSE       = self::IS_FALSE;
    const TRUE        = self::IS_TRUE;

    /** Type of the zval. One of the `ZE::IS_*` constants. */
    const TYPE_P        = 'type';
    /** Integer value. */
    const LVAL_P        = 'lval';
    /** Floating-point value. */
    const DVAL_P        = 'dval';
    /** Pointer to full zend_string structure. */
    const STR_P         = 'str';
    /** String contents of the zend_string struct. */
    const STRVAL_P      = 'sval';
    /** String length of the zend_string struct. */
    const STRLEN_P      = 'slen';
    /** Pointer to HashTable structure. */
    const ARR_P         = 'arr';
    /** Alias of Z_ARR. */
    const ARRVAL_P      = 'aval';
    /** Pointer to zend_object structure. */
    const OBJ_P         = 'obj';
    /** Class entry of the object. */
    const OBJCE_P       = 'objce';
    /** Pointer to zend_resource structure. */
    const RES_P         = 'res';
    /** Pointer to zend_reference structure. */
    const REF_P         = 'ref';
    /** Void pointer. */
    const PTR_P         = 'ptr';
    /** Pointer to the zval the reference wraps. */
    const REFVAL_P      = 'rval';
    const TYPE_INFO_P   = 'info';
    /** Pointer a reference count, tracks how many places a structure is used */
    const COUNTED_P             = 'counted';
    const TYPE_INFO_REFCOUNTED  = 'refcounted';

    /** Regular data types: Must be in sync with zend_variables.c. */
    const IS_UNDEF          = 0;
    const IS_NULL           = 1;
    const IS_FALSE          = 2;
    const IS_TRUE           = 3;
    const IS_LONG           = 4;
    const IS_DOUBLE         = 5;
    const IS_STRING         = 6;
    const IS_ARRAY          = 7;
    const IS_OBJECT         = 8;
    const IS_RESOURCE       = 9;
    const IS_REFERENCE      = 10;
    /** Constant expressions */
    const IS_CONSTANT_AST   = 11;

    /** Fake types used only for type hinting.
     * These are allowed to overlap with the types below. */
    const IS_CALLABLE       = 12;
    const IS_ITERABLE       = 13;
    const IS_VOID           = 14;
    const IS_STATIC         = 15;
    const IS_MIXED          = 16;

    /** internal types */
    const IS_INDIRECT       = 12;
    const IS_PTR            = 13;
    const IS_ALIAS_PTR      = 14;
    const _IS_ERROR         = 15;

    /** string flags (zval.value->gc.u.flags) */
    /** interned string */
    const IS_STR_INTERNED     = self::GC_IMMUTABLE;
    /** allocated using malloc */
    const IS_STR_PERSISTENT   = self::GC_PERSISTENT;
    /** relives request boundary */
    const IS_STR_PERMANENT    = (1 << 8);
    /** valid UTF-8 according to PCRE */
    const IS_STR_VALID_UTF8   = (1 << 9);

    /** extended types */
    const IS_INTERNED_STRING_EX     = self::IS_STRING;
    const IS_REFERENCE_EX           = (self::IS_REFERENCE | (self::IS_TYPE_REFCOUNTED << self::Z_TYPE_FLAGS_SHIFT));
    const IS_RESOURCE_EX            = (self::IS_RESOURCE | (self::IS_TYPE_REFCOUNTED << self::Z_TYPE_FLAGS_SHIFT));
    const IS_STRING_EX              = (self::IS_STRING | (self::IS_TYPE_REFCOUNTED << self::Z_TYPE_FLAGS_SHIFT));
    const IS_ARRAY_EX               = (self::IS_ARRAY | (self::IS_TYPE_REFCOUNTED << self::Z_TYPE_FLAGS_SHIFT)
        | (self::IS_TYPE_COLLECTABLE << self::Z_TYPE_FLAGS_SHIFT));
    const IS_OBJECT_EX              = (self::IS_OBJECT | (self::IS_TYPE_REFCOUNTED << self::Z_TYPE_FLAGS_SHIFT)
        | (self::IS_TYPE_COLLECTABLE << self::Z_TYPE_FLAGS_SHIFT));

    protected ?CData $ze = null;
    protected ?CData $ze_ptr = null;

    protected ?CData $ze_other = null;
    protected ?CData $ze_other_ptr = null;

    protected $isZval = true;

    /**
     * Reversed class constants, containing names by number
     *
     * @var string[]
     */
    private static array $constant_names = [];

    use ZETrait;

    protected function __construct(string $typedef, bool $isZval = true)
    {
        $this->isZval = $isZval;
        if ($this->isZval) {
            //$this->ze_ptr = \ffi_ptr(\ze_ffi()->new($typedef, false));
            $this->ze = \ze_ffi()->new($typedef);
            $this->ze_ptr = \ffi_ptr($this->ze);
        } else {
            $this->ze_other = \ze_ffi()->new($typedef);
            $this->ze_other_ptr = \ffi_ptr($this->ze_other);
        }
    }

    public function __invoke($isZval = true)
    {
        if ($this->isZval && $isZval)
            return $this->ze_ptr;

        return $this->ze_other_ptr;
    }

    public function free(): void
    {
        if (!$this->isZval) {
            if (\is_cdata($this->ze_other_ptr) && !\is_null_ptr($this->ze_other_ptr))
                \FFI::free($this->ze_other_ptr);

            $this->ze_other_ptr = null;
            $this->ze_other = null;
        } else {
            if (\is_cdata($this->ze_ptr) && !\is_null_ptr($this->ze_ptr))
                \FFI::free($this->ze_ptr);

            $this->ze_ptr = null;
            $this->ze = null;
        }

        self::$constant_names = [];
    }

    public function update(CData $ptr, bool $isOther = false): self
    {
        if ($this->isZval && !$isOther) {
            $this->ze_ptr = $ptr;
        } else {
            $this->ze_other_ptr = $ptr;
        }

        return $this;
    }

    /**
     * Returns the type name of code
     *
     * @param int $valueCode Integer value of type
     */
    public static function name(int $valueCode): string
    {
        if (empty(self::$constant_names)) {
            static::$constant_names = \array_flip((new \ReflectionClass(static::class))->getConstants());
        }

        // We should use only low byte to get the name of constant
        $valueCode &= 0xFF;
        if (!isset(static::$constant_names[$valueCode])) {
            return \ze_ffi()->zend_error(\E_WARNING, 'Unknown code %s', $valueCode);
        }

        return static::$constant_names[$valueCode];
    }
}
