#define FFI_LIB "C:\\Windows\\System32\\msvcrt.dll"

typedef unsigned short wchar_t;
typedef int BOOL;
typedef unsigned long DWORD;
typedef void *PVOID;
typedef PVOID HANDLE;
typedef DWORD *LPDWORD;
typedef unsigned short WORD;
typedef wchar_t WCHAR;
typedef short SHORT;
typedef unsigned int UINT;
typedef char CHAR;

typedef int errno_t;
typedef unsigned short wint_t;
typedef unsigned short wctype_t;
typedef signed long int __int64;
typedef long __time32_t;
typedef __int64 __time64_t;
typedef long int intptr_t;
typedef intptr_t ssize_t;
typedef long int __off_t;
typedef long int __off64_t;
typedef int __pid_t;
typedef long int __ssize_t;

struct _IO_marker;
struct _IO_codecvt;
struct _IO_wide_data;
typedef void _IO_lock_t;

struct _IO_FILE
{
  int _flags;
  char *_IO_read_ptr;
  char *_IO_read_end;
  char *_IO_read_base;
  char *_IO_write_base;
  char *_IO_write_ptr;
  char *_IO_write_end;
  char *_IO_buf_base;
  char *_IO_buf_end;
  char *_IO_save_base;
  char *_IO_backup_base;
  char *_IO_save_end;
  struct _IO_marker *_markers;
  struct _IO_FILE *_chain;
  int _fileno;
  int _flags2;
  __off_t _old_offset;
  unsigned short _cur_column;
  signed char _vtable_offset;
  char _shortbuf[1];
  _IO_lock_t *_lock;
  __off64_t _offset;
  struct _IO_codecvt *_codecvt;
  struct _IO_wide_data *_wide_data;
  struct _IO_FILE *_freeres_list;
  void *_freeres_buf;
  size_t __pad5;
  int _mode;
  char _unused2[15 * sizeof (int) - 4 * sizeof (void *) - sizeof (size_t)];
};

typedef struct _IO_FILE FILE;

typedef struct
{
  int __count;
  union
  {
    unsigned int __wch;
    char __wchb[4];
  } __value;
} __mbstate_t;

typedef struct _G_fpos_t
{
  __off_t __pos;
  __mbstate_t __state;
} __fpos_t;

typedef __off_t off_t;
typedef __ssize_t ssize_t;
typedef __fpos_t fpos_t;


int _dup(int fd );
int _dup2(int fd1, int fd2 );
int _write(int fd, const void *buffer, unsigned int count);
int _read(int const fd, void * const buffer, unsigned const buffer_size);
int _close(int _FileHandle);
int _commit(int _FileHandle);
int _eof(int _FileHandle);
long _filelength(int _FileHandle);
int _isatty(int _FileHandle);

intptr_t _get_osfhandle(int _FileHandle);
int _open_osfhandle(intptr_t _OSFileHandle, int _Flags);

int _fileno(FILE *stream);
FILE *fopen(const char *filename, const char *mode);
FILE* _fdopen(int _FileHandle, char const* _Mode);
int fclose(FILE* _Stream);
errno_t fopen_s(FILE** _Stream, char const* _FileName, char const* _Mode);
errno_t freopen_s(FILE** _Stream, char const* _FileName, char const* _Mode, FILE* _OldStream);
void clearerr(FILE* _Stream);
int fflush(FILE* _Stream);
