# uv-ffi

 An [Foreign function interface](https://en.wikipedia.org/wiki/Foreign_function_interface) ([FFI](https://github.com/libffi/libffi)) for PHP of **[libuv](http://docs.libuv.org/en/v1.x/)** cross-platform event-driven _asynchronous_ I/O library.

This **libuv ffi** implementation is based on extension [ext-uv](https://github.com/amphp/ext-uv).

The _ext-uv_ extension is on _1.6_ version of **libuv** at _v1.44.1_, the 1.6 is actually _1.06_ could be _0.1.6_, _38_ releases behind or the unthinkable.
The implementation progress will start by getting compatibility around unreleased **ext-uv 0.30** version, current version is _0.24beta_.

Getting _ext-uv_ tests implemented will indicate overall progress, currently 4 out of 53. only a few things libuv functions been actually implemented. **PR** are welcome to speed things up.

## Installation

There will be two ways:
    composer create-project symplely/uv-ffi .cdef "0.0.0"
and:
    composer require symplely/uv-ffi

This repo has **GitHub Actions** building each hardware platforms libuv binary `.dylib` `.dll` `.so`, and committing to repo.
The `require` Installations will include all, `create-project` will delete the ones not detected for installers hardware.

## Error handling

Initialization functions `*_init()` or synchronous functions which may fail will return a negative number on error.
This **ffi** version will return `null` on failure for `*_init()` functions.

Async functions that may fail will pass a status parameter to their callbacks. The error messages are defined as `UV::UV_E*` constants.

You can use the `uv_strerror(int)` and `uv_err_name(int)` functions to get a `string` describing the error or the error name respectively.

I/O read callbacks (such as for files and sockets) are passed a parameter `nread`. If `nread` is less than 0, there was an error (`UV::UV_EOF` is the end of file error, which you may want to handle differently).

## Documentation

All `functions/methods/classes` have there original **Libuv** _documentation_, _signatures_ embedded in DOC-BLOCKS.

For deeper usage understanding, see the online [book](https://nikhilm.github.io/uvbook/index.html) for a full tutorial overview.

## Reference/Credits

- [Introduction to PHP FFI](https://dev.to/verkkokauppacom/introduction-to-php-ffi-po3)
- [How to Use PHP FFI in Programming](https://spiralscout.com/blog/how-to-use-php-ffi-in-programming)
- [Getting Started with PHP-FFI](https://www.youtube.com/watch?v=7pfjvRupoqg) **Youtube**
- [Awesome PHP FFI](https://github.com/gabrielrcouto/awesome-php-ffi)

## Contributing

Contributions are encouraged and welcome; I am always happy to get feedback or pull requests on Github :) Create [Github Issues](https://github.com/symplely/uv-ffi/issues) for bugs and new features and comment on the ones you are interested in.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
