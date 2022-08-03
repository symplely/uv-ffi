<?php

interface FFI
{
    /** @return int */
    public function _dup(int $fd);

    /** @return int */
    public function _dup2(int $fd1, int $fd2);

    /** @return intptr_t */
    public function _get_osfhandle(int $_FileHandle);

    /** @return int */
    public function _write(int $fd, const_char &$buffer, int $count);

    /** @return int */
    public function _read(int $fd, const_char &$buffer, int $buffer_size);

    /** @return int */
    public function _close(int $_FileHandle);

    /** @return int */
    public function _commit(int $_FileHandle);

    /** @return int */
    public function _eof(int $_FileHandle);

    /** @return long */
    public function _filelength(int $_FileHandle);

    /** @return int */
    public function _isatty(int $_FileHandle);

    /** @return int */
    public function _open_osfhandle(intptr_t $_OSFileHandle, int $_Flags);

    /** @return int */
    public function _fileno(FILE &$stream);

    /** @return FILE */
    public function fopen(const_char &$filename, const_char &$mode);

    /** @return FILE */
    public function _fdopen(int $_FileHandle, const_char &$_Mode);

    /** @return int */
    public function fclose(FILE &$_Stream);

    /** @return errno_t */
    public function fopen_s(FILE &$_Stream, const_char &$_FileName, const_char &$_Mode);

    /** @return errno_t */
    public function freopen_s(FILE &$_Stream, const_char &$_FileName, const_char &$_Mode, FILE &$_OldStream);

    /** @return void */
    public function clearerr(FILE &$_Stream);

    /** @return int */
    public function fflush(FILE &$_Stream);
}
