// This is a microsoft specific type, here is its definition for gcc
// https://github.com/Alexpux/mingw-w64/blob/d0d7f784833bbb0b2d279310ddc6afb52fe47a46/mingw-w64-headers/crt/time.h#L36
typedef unsigned short wchar_t;

// Source for data correpsondance
// https://docs.microsoft.com/en-us/windows/win32/winprog/windows-data-types

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

typedef struct _COORD {
  SHORT X;
  SHORT Y;
} COORD, *PCOORD;

typedef struct _WINDOW_BUFFER_SIZE_RECORD {
  COORD dwSize;
} WINDOW_BUFFER_SIZE_RECORD;

typedef struct _MENU_EVENT_RECORD {
  UINT dwCommandId;
} MENU_EVENT_RECORD, *PMENU_EVENT_RECORD;

typedef struct _KEY_EVENT_RECORD {
  BOOL  bKeyDown;
  WORD  wRepeatCount;
  WORD  wVirtualKeyCode;
  WORD  wVirtualScanCode;
  union {
    WCHAR UnicodeChar;
    CHAR  AsciiChar;
  } uChar;
  DWORD dwControlKeyState;
} KEY_EVENT_RECORD;

typedef struct _MOUSE_EVENT_RECORD {
  COORD dwMousePosition;
  DWORD dwButtonState;
  DWORD dwControlKeyState;
  DWORD dwEventFlags;
} MOUSE_EVENT_RECORD;

typedef struct _FOCUS_EVENT_RECORD {
  BOOL bSetFocus;
} FOCUS_EVENT_RECORD;

typedef struct _INPUT_RECORD {
  WORD  EventType;
  union {
    KEY_EVENT_RECORD          KeyEvent;
    MOUSE_EVENT_RECORD        MouseEvent;
    WINDOW_BUFFER_SIZE_RECORD WindowBufferSizeEvent;
    MENU_EVENT_RECORD         MenuEvent;
    FOCUS_EVENT_RECORD        FocusEvent;
  } Event;
} INPUT_RECORD;
typedef INPUT_RECORD *PINPUT_RECORD;

// Original definition is
// WINBASEAPI HANDLE WINAPI GetStdHandle (DWORD nStdHandle);
// https://github.com/Alexpux/mingw-w64/blob/master/mingw-w64-headers/include/processenv.h#L31
HANDLE GetStdHandle(DWORD nStdHandle);

// https://docs.microsoft.com/fr-fr/windows/console/getconsolemode
BOOL GetConsoleMode(
	/* _In_ */HANDLE  hConsoleHandle,
	/* _Out_ */ LPDWORD lpMode
);

// https://docs.microsoft.com/fr-fr/windows/console/setconsolemode
BOOL SetConsoleMode(
  /* _In_ */ HANDLE hConsoleHandle,
  /* _In_ */ DWORD  dwMode
);

// https://docs.microsoft.com/fr-fr/windows/console/getnumberofconsoleinputevents
BOOL GetNumberOfConsoleInputEvents(
  /* _In_ */  HANDLE  hConsoleInput,
  /* _Out_ */ LPDWORD lpcNumberOfEvents
);

// https://docs.microsoft.com/fr-fr/windows/console/readconsoleinput
BOOL ReadConsoleInputA(
  /* _In_ */  HANDLE        hConsoleInput,
  /* _Out_ */ PINPUT_RECORD lpBuffer,
  /* _In_ */  DWORD         nLength,
  /* _Out_ */ LPDWORD       lpNumberOfEventsRead
);
BOOL ReadConsoleInputW(
  /* _In_ */  HANDLE        hConsoleInput,
  /* _Out_ */ PINPUT_RECORD lpBuffer,
  /* _In_ */  DWORD         nLength,
  /* _Out_ */ LPDWORD       lpNumberOfEventsRead
);

BOOL CloseHandle(HANDLE hObject);

typedef intptr_t ssize_t;
  typedef BOOL (PASCAL *LPFN_ACCEPTEX)
                      (SOCKET sListenSocket,
                       SOCKET sAcceptSocket,
                       PVOID lpOutputBuffer,
                       DWORD dwReceiveDataLength,
                       DWORD dwLocalAddressLength,
                       DWORD dwRemoteAddressLength,
                       LPDWORD lpdwBytesReceived,
                       LPOVERLAPPED lpOverlapped);

  typedef BOOL (PASCAL *LPFN_CONNECTEX)
                      (SOCKET s,
                       const struct sockaddr* name,
                       int namelen,
                       PVOID lpSendBuffer,
                       DWORD dwSendDataLength,
                       LPDWORD lpdwBytesSent,
                       LPOVERLAPPED lpOverlapped);

  typedef void (PASCAL *LPFN_GETACCEPTEXSOCKADDRS)
                      (PVOID lpOutputBuffer,
                       DWORD dwReceiveDataLength,
                       DWORD dwLocalAddressLength,
                       DWORD dwRemoteAddressLength,
                       LPSOCKADDR* LocalSockaddr,
                       LPINT LocalSockaddrLength,
                       LPSOCKADDR* RemoteSockaddr,
                       LPINT RemoteSockaddrLength);

  typedef BOOL (PASCAL *LPFN_DISCONNECTEX)
                      (SOCKET hSocket,
                       LPOVERLAPPED lpOverlapped,
                       DWORD dwFlags,
                       DWORD reserved);

  typedef BOOL (PASCAL *LPFN_TRANSMITFILE)
                      (SOCKET hSocket,
                       HANDLE hFile,
                       DWORD nNumberOfBytesToWrite,
                       DWORD nNumberOfBytesPerSend,
                       LPOVERLAPPED lpOverlapped,
                       LPTRANSMIT_FILE_BUFFERS lpTransmitBuffers,
                       DWORD dwFlags);

  typedef PVOID RTL_SRWLOCK;
  typedef RTL_SRWLOCK SRWLOCK, *PSRWLOCK;


typedef int (WSAAPI* LPFN_WSARECV)
            (SOCKET socket,
             LPWSABUF buffers,
             DWORD buffer_count,
             LPDWORD bytes,
             LPDWORD flags,
             LPWSAOVERLAPPED overlapped,
             LPWSAOVERLAPPED_COMPLETION_ROUTINE completion_routine);

typedef int (WSAAPI* LPFN_WSARECVFROM)
            (SOCKET socket,
             LPWSABUF buffers,
             DWORD buffer_count,
             LPDWORD bytes,
             LPDWORD flags,
             struct sockaddr* addr,
             LPINT addr_len,
             LPWSAOVERLAPPED overlapped,
             LPWSAOVERLAPPED_COMPLETION_ROUTINE completion_routine);


  typedef LONG NTSTATUS;
  typedef NTSTATUS *PNTSTATUS;



  typedef PVOID CONDITION_VARIABLE, *PCONDITION_VARIABLE;


typedef struct _AFD_POLL_HANDLE_INFO {
  HANDLE Handle;
  ULONG Events;
  NTSTATUS Status;
} AFD_POLL_HANDLE_INFO, *PAFD_POLL_HANDLE_INFO;

typedef struct _AFD_POLL_INFO {
  LARGE_INTEGER Timeout;
  ULONG NumberOfHandles;
  ULONG Exclusive;
  AFD_POLL_HANDLE_INFO Handles[1];
} AFD_POLL_INFO, *PAFD_POLL_INFO;
typedef struct uv_buf_t {
  ULONG len;
  char* base;
} uv_buf_t;

typedef int uv_file;
typedef SOCKET uv_os_sock_t;
typedef HANDLE uv_os_fd_t;
typedef int uv_pid_t;

typedef HANDLE uv_thread_t;

typedef HANDLE uv_sem_t;

typedef CRITICAL_SECTION uv_mutex_t;
typedef union {
  CONDITION_VARIABLE cond_var;
  struct {
    unsigned int waiters_count;
    CRITICAL_SECTION waiters_count_lock;
    HANDLE signal_event;
    HANDLE broadcast_event;
  } unused_;
} uv_cond_t;

typedef struct {
  SRWLOCK read_write_lock_;




  unsigned char padding_[44];

} uv_rwlock_t;

typedef struct {
  unsigned int n;
  unsigned int count;
  uv_mutex_t mutex;
  uv_sem_t turnstile1;
  uv_sem_t turnstile2;
} uv_barrier_t;

typedef struct {
  DWORD tls_index;
} uv_key_t;



typedef struct uv_once_s {
  unsigned char ran;
  HANDLE event;
} uv_once_t;


typedef unsigned char uv_uid_t;
typedef unsigned char uv_gid_t;

typedef struct uv__dirent_s {
  int d_type;
  char d_name[1];
} uv__dirent_t;
typedef struct {
  HMODULE handle;
  char* errmsg;
} uv_lib_t;
