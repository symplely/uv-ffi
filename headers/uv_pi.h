#define FFI_SCOPE "__uv__"
#define FFI_LIB "./lib/Linux/raspberry/libuv.so.1.0.0"

typedef long int ptrdiff_t;
typedef long unsigned int size_t;
typedef struct
{
  long long __max_align_ll;
  long double __max_align_ld;
} max_align_t;

typedef unsigned char __u_char;
typedef unsigned short int __u_short;
typedef unsigned int __u_int;
typedef unsigned long int __u_long;
typedef signed char __int8_t;
typedef unsigned char __uint8_t;
typedef signed short int __int16_t;
typedef unsigned short int __uint16_t;
typedef signed int __int32_t;
typedef unsigned int __uint32_t;
typedef signed long int __int64_t;
typedef unsigned long int __uint64_t;
typedef __int8_t __int_least8_t;
typedef __uint8_t __uint_least8_t;
typedef __int16_t __int_least16_t;
typedef __uint16_t __uint_least16_t;
typedef __int32_t __int_least32_t;
typedef __uint32_t __uint_least32_t;
typedef __int64_t __int_least64_t;
typedef __uint64_t __uint_least64_t;
typedef long int __quad_t;
typedef unsigned long int __u_quad_t;
typedef long int __intmax_t;
typedef unsigned long int __uintmax_t;
typedef unsigned long int __dev_t;
typedef unsigned int __uid_t;
typedef unsigned int __gid_t;
typedef unsigned long int __ino_t;
typedef unsigned long int __ino64_t;
typedef unsigned int __mode_t;
typedef unsigned long int __nlink_t;
typedef long int __off_t;
typedef long int __off64_t;
typedef int __pid_t;
typedef struct
{
  int __val[2];
} __fsid_t;
typedef long int __clock_t;
typedef unsigned long int __rlim_t;
typedef unsigned long int __rlim64_t;
typedef unsigned int __id_t;
typedef long int __time_t;
typedef unsigned int __useconds_t;
typedef long int __suseconds_t;
typedef int __daddr_t;
typedef int __key_t;
typedef int __clockid_t;
typedef void *__timer_t;
typedef long int __blksize_t;
typedef long int __blkcnt_t;
typedef long int __blkcnt64_t;
typedef unsigned long int __fsblkcnt_t;
typedef unsigned long int __fsblkcnt64_t;
typedef unsigned long int __fsfilcnt_t;
typedef unsigned long int __fsfilcnt64_t;
typedef long int __fsword_t;
typedef long int __ssize_t;
typedef long int __syscall_slong_t;
typedef unsigned long int __syscall_ulong_t;
typedef __off64_t __loff_t;
typedef char *__caddr_t;
typedef long int __intptr_t;
typedef unsigned int __socklen_t;
typedef int __sig_atomic_t;
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
typedef struct _G_fpos64_t
{
  __off64_t __pos;
  __mbstate_t __state;
} __fpos64_t;
struct _IO_FILE;
typedef struct _IO_FILE __FILE;
struct _IO_FILE;
typedef struct _IO_FILE FILE;
struct _IO_FILE;
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
  char _unused2[15 * sizeof(int) - 4 * sizeof(void *) - sizeof(size_t)];
};
typedef __off_t off_t;
typedef __ssize_t ssize_t;
typedef __fpos_t fpos_t;

typedef __int8_t int8_t;
typedef __int16_t int16_t;
typedef __int32_t int32_t;
typedef __int64_t int64_t;
typedef __uint8_t uint8_t;
typedef __uint16_t uint16_t;
typedef __uint32_t uint32_t;
typedef __uint64_t uint64_t;
typedef __int_least8_t int_least8_t;
typedef __int_least16_t int_least16_t;
typedef __int_least32_t int_least32_t;
typedef __int_least64_t int_least64_t;
typedef __uint_least8_t uint_least8_t;
typedef __uint_least16_t uint_least16_t;
typedef __uint_least32_t uint_least32_t;
typedef __uint_least64_t uint_least64_t;
typedef signed char int_fast8_t;
typedef long int int_fast16_t;
typedef long int int_fast32_t;
typedef long int int_fast64_t;
typedef unsigned char uint_fast8_t;
typedef unsigned long int uint_fast16_t;
typedef unsigned long int uint_fast32_t;
typedef unsigned long int uint_fast64_t;
typedef long int intptr_t;
typedef unsigned long int uintptr_t;
typedef __intmax_t intmax_t;
typedef __uintmax_t uintmax_t;

typedef __u_char u_char;
typedef __u_short u_short;
typedef __u_int u_int;
typedef __u_long u_long;
typedef __quad_t quad_t;
typedef __u_quad_t u_quad_t;
typedef __fsid_t fsid_t;
typedef __loff_t loff_t;
typedef __ino_t ino_t;
typedef __dev_t dev_t;
typedef __gid_t gid_t;
typedef __mode_t mode_t;
typedef __nlink_t nlink_t;
typedef __uid_t uid_t;
typedef __pid_t pid_t;
typedef __id_t id_t;
typedef __daddr_t daddr_t;
typedef __caddr_t caddr_t;
typedef __key_t key_t;
typedef __clock_t clock_t;
typedef __clockid_t clockid_t;
typedef __time_t time_t;
typedef __timer_t timer_t;
typedef unsigned long int ulong;
typedef unsigned short int ushort;
typedef unsigned int uint;
typedef __uint8_t u_int8_t;
typedef __uint16_t u_int16_t;
typedef __uint32_t u_int32_t;
typedef __uint64_t u_int64_t;
typedef int register_t;
typedef struct
{
  unsigned long int __val[(1024 / (8 * sizeof(unsigned long int)))];
} __sigset_t;
typedef __sigset_t sigset_t;
struct timeval
{
  __time_t tv_sec;
  __suseconds_t tv_usec;
};
struct timespec
{
  __time_t tv_sec;
  __syscall_slong_t tv_nsec;
};
typedef __suseconds_t suseconds_t;
typedef long int __fd_mask;
typedef struct
{
  __fd_mask __fds_bits[1024 / (8 * (int)sizeof(__fd_mask))];
} fd_set;
typedef __fd_mask fd_mask;

typedef __blksize_t blksize_t;
typedef __blkcnt_t blkcnt_t;
typedef __fsblkcnt_t fsblkcnt_t;
typedef __fsfilcnt_t fsfilcnt_t;
typedef struct __pthread_internal_list
{
  struct __pthread_internal_list *__prev;
  struct __pthread_internal_list *__next;
} __pthread_list_t;
typedef struct __pthread_internal_slist
{
  struct __pthread_internal_slist *__next;
} __pthread_slist_t;
struct __pthread_mutex_s
{
  int __lock;
  unsigned int __count;
  int __owner;
  unsigned int __nusers;
  int __kind;
  short __spins;
  short __elision;
  __pthread_list_t __list;
};
struct __pthread_rwlock_arch_t
{
  unsigned int __readers;
  unsigned int __writers;
  unsigned int __wrphase_futex;
  unsigned int __writers_futex;
  unsigned int __pad3;
  unsigned int __pad4;
  int __cur_writer;
  int __shared;
  signed char __rwelision;
  unsigned char __pad1[7];
  unsigned long int __pad2;
  unsigned int __flags;
};
struct __pthread_cond_s
{
  __extension__ union
  {
    __extension__ unsigned long long int __wseq;
    struct
    {
      unsigned int __low;
      unsigned int __high;
    } __wseq32;
  };
  __extension__ union
  {
    __extension__ unsigned long long int __g1_start;
    struct
    {
      unsigned int __low;
      unsigned int __high;
    } __g1_start32;
  };
  unsigned int __g_refs[2];
  unsigned int __g_size[2];
  unsigned int __g1_orig_size;
  unsigned int __wrefs;
  unsigned int __g_signals[2];
};
typedef unsigned long int pthread_t;
typedef union
{
  char __size[4];
  int __align;
} pthread_mutexattr_t;
typedef union
{
  char __size[4];
  int __align;
} pthread_condattr_t;
union pthread_attr_t
{
  char __size[56];
  long int __align;
};
typedef union pthread_attr_t pthread_attr_t;
typedef union
{
  struct __pthread_mutex_s __data;
  char __size[40];
  long int __align;
} pthread_mutex_t;
typedef union
{
  struct __pthread_cond_s __data;
  char __size[48];
  __extension__ long long int __align;
} pthread_cond_t;
typedef union
{
  struct __pthread_rwlock_arch_t __data;
  char __size[56];
  long int __align;
} pthread_rwlock_t;
typedef union
{
  char __size[8];
  long int __align;
} pthread_rwlockattr_t;
typedef volatile int pthread_spinlock_t;
typedef union
{
  char __size[32];
  long int __align;
} pthread_barrier_t;
typedef union
{
  char __size[4];
  int __align;
} pthread_barrierattr_t;

struct stat
{
  __dev_t st_dev;
  __ino_t st_ino;
  __nlink_t st_nlink;
  __mode_t st_mode;
  __uid_t st_uid;
  __gid_t st_gid;
  int __pad0;
  __dev_t st_rdev;
  __off_t st_size;
  __blksize_t st_blksize;
  __blkcnt_t st_blocks;
  struct timespec st_atim;
  struct timespec st_mtim;
  struct timespec st_ctim;
  __syscall_slong_t __glibc_reserved[3];
};
struct flock
{
  short int l_type;
  short int l_whence;
  __off_t l_start;
  __off_t l_len;
  __pid_t l_pid;
};

struct dirent
{
  __ino_t d_ino;
  __off_t d_off;
  unsigned short int d_reclen;
  unsigned char d_type;
  char d_name[256];
};

typedef struct dirent uv__dirent_t;
enum
{
  DT_UNKNOWN = 0,
  DT_FIFO = 1,
  DT_CHR = 2,
  DT_DIR = 4,
  DT_BLK = 6,
  DT_REG = 8,
  DT_LNK = 10,
  DT_SOCK = 12,
  DT_WHT = 14
};
typedef struct __dirstream DIR;

struct iovec
{
  void *iov_base;
  size_t iov_len;
};
typedef __socklen_t socklen_t;
enum __socket_type
{
  SOCK_STREAM = 1,
  SOCK_DGRAM = 2,
  SOCK_RAW = 3,
  SOCK_RDM = 4,
  SOCK_SEQPACKET = 5,
  SOCK_DCCP = 6,
  SOCK_PACKET = 10,
  SOCK_CLOEXEC = 02000000,
  SOCK_NONBLOCK = 00004000
};
typedef unsigned short int sa_family_t;
struct sockaddr
{
  sa_family_t sa_family;
  char sa_data[14];
};
struct sockaddr_storage
{
  sa_family_t ss_family;
  char __ss_padding[(128 - (sizeof(unsigned short int)) - sizeof(unsigned long int))];
  unsigned long int __ss_align;
};
enum
{
  MSG_OOB = 0x01,
  MSG_PEEK = 0x02,
  MSG_DONTROUTE = 0x04,
  MSG_CTRUNC = 0x08,
  MSG_PROXY = 0x10,
  MSG_TRUNC = 0x20,
  MSG_DONTWAIT = 0x40,
  MSG_EOR = 0x80,
  MSG_WAITALL = 0x100,
  MSG_FIN = 0x200,
  MSG_SYN = 0x400,
  MSG_CONFIRM = 0x800,
  MSG_RST = 0x1000,
  MSG_ERRQUEUE = 0x2000,
  MSG_NOSIGNAL = 0x4000,
  MSG_MORE = 0x8000,
  MSG_WAITFORONE = 0x10000,
  MSG_BATCH = 0x40000,
  MSG_ZEROCOPY = 0x4000000,
  MSG_FASTOPEN = 0x20000000,
  MSG_CMSG_CLOEXEC = 0x40000000
};
struct msghdr
{
  void *msg_name;
  socklen_t msg_namelen;
  struct iovec *msg_iov;
  size_t msg_iovlen;
  void *msg_control;
  size_t msg_controllen;
  int msg_flags;
};

struct cmsghdr
{
  size_t cmsg_len;
  int cmsg_level;
  int cmsg_type;
  __extension__ unsigned char __cmsg_data[];
};

enum
{
  SCM_RIGHTS = 0x01
};
typedef struct
{
  unsigned long fds_bits[1024 / (8 * sizeof(long))];
} __kernel_fd_set;
typedef void (*__kernel_sighandler_t)(int);
typedef int __kernel_key_t;
typedef int __kernel_mqd_t;
typedef unsigned short __kernel_old_uid_t;
typedef unsigned short __kernel_old_gid_t;
typedef unsigned long __kernel_old_dev_t;
typedef long __kernel_long_t;
typedef unsigned long __kernel_ulong_t;
typedef __kernel_ulong_t __kernel_ino_t;
typedef unsigned int __kernel_mode_t;
typedef int __kernel_pid_t;
typedef int __kernel_ipc_pid_t;
typedef unsigned int __kernel_uid_t;
typedef unsigned int __kernel_gid_t;
typedef __kernel_long_t __kernel_suseconds_t;
typedef int __kernel_daddr_t;
typedef unsigned int __kernel_uid32_t;
typedef unsigned int __kernel_gid32_t;
typedef __kernel_ulong_t __kernel_size_t;
typedef __kernel_long_t __kernel_ssize_t;
typedef __kernel_long_t __kernel_ptrdiff_t;
typedef struct
{
  int val[2];
} __kernel_fsid_t;
typedef __kernel_long_t __kernel_off_t;
typedef long long __kernel_loff_t;
typedef __kernel_long_t __kernel_time_t;
typedef long long __kernel_time64_t;
typedef __kernel_long_t __kernel_clock_t;
typedef int __kernel_timer_t;
typedef int __kernel_clockid_t;
typedef char *__kernel_caddr_t;
typedef unsigned short __kernel_uid16_t;
typedef unsigned short __kernel_gid16_t;
struct linger
{
  int l_onoff;
  int l_linger;
};
struct osockaddr
{
  unsigned short int sa_family;
  unsigned char sa_data[14];
};
enum
{
  SHUT_RD = 0,
  SHUT_WR,
  SHUT_RDWR
};

typedef uint32_t in_addr_t;
struct in_addr
{
  in_addr_t s_addr;
};
struct ip_opts
{
  struct in_addr ip_dst;
  char ip_opts[40];
};
struct ip_mreqn
{
  struct in_addr imr_multiaddr;
  struct in_addr imr_address;
  int imr_ifindex;
};
struct in_pktinfo
{
  int ipi_ifindex;
  struct in_addr ipi_spec_dst;
  struct in_addr ipi_addr;
};
enum
{
  IPPROTO_IP = 0,
  IPPROTO_ICMP = 1,
  IPPROTO_IGMP = 2,
  IPPROTO_IPIP = 4,
  IPPROTO_TCP = 6,
  IPPROTO_EGP = 8,
  IPPROTO_PUP = 12,
  IPPROTO_UDP = 17,
  IPPROTO_IDP = 22,
  IPPROTO_TP = 29,
  IPPROTO_DCCP = 33,
  IPPROTO_IPV6 = 41,
  IPPROTO_RSVP = 46,
  IPPROTO_GRE = 47,
  IPPROTO_ESP = 50,
  IPPROTO_AH = 51,
  IPPROTO_MTP = 92,
  IPPROTO_BEETPH = 94,
  IPPROTO_ENCAP = 98,
  IPPROTO_PIM = 103,
  IPPROTO_COMP = 108,
  IPPROTO_SCTP = 132,
  IPPROTO_UDPLITE = 136,
  IPPROTO_MPLS = 137,
  IPPROTO_RAW = 255,
  IPPROTO_MAX
};
enum
{
  IPPROTO_HOPOPTS = 0,
  IPPROTO_ROUTING = 43,
  IPPROTO_FRAGMENT = 44,
  IPPROTO_ICMPV6 = 58,
  IPPROTO_NONE = 59,
  IPPROTO_DSTOPTS = 60,
  IPPROTO_MH = 135
};
typedef uint16_t in_port_t;
enum
{
  IPPORT_ECHO = 7,
  IPPORT_DISCARD = 9,
  IPPORT_SYSTAT = 11,
  IPPORT_DAYTIME = 13,
  IPPORT_NETSTAT = 15,
  IPPORT_FTP = 21,
  IPPORT_TELNET = 23,
  IPPORT_SMTP = 25,
  IPPORT_TIMESERVER = 37,
  IPPORT_NAMESERVER = 42,
  IPPORT_WHOIS = 43,
  IPPORT_MTP = 57,
  IPPORT_TFTP = 69,
  IPPORT_RJE = 77,
  IPPORT_FINGER = 79,
  IPPORT_TTYLINK = 87,
  IPPORT_SUPDUP = 95,
  IPPORT_EXECSERVER = 512,
  IPPORT_LOGINSERVER = 513,
  IPPORT_CMDSERVER = 514,
  IPPORT_EFSSERVER = 520,
  IPPORT_BIFFUDP = 512,
  IPPORT_WHOSERVER = 513,
  IPPORT_ROUTESERVER = 520,
  IPPORT_RESERVED = 1024,
  IPPORT_USERRESERVED = 5000
};
struct in6_addr
{
  union
  {
    uint8_t __u6_addr8[16];
    uint16_t __u6_addr16[8];
    uint32_t __u6_addr32[4];
  } __in6_u;
};
struct sockaddr_in
{
  sa_family_t sin_family;
  in_port_t sin_port;
  struct in_addr sin_addr;
  unsigned char sin_zero[sizeof(struct sockaddr) - (sizeof(unsigned short int)) - sizeof(in_port_t) - sizeof(struct in_addr)];
};
struct sockaddr_in6
{
  sa_family_t sin6_family;
  in_port_t sin6_port;
  uint32_t sin6_flowinfo;
  struct in6_addr sin6_addr;
  uint32_t sin6_scope_id;
};
struct ip_mreq
{
  struct in_addr imr_multiaddr;
  struct in_addr imr_interface;
};
struct ip_mreq_source
{
  struct in_addr imr_multiaddr;
  struct in_addr imr_interface;
  struct in_addr imr_sourceaddr;
};
struct ipv6_mreq
{
  struct in6_addr ipv6mr_multiaddr;
  unsigned int ipv6mr_interface;
};
struct group_req
{
  uint32_t gr_interface;
  struct sockaddr_storage gr_group;
};
struct group_source_req
{
  uint32_t gsr_interface;
  struct sockaddr_storage gsr_group;
  struct sockaddr_storage gsr_source;
};
struct ip_msfilter
{
  struct in_addr imsf_multiaddr;
  struct in_addr imsf_interface;
  uint32_t imsf_fmode;
  uint32_t imsf_numsrc;
  struct in_addr imsf_slist[1];
};
struct group_filter
{
  uint32_t gf_interface;
  struct sockaddr_storage gf_group;
  uint32_t gf_fmode;
  uint32_t gf_numsrc;
  struct sockaddr_storage gf_slist[1];
};

typedef uint32_t tcp_seq;
struct tcphdr
{
  __extension__ union
  {
    struct
    {
      uint16_t th_sport;
      uint16_t th_dport;
      tcp_seq th_seq;
      tcp_seq th_ack;
      uint8_t th_x2 : 4;
      uint8_t th_off : 4;
      uint8_t th_flags;
      uint16_t th_win;
      uint16_t th_sum;
      uint16_t th_urp;
    };
    struct
    {
      uint16_t source;
      uint16_t dest;
      uint32_t seq;
      uint32_t ack_seq;
      uint16_t res1 : 4;
      uint16_t doff : 4;
      uint16_t fin : 1;
      uint16_t syn : 1;
      uint16_t rst : 1;
      uint16_t psh : 1;
      uint16_t ack : 1;
      uint16_t urg : 1;
      uint16_t res2 : 2;
      uint16_t window;
      uint16_t check;
      uint16_t urg_ptr;
    };
  };
};
enum
{
  TCP_ESTABLISHED = 1,
  TCP_SYN_SENT,
  TCP_SYN_RECV,
  TCP_FIN_WAIT1,
  TCP_FIN_WAIT2,
  TCP_TIME_WAIT,
  TCP_CLOSE,
  TCP_CLOSE_WAIT,
  TCP_LAST_ACK,
  TCP_LISTEN,
  TCP_CLOSING
};
enum tcp_ca_state
{
  TCP_CA_Open = 0,
  TCP_CA_Disorder = 1,
  TCP_CA_CWR = 2,
  TCP_CA_Recovery = 3,
  TCP_CA_Loss = 4
};
struct tcp_info
{
  uint8_t tcpi_state;
  uint8_t tcpi_ca_state;
  uint8_t tcpi_retransmits;
  uint8_t tcpi_probes;
  uint8_t tcpi_backoff;
  uint8_t tcpi_options;
  uint8_t tcpi_snd_wscale : 4, tcpi_rcv_wscale : 4;
  uint32_t tcpi_rto;
  uint32_t tcpi_ato;
  uint32_t tcpi_snd_mss;
  uint32_t tcpi_rcv_mss;
  uint32_t tcpi_unacked;
  uint32_t tcpi_sacked;
  uint32_t tcpi_lost;
  uint32_t tcpi_retrans;
  uint32_t tcpi_fackets;
  uint32_t tcpi_last_data_sent;
  uint32_t tcpi_last_ack_sent;
  uint32_t tcpi_last_data_recv;
  uint32_t tcpi_last_ack_recv;
  uint32_t tcpi_pmtu;
  uint32_t tcpi_rcv_ssthresh;
  uint32_t tcpi_rtt;
  uint32_t tcpi_rttvar;
  uint32_t tcpi_snd_ssthresh;
  uint32_t tcpi_snd_cwnd;
  uint32_t tcpi_advmss;
  uint32_t tcpi_reordering;
  uint32_t tcpi_rcv_rtt;
  uint32_t tcpi_rcv_space;
  uint32_t tcpi_total_retrans;
};
struct tcp_md5sig
{
  struct sockaddr_storage tcpm_addr;
  uint8_t tcpm_flags;
  uint8_t tcpm_prefixlen;
  uint16_t tcpm_keylen;
  uint32_t __tcpm_pad;
  uint8_t tcpm_key[80];
};
struct tcp_repair_opt
{
  uint32_t opt_code;
  uint32_t opt_val;
};
enum
{
  TCP_NO_QUEUE,
  TCP_RECV_QUEUE,
  TCP_SEND_QUEUE,
  TCP_QUEUES_NR,
};
struct tcp_cookie_transactions
{
  uint16_t tcpct_flags;
  uint8_t __tcpct_pad1;
  uint8_t tcpct_cookie_desired;
  uint16_t tcpct_s_data_desired;
  uint16_t tcpct_used;
  uint8_t tcpct_value[536U];
};
struct tcp_repair_window
{
  uint32_t snd_wl1;
  uint32_t snd_wnd;
  uint32_t max_window;
  uint32_t rcv_wnd;
  uint32_t rcv_wup;
};
struct tcp_zerocopy_receive
{
  uint64_t address;
  uint32_t length;
  uint32_t recv_skip_hint;
};

struct rpcent
{
  char *r_name;
  char **r_aliases;
  int r_number;
};

struct netent
{
  char *n_name;
  char **n_aliases;
  int n_addrtype;
  uint32_t n_net;
};

struct hostent
{
  char *h_name;
  char **h_aliases;
  int h_addrtype;
  int h_length;
  char **h_addr_list;
};

struct servent
{
  char *s_name;
  char **s_aliases;
  int s_port;
  char *s_proto;
};

struct protoent
{
  char *p_name;
  char **p_aliases;
  int p_proto;
};

struct addrinfo
{
  int ai_flags;
  int ai_family;
  int ai_socktype;
  int ai_protocol;
  socklen_t ai_addrlen;
  struct sockaddr *ai_addr;
  char *ai_canonname;
  struct addrinfo *ai_next;
};

typedef unsigned char cc_t;
typedef unsigned int speed_t;
typedef unsigned int tcflag_t;
struct termios
{
  tcflag_t c_iflag;
  tcflag_t c_oflag;
  tcflag_t c_cflag;
  tcflag_t c_lflag;
  cc_t c_line;
  cc_t c_cc[32];
  speed_t c_ispeed;
  speed_t c_ospeed;
};

struct passwd
{
  char *pw_name;
  char *pw_passwd;
  __uid_t pw_uid;
  __gid_t pw_gid;
  char *pw_gecos;
  char *pw_dir;
  char *pw_shell;
};

typedef union
{
  char __size[32];
  long int __align;
} sem_t;

typedef __sig_atomic_t sig_atomic_t;
union sigval
{
  int sival_int;
  void *sival_ptr;
};
typedef union sigval __sigval_t;
typedef struct
{
  int si_signo;
  int si_errno;
  int si_code;
  int __pad0;
  union
  {
    int _pad[((128 / sizeof(int)) - 4)];
    struct
    {
      __pid_t si_pid;
      __uid_t si_uid;
    } _kill;
    struct
    {
      int si_tid;
      int si_overrun;
      __sigval_t si_sigval;
    } _timer;
    struct
    {
      __pid_t si_pid;
      __uid_t si_uid;
      __sigval_t si_sigval;
    } _rt;
    struct
    {
      __pid_t si_pid;
      __uid_t si_uid;
      int si_status;
      __clock_t si_utime;
      __clock_t si_stime;
    } _sigchld;
    struct
    {
      void *si_addr;

      short int si_addr_lsb;
      union
      {
        struct
        {
          void *_lower;
          void *_upper;
        } _addr_bnd;
        __uint32_t _pkey;
      } _bounds;
    } _sigfault;
    struct
    {
      long int si_band;
      int si_fd;
    } _sigpoll;
    struct
    {
      void *_call_addr;
      int _syscall;
      unsigned int _arch;
    } _sigsys;
  } _sifields;
} siginfo_t;
enum
{
  SI_ASYNCNL = -60,
  SI_DETHREAD = -7,
  SI_TKILL,
  SI_SIGIO,
  SI_ASYNCIO,
  SI_MESGQ,
  SI_TIMER,
  SI_QUEUE,
  SI_USER,
  SI_KERNEL = 0x80
};
enum
{
  ILL_ILLOPC = 1,
  ILL_ILLOPN,
  ILL_ILLADR,
  ILL_ILLTRP,
  ILL_PRVOPC,
  ILL_PRVREG,
  ILL_COPROC,
  ILL_BADSTK,
  ILL_BADIADDR
};
enum
{
  FPE_INTDIV = 1,
  FPE_INTOVF,
  FPE_FLTDIV,
  FPE_FLTOVF,
  FPE_FLTUND,
  FPE_FLTRES,
  FPE_FLTINV,
  FPE_FLTSUB,
  FPE_FLTUNK = 14,
  FPE_CONDTRAP
};
enum
{
  SEGV_MAPERR = 1,
  SEGV_ACCERR,
  SEGV_BNDERR,
  SEGV_PKUERR,
  SEGV_ACCADI,
  SEGV_ADIDERR,
  SEGV_ADIPERR
};
enum
{
  BUS_ADRALN = 1,
  BUS_ADRERR,
  BUS_OBJERR,
  BUS_MCEERR_AR,
  BUS_MCEERR_AO
};
enum
{
  CLD_EXITED = 1,
  CLD_KILLED,
  CLD_DUMPED,
  CLD_TRAPPED,
  CLD_STOPPED,
  CLD_CONTINUED
};
enum
{
  POLL_IN = 1,
  POLL_OUT,
  POLL_MSG,
  POLL_ERR,
  POLL_PRI,
  POLL_HUP
};
typedef __sigval_t sigval_t;
typedef struct sigevent
{
  __sigval_t sigev_value;
  int sigev_signo;
  int sigev_notify;
  union
  {
    int _pad[((64 / sizeof(int)) - 4)];
    __pid_t _tid;
    struct
    {
      void (*_function)(__sigval_t);
      pthread_attr_t *_attribute;
    } _sigev_thread;
  } _sigev_un;
} sigevent_t;
enum
{
  SIGEV_SIGNAL = 0,
  SIGEV_NONE,
  SIGEV_THREAD,
  SIGEV_THREAD_ID = 4
};
typedef void (*__sighandler_t)(int);

struct sigaction
{
  union
  {
    __sighandler_t sa_handler;
    void (*sa_sigaction)(int, siginfo_t *, void *);
  } __sigaction_handler;
  __sigset_t sa_mask;
  int sa_flags;
  void (*sa_restorer)(void);
};

struct _fpx_sw_bytes
{
  __uint32_t magic1;
  __uint32_t extended_size;
  __uint64_t xstate_bv;
  __uint32_t xstate_size;
  __uint32_t __glibc_reserved1[7];
};
struct _fpreg
{
  unsigned short significand[4];
  unsigned short exponent;
};
struct _fpxreg
{
  unsigned short significand[4];
  unsigned short exponent;
  unsigned short __glibc_reserved1[3];
};
struct _xmmreg
{
  __uint32_t element[4];
};
struct _fpstate
{
  __uint16_t cwd;
  __uint16_t swd;
  __uint16_t ftw;
  __uint16_t fop;
  __uint64_t rip;
  __uint64_t rdp;
  __uint32_t mxcsr;
  __uint32_t mxcr_mask;
  struct _fpxreg _st[8];
  struct _xmmreg _xmm[16];
  __uint32_t __glibc_reserved1[24];
};
struct sigcontext
{
  __uint64_t r8;
  __uint64_t r9;
  __uint64_t r10;
  __uint64_t r11;
  __uint64_t r12;
  __uint64_t r13;
  __uint64_t r14;
  __uint64_t r15;
  __uint64_t rdi;
  __uint64_t rsi;
  __uint64_t rbp;
  __uint64_t rbx;
  __uint64_t rdx;
  __uint64_t rax;
  __uint64_t rcx;
  __uint64_t rsp;
  __uint64_t rip;
  __uint64_t eflags;
  unsigned short cs;
  unsigned short gs;
  unsigned short fs;
  unsigned short __pad0;
  __uint64_t err;
  __uint64_t trapno;
  __uint64_t oldmask;
  __uint64_t cr2;
  __extension__ union
  {
    struct _fpstate *fpstate;
    __uint64_t __fpstate_word;
  };
  __uint64_t __reserved1[8];
};
struct _xsave_hdr
{
  __uint64_t xstate_bv;
  __uint64_t __glibc_reserved1[2];
  __uint64_t __glibc_reserved2[5];
};
struct _ymmh_state
{
  __uint32_t ymmh_space[64];
};
struct _xstate
{
  struct _fpstate fpstate;
  struct _xsave_hdr xstate_hdr;
  struct _ymmh_state ymmh;
};

typedef struct
{
  void *ss_sp;
  int ss_flags;
  size_t ss_size;
} stack_t;
__extension__ typedef long long int greg_t;
typedef greg_t gregset_t[23];
struct _libc_fpxreg
{
  unsigned short int significand[4];
  unsigned short int exponent;
  unsigned short int __glibc_reserved1[3];
};
struct _libc_xmmreg
{
  __uint32_t element[4];
};
struct _libc_fpstate
{
  __uint16_t cwd;
  __uint16_t swd;
  __uint16_t ftw;
  __uint16_t fop;
  __uint64_t rip;
  __uint64_t rdp;
  __uint32_t mxcsr;
  __uint32_t mxcr_mask;
  struct _libc_fpxreg _st[8];
  struct _libc_xmmreg _xmm[16];
  __uint32_t __glibc_reserved1[24];
};
typedef struct _libc_fpstate *fpregset_t;
typedef struct
{
  gregset_t gregs;
  fpregset_t fpregs;
  __extension__ unsigned long long __reserved1[8];
} mcontext_t;
typedef struct ucontext_t
{
  unsigned long int uc_flags;
  struct ucontext_t *uc_link;
  stack_t uc_stack;
  mcontext_t uc_mcontext;
  sigset_t uc_sigmask;
  struct _libc_fpstate __fpregs_mem;
  __extension__ unsigned long long int __ssp[4];
} ucontext_t;

enum
{
  SS_ONSTACK = 1,
  SS_DISABLE
};

struct sigstack
{
  void *ss_sp;
  int ss_onstack;
};

struct sched_param
{
  int sched_priority;
};

typedef unsigned long int __cpu_mask;
typedef struct
{
  __cpu_mask __bits[1024 / (8 * sizeof(__cpu_mask))];
} cpu_set_t;

struct tm
{
  int tm_sec;
  int tm_min;
  int tm_hour;
  int tm_mday;
  int tm_mon;
  int tm_year;
  int tm_wday;
  int tm_yday;
  int tm_isdst;
  long int tm_gmtoff;
  const char *tm_zone;
};
struct itimerspec
{
  struct timespec it_interval;
  struct timespec it_value;
};
struct sigevent;
struct __locale_struct
{
  struct __locale_data *__locales[13];
  const unsigned short int *__ctype_b;
  const int *__ctype_tolower;
  const int *__ctype_toupper;
  const char *__names[13];
};
typedef struct __locale_struct *__locale_t;
typedef __locale_t locale_t;

typedef long int __jmp_buf[8];
enum
{
  PTHREAD_CREATE_JOINABLE,
  PTHREAD_CREATE_DETACHED
};
enum
{
  PTHREAD_MUTEX_TIMED_NP,
  PTHREAD_MUTEX_RECURSIVE_NP,
  PTHREAD_MUTEX_ERRORCHECK_NP,
  PTHREAD_MUTEX_ADAPTIVE_NP,
  PTHREAD_MUTEX_NORMAL = PTHREAD_MUTEX_TIMED_NP,
  PTHREAD_MUTEX_RECURSIVE = PTHREAD_MUTEX_RECURSIVE_NP,
  PTHREAD_MUTEX_ERRORCHECK = PTHREAD_MUTEX_ERRORCHECK_NP,
  PTHREAD_MUTEX_DEFAULT = PTHREAD_MUTEX_NORMAL
};
enum
{
  PTHREAD_MUTEX_STALLED,
  PTHREAD_MUTEX_STALLED_NP = PTHREAD_MUTEX_STALLED,
  PTHREAD_MUTEX_ROBUST,
  PTHREAD_MUTEX_ROBUST_NP = PTHREAD_MUTEX_ROBUST
};
enum
{
  PTHREAD_PRIO_NONE,
  PTHREAD_PRIO_INHERIT,
  PTHREAD_PRIO_PROTECT
};
enum
{
  PTHREAD_RWLOCK_PREFER_READER_NP,
  PTHREAD_RWLOCK_PREFER_WRITER_NP,
  PTHREAD_RWLOCK_PREFER_WRITER_NONRECURSIVE_NP,
  PTHREAD_RWLOCK_DEFAULT_NP = PTHREAD_RWLOCK_PREFER_READER_NP
};
enum
{
  PTHREAD_INHERIT_SCHED,
  PTHREAD_EXPLICIT_SCHED
};
enum
{
  PTHREAD_SCOPE_SYSTEM,
  PTHREAD_SCOPE_PROCESS
};
enum
{
  PTHREAD_PROCESS_PRIVATE,
  PTHREAD_PROCESS_SHARED
};
struct _pthread_cleanup_buffer
{
  void (*__routine)(void *);
  void *__arg;
  int __canceltype;
  struct _pthread_cleanup_buffer *__prev;
};
enum
{
  PTHREAD_CANCEL_ENABLE,
  PTHREAD_CANCEL_DISABLE
};
enum
{
  PTHREAD_CANCEL_DEFERRED,
  PTHREAD_CANCEL_ASYNCHRONOUS
};

typedef struct
{
  struct
  {
    __jmp_buf __cancel_jmp_buf;
    int __mask_was_saved;
  } __cancel_jmp_buf[1];
  void *__pad[4];
} __pthread_unwind_buf_t;
struct __pthread_cleanup_frame
{
  void (*__cancel_routine)(void *);
  void *__cancel_arg;
  int __do_it;
  int __cancel_type;
};

struct __jmp_buf_tag;
struct uv__work
{
  void (*work)(struct uv__work *w);
  void (*done)(struct uv__work *w, int status);
  struct uv_loop_s *loop;
  void *wq[2];
};
struct uv__io_s;
struct uv_loop_s;
typedef void (*uv__io_cb)(struct uv_loop_s *loop,
                          struct uv__io_s *w,
                          unsigned int events);
typedef struct uv__io_s uv__io_t;
struct uv__io_s
{
  uv__io_cb cb;
  void *pending_queue[2];
  void *watcher_queue[2];
  unsigned int pevents;
  unsigned int events;
  int fd;
};

typedef struct uv_buf_t
{
  char *base;
  size_t len;
} uv_buf_t;

typedef int uv_file;
typedef int uv_os_sock_t;
typedef int uv_os_fd_t;
typedef pid_t uv_pid_t;
typedef int pthread_once_t;
typedef pthread_once_t uv_once_t;
typedef pthread_t uv_thread_t;
typedef pthread_mutex_t uv_mutex_t;
typedef sem_t uv_sem_t;
typedef pthread_rwlock_t uv_rwlock_t;
typedef pthread_cond_t uv_cond_t;
typedef unsigned int pthread_key_t;
typedef pthread_key_t uv_key_t;

typedef pthread_barrier_t uv_barrier_t;
typedef gid_t uv_gid_t;
typedef uid_t uv_uid_t;
typedef struct
{
  void *handle;
  char *errmsg;
} uv_lib_t;
typedef enum
{
  UV_E2BIG = (-(7)),
  UV_EACCES = (-(13)),
  UV_EADDRINUSE = (-(98)),
  UV_EADDRNOTAVAIL = (-(99)),
  UV_EAFNOSUPPORT = (-(97)),
  UV_EAGAIN = (-(11)),
  UV_EAI_ADDRFAMILY = (-3000),
  UV_EAI_AGAIN = (-3001),
  UV_EAI_BADFLAGS = (-3002),
  UV_EAI_BADHINTS = (-3013),
  UV_EAI_CANCELED = (-3003),
  UV_EAI_FAIL = (-3004),
  UV_EAI_FAMILY = (-3005),
  UV_EAI_MEMORY = (-3006),
  UV_EAI_NODATA = (-3007),
  UV_EAI_NONAME = (-3008),
  UV_EAI_OVERFLOW = (-3009),
  UV_EAI_PROTOCOL = (-3014),
  UV_EAI_SERVICE = (-3010),
  UV_EAI_SOCKTYPE = (-3011),
  UV_EALREADY = (-(114)),
  UV_EBADF = (-(9)),
  UV_EBUSY = (-(16)),
  UV_ECANCELED = (-(125)),
  UV_ECHARSET = (-4080),
  UV_ECONNABORTED = (-(103)),
  UV_ECONNREFUSED = (-(111)),
  UV_ECONNRESET = (-(104)),
  UV_EDESTADDRREQ = (-(89)),
  UV_EEXIST = (-(17)),
  UV_EFAULT = (-(14)),
  UV_EFBIG = (-(27)),
  UV_EHOSTUNREACH = (-(113)),
  UV_EINTR = (-(4)),
  UV_EINVAL = (-(22)),
  UV_EIO = (-(5)),
  UV_EISCONN = (-(106)),
  UV_EISDIR = (-(21)),
  UV_ELOOP = (-(40)),
  UV_EMFILE = (-(24)),
  UV_EMSGSIZE = (-(90)),
  UV_ENAMETOOLONG = (-(36)),
  UV_ENETDOWN = (-(100)),
  UV_ENETUNREACH = (-(101)),
  UV_ENFILE = (-(23)),
  UV_ENOBUFS = (-(105)),
  UV_ENODEV = (-(19)),
  UV_ENOENT = (-(2)),
  UV_ENOMEM = (-(12)),
  UV_ENONET = (-(64)),
  UV_ENOPROTOOPT = (-(92)),
  UV_ENOSPC = (-(28)),
  UV_ENOSYS = (-(38)),
  UV_ENOTCONN = (-(107)),
  UV_ENOTDIR = (-(20)),
  UV_ENOTEMPTY = (-(39)),
  UV_ENOTSOCK = (-(88)),
  UV_ENOTSUP = (-(95)),
  UV_EOVERFLOW = (-(75)),
  UV_EPERM = (-(1)),
  UV_EPIPE = (-(32)),
  UV_EPROTO = (-(71)),
  UV_EPROTONOSUPPORT = (-(93)),
  UV_EPROTOTYPE = (-(91)),
  UV_ERANGE = (-(34)),
  UV_EROFS = (-(30)),
  UV_ESHUTDOWN = (-(108)),
  UV_ESPIPE = (-(29)),
  UV_ESRCH = (-(3)),
  UV_ETIMEDOUT = (-(110)),
  UV_ETXTBSY = (-(26)),
  UV_EXDEV = (-(18)),
  UV_UNKNOWN = (-4094),
  UV_EOF = (-4095),
  UV_ENXIO = (-(6)),
  UV_EMLINK = (-(31)),
  UV_EHOSTDOWN = (-(112)),
  UV_EREMOTEIO = (-(121)),
  UV_ENOTTY = (-(25)),
  UV_EFTYPE = (-4028),
  UV_EILSEQ = (-(84)),
  UV_ESOCKTNOSUPPORT = (-(94)),
  UV_ERRNO_MAX = (-4095) - 1
} uv_errno_t;
typedef enum
{
  UV_UNKNOWN_HANDLE = 0,
  UV_ASYNC,
  UV_CHECK,
  UV_FS_EVENT,
  UV_FS_POLL,
  UV_HANDLE,
  UV_IDLE,
  UV_NAMED_PIPE,
  UV_POLL,
  UV_PREPARE,
  UV_PROCESS,
  UV_STREAM,
  UV_TCP,
  UV_TIMER,
  UV_TTY,
  UV_UDP,
  UV_SIGNAL,
  UV_FILE,
  UV_HANDLE_TYPE_MAX
} uv_handle_type;
typedef enum
{
  UV_UNKNOWN_REQ = 0,
  UV_REQ,
  UV_CONNECT,
  UV_WRITE,
  UV_SHUTDOWN,
  UV_UDP_SEND,
  UV_FS,
  UV_WORK,
  UV_GETADDRINFO,
  UV_GETNAMEINFO,
  UV_RANDOM,

  UV_REQ_TYPE_MAX
} uv_req_type;

typedef struct uv_loop_s uv_loop_t;
typedef struct uv_handle_s uv_handle_t;
typedef struct uv_dir_s uv_dir_t;
typedef struct uv_stream_s uv_stream_t;
typedef struct uv_tcp_s uv_tcp_t;
typedef struct uv_udp_s uv_udp_t;
typedef struct uv_pipe_s uv_pipe_t;
typedef struct uv_tty_s uv_tty_t;
typedef struct uv_poll_s uv_poll_t;
typedef struct uv_timer_s uv_timer_t;
typedef struct uv_prepare_s uv_prepare_t;
typedef struct uv_check_s uv_check_t;
typedef struct uv_idle_s uv_idle_t;
typedef struct uv_async_s uv_async_t;
typedef struct uv_process_s uv_process_t;
typedef struct uv_fs_event_s uv_fs_event_t;
typedef struct uv_fs_poll_s uv_fs_poll_t;
typedef struct uv_signal_s uv_signal_t;
typedef struct uv_req_s uv_req_t;
typedef struct uv_getaddrinfo_s uv_getaddrinfo_t;
typedef struct uv_getnameinfo_s uv_getnameinfo_t;
typedef struct uv_shutdown_s uv_shutdown_t;
typedef struct uv_write_s uv_write_t;
typedef struct uv_connect_s uv_connect_t;
typedef struct uv_udp_send_s uv_udp_send_t;
typedef struct uv_fs_s uv_fs_t;
typedef struct uv_work_s uv_work_t;
typedef struct uv_random_s uv_random_t;
typedef struct uv_env_item_s uv_env_item_t;
typedef struct uv_cpu_info_s uv_cpu_info_t;
typedef struct uv_interface_address_s uv_interface_address_t;
typedef struct uv_dirent_s uv_dirent_t;
typedef struct uv_passwd_s uv_passwd_t;
typedef struct uv_utsname_s uv_utsname_t;
typedef struct uv_statfs_s uv_statfs_t;
typedef enum
{
  UV_LOOP_BLOCK_SIGNAL = 0,
  UV_METRICS_IDLE_TIME
} uv_loop_option;
typedef enum
{
  UV_RUN_DEFAULT = 0,
  UV_RUN_ONCE,
  UV_RUN_NOWAIT
} uv_run_mode;
unsigned int uv_version(void);
const char *uv_version_string(void);
typedef void *(*uv_malloc_func)(size_t size);
typedef void *(*uv_realloc_func)(void *ptr, size_t size);
typedef void *(*uv_calloc_func)(size_t count, size_t size);
typedef void (*uv_free_func)(void *ptr);
void uv_library_shutdown(void);
int uv_replace_allocator(uv_malloc_func malloc_func,
                         uv_realloc_func realloc_func,
                         uv_calloc_func calloc_func,
                         uv_free_func free_func);
uv_loop_t *uv_default_loop(void);
int uv_loop_init(uv_loop_t *loop);
int uv_loop_close(uv_loop_t *loop);
uv_loop_t *uv_loop_new(void);
void uv_loop_delete(uv_loop_t *);
size_t uv_loop_size(void);
int uv_loop_alive(const uv_loop_t *loop);
int uv_loop_configure(uv_loop_t *loop, uv_loop_option option, ...);
int uv_loop_fork(uv_loop_t *loop);
int uv_run(uv_loop_t *, uv_run_mode mode);
void uv_stop(uv_loop_t *);
void uv_ref(uv_handle_t *);
void uv_unref(uv_handle_t *);
int uv_has_ref(const uv_handle_t *);
void uv_update_time(uv_loop_t *);
uint64_t uv_now(const uv_loop_t *);
int uv_backend_fd(const uv_loop_t *);
int uv_backend_timeout(const uv_loop_t *);
typedef void (*uv_alloc_cb)(uv_handle_t *handle,
                            size_t suggested_size,
                            uv_buf_t *buf);
typedef void (*uv_read_cb)(uv_stream_t *stream,
                           ssize_t nread,
                           const uv_buf_t *buf);
typedef void (*uv_write_cb)(uv_write_t *req, int status);
typedef void (*uv_connect_cb)(uv_connect_t *req, int status);
typedef void (*uv_shutdown_cb)(uv_shutdown_t *req, int status);
typedef void (*uv_connection_cb)(uv_stream_t *server, int status);
typedef void (*uv_close_cb)(uv_handle_t *handle);
typedef void (*uv_poll_cb)(uv_poll_t *handle, int status, int events);
typedef void (*uv_timer_cb)(uv_timer_t *handle);
typedef void (*uv_async_cb)(uv_async_t *handle);
typedef void (*uv_prepare_cb)(uv_prepare_t *handle);
typedef void (*uv_check_cb)(uv_check_t *handle);
typedef void (*uv_idle_cb)(uv_idle_t *handle);
typedef void (*uv_exit_cb)(uv_process_t *, int64_t exit_status, int term_signal);
typedef void (*uv_walk_cb)(uv_handle_t *handle, void *arg);
typedef void (*uv_fs_cb)(uv_fs_t *req);
typedef void (*uv_work_cb)(uv_work_t *req);
typedef void (*uv_after_work_cb)(uv_work_t *req, int status);
typedef void (*uv_getaddrinfo_cb)(uv_getaddrinfo_t *req,
                                  int status,
                                  struct addrinfo *res);
typedef void (*uv_getnameinfo_cb)(uv_getnameinfo_t *req,
                                  int status,
                                  const char *hostname,
                                  const char *service);
typedef void (*uv_random_cb)(uv_random_t *req,
                             int status,
                             void *buf,
                             size_t buflen);
typedef struct
{
  long tv_sec;
  long tv_nsec;
} uv_timespec_t;
typedef struct
{
  uint64_t st_dev;
  uint64_t st_mode;
  uint64_t st_nlink;
  uint64_t st_uid;
  uint64_t st_gid;
  uint64_t st_rdev;
  uint64_t st_ino;
  uint64_t st_size;
  uint64_t st_blksize;
  uint64_t st_blocks;
  uint64_t st_flags;
  uint64_t st_gen;
  uv_timespec_t st_atim;
  uv_timespec_t st_mtim;
  uv_timespec_t st_ctim;
  uv_timespec_t st_birthtim;
} uv_stat_t;
typedef void (*uv_fs_event_cb)(uv_fs_event_t *handle,
                               const char *filename,
                               int events,
                               int status);
typedef void (*uv_fs_poll_cb)(uv_fs_poll_t *handle,
                              int status,
                              const uv_stat_t *prev,
                              const uv_stat_t *curr);
typedef void (*uv_signal_cb)(uv_signal_t *handle, int signum);
typedef enum
{
  UV_LEAVE_GROUP = 0,
  UV_JOIN_GROUP
} uv_membership;
int uv_translate_sys_error(int sys_errno);
const char *uv_strerror(int err);
char *uv_strerror_r(int err, char *buf, size_t buflen);
const char *uv_err_name(int err);
char *uv_err_name_r(int err, char *buf, size_t buflen);

struct uv_req_s
{
  void *data;
  uv_req_type type;
  void *reserved[6];
};

int uv_shutdown(uv_shutdown_t *req,
                uv_stream_t *handle,
                uv_shutdown_cb cb);
struct uv_shutdown_s
{
  void *data;
  uv_req_type type;
  void *reserved[6];
  uv_stream_t *handle;
  uv_shutdown_cb cb;
};

struct uv_handle_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
};

size_t uv_handle_size(uv_handle_type type);
uv_handle_type uv_handle_get_type(const uv_handle_t *handle);
const char *uv_handle_type_name(uv_handle_type type);
void *uv_handle_get_data(const uv_handle_t *handle);
uv_loop_t *uv_handle_get_loop(const uv_handle_t *handle);
void uv_handle_set_data(uv_handle_t *handle, void *data);
size_t uv_req_size(uv_req_type type);
void *uv_req_get_data(const uv_req_t *req);
void uv_req_set_data(uv_req_t *req, void *data);
uv_req_type uv_req_get_type(const uv_req_t *req);
const char *uv_req_type_name(uv_req_type type);
int uv_is_active(const uv_handle_t *handle);
void uv_walk(uv_loop_t *loop, uv_walk_cb walk_cb, void *arg);
void uv_print_all_handles(uv_loop_t *loop, FILE *stream);
void uv_print_active_handles(uv_loop_t *loop, FILE *stream);
void uv_close(uv_handle_t *handle, uv_close_cb close_cb);
int uv_send_buffer_size(uv_handle_t *handle, int *value);
int uv_recv_buffer_size(uv_handle_t *handle, int *value);
int uv_fileno(const uv_handle_t *handle, uv_os_fd_t *fd);
uv_buf_t uv_buf_init(char *base, unsigned int len);
int uv_pipe(uv_file fds[2], int read_flags, int write_flags);
int uv_socketpair(int type,
                  int protocol,
                  uv_os_sock_t socket_vector[2],
                  int flags0,
                  int flags1);
struct uv_stream_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  size_t write_queue_size;
  uv_alloc_cb alloc_cb;
  uv_read_cb read_cb;
  uv_connect_t *connect_req;
  uv_shutdown_t *shutdown_req;
  uv__io_t io_watcher;
  void *write_queue[2];
  void *write_completed_queue[2];
  uv_connection_cb connection_cb;
  int delayed_error;
  int accepted_fd;
  void *queued_fds;
};

size_t uv_stream_get_write_queue_size(const uv_stream_t *stream);
int uv_listen(uv_stream_t *stream, int backlog, uv_connection_cb cb);
int uv_accept(uv_stream_t *server, uv_stream_t *client);
int uv_read_start(uv_stream_t *,
                  uv_alloc_cb alloc_cb,
                  uv_read_cb read_cb);
int uv_read_stop(uv_stream_t *);
int uv_write(uv_write_t *req,
             uv_stream_t *handle,
             const uv_buf_t bufs[],
             unsigned int nbufs,
             uv_write_cb cb);
int uv_write2(uv_write_t *req,
              uv_stream_t *handle,
              const uv_buf_t bufs[],
              unsigned int nbufs,
              uv_stream_t *send_handle,
              uv_write_cb cb);
int uv_try_write(uv_stream_t *handle,
                 const uv_buf_t bufs[],
                 unsigned int nbufs);
int uv_try_write2(uv_stream_t *handle,
                  const uv_buf_t bufs[],
                  unsigned int nbufs,
                  uv_stream_t *send_handle);
struct uv_write_s
{
  void *data;
  uv_req_type type;
  void *reserved[6];
  uv_write_cb cb;
  uv_stream_t *send_handle;
  uv_stream_t *handle;
  void *queue[2];
  unsigned int write_index;
  uv_buf_t *bufs;
  unsigned int nbufs;
  int error;
  uv_buf_t bufsml[4];
};

int uv_is_readable(const uv_stream_t *handle);
int uv_is_writable(const uv_stream_t *handle);
int uv_stream_set_blocking(uv_stream_t *handle, int blocking);
int uv_is_closing(const uv_handle_t *handle);
struct uv_tcp_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  size_t write_queue_size;
  uv_alloc_cb alloc_cb;
  uv_read_cb read_cb;
  uv_connect_t *connect_req;
  uv_shutdown_t *shutdown_req;
  uv__io_t io_watcher;
  void *write_queue[2];
  void *write_completed_queue[2];
  uv_connection_cb connection_cb;
  int delayed_error;
  int accepted_fd;
  void *queued_fds;
};

int uv_tcp_init(uv_loop_t *, uv_tcp_t *handle);
int uv_tcp_init_ex(uv_loop_t *, uv_tcp_t *handle, unsigned int flags);
int uv_tcp_open(uv_tcp_t *handle, uv_os_sock_t sock);
int uv_tcp_nodelay(uv_tcp_t *handle, int enable);
int uv_tcp_keepalive(uv_tcp_t *handle,
                     int enable,
                     unsigned int delay);
int uv_tcp_simultaneous_accepts(uv_tcp_t *handle, int enable);
enum uv_tcp_flags
{
  UV_TCP_IPV6ONLY = 1
};
int uv_tcp_bind(uv_tcp_t *handle,
                const struct sockaddr *addr,
                unsigned int flags);
int uv_tcp_getsockname(const uv_tcp_t *handle,
                       struct sockaddr *name,
                       int *namelen);
int uv_tcp_getpeername(const uv_tcp_t *handle,
                       struct sockaddr *name,
                       int *namelen);
int uv_tcp_close_reset(uv_tcp_t *handle, uv_close_cb close_cb);
int uv_tcp_connect(uv_connect_t *req,
                   uv_tcp_t *handle,
                   const struct sockaddr *addr,
                   uv_connect_cb cb);
struct uv_connect_s
{
  void *data;
  uv_req_type type;
  void *reserved[6];
  uv_connect_cb cb;
  uv_stream_t *handle;
  void *queue[2];
};

enum uv_udp_flags
{
  UV_UDP_IPV6ONLY = 1,
  UV_UDP_PARTIAL = 2,
  UV_UDP_REUSEADDR = 4,
  UV_UDP_MMSG_CHUNK = 8,
  UV_UDP_MMSG_FREE = 16,
  UV_UDP_LINUX_RECVERR = 32,
  UV_UDP_RECVMMSG = 256
};
typedef void (*uv_udp_send_cb)(uv_udp_send_t *req, int status);
typedef void (*uv_udp_recv_cb)(uv_udp_t *handle,
                               ssize_t nread,
                               const uv_buf_t *buf,
                               const struct sockaddr *addr,
                               unsigned flags);
struct uv_udp_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  size_t send_queue_size;
  size_t send_queue_count;
  uv_alloc_cb alloc_cb;
  uv_udp_recv_cb recv_cb;
  uv__io_t io_watcher;
  void *write_queue[2];
  void *write_completed_queue[2];
};

struct uv_udp_send_s
{
  void *data;
  uv_req_type type;
  void *reserved[6];
  uv_udp_t *handle;
  uv_udp_send_cb cb;
  void *queue[2];
  struct sockaddr_storage addr;
  unsigned int nbufs;
  uv_buf_t *bufs;
  ssize_t status;
  uv_udp_send_cb send_cb;
  uv_buf_t bufsml[4];
};

int uv_udp_init(uv_loop_t *, uv_udp_t *handle);
int uv_udp_init_ex(uv_loop_t *, uv_udp_t *handle, unsigned int flags);
int uv_udp_open(uv_udp_t *handle, uv_os_sock_t sock);
int uv_udp_bind(uv_udp_t *handle,
                const struct sockaddr *addr,
                unsigned int flags);
int uv_udp_connect(uv_udp_t *handle, const struct sockaddr *addr);
int uv_udp_getpeername(const uv_udp_t *handle,
                       struct sockaddr *name,
                       int *namelen);
int uv_udp_getsockname(const uv_udp_t *handle,
                       struct sockaddr *name,
                       int *namelen);
int uv_udp_set_membership(uv_udp_t *handle,
                          const char *multicast_addr,
                          const char *interface_addr,
                          uv_membership membership);
int uv_udp_set_source_membership(uv_udp_t *handle,
                                 const char *multicast_addr,
                                 const char *interface_addr,
                                 const char *source_addr,
                                 uv_membership membership);
int uv_udp_set_multicast_loop(uv_udp_t *handle, int on);
int uv_udp_set_multicast_ttl(uv_udp_t *handle, int ttl);
int uv_udp_set_multicast_interface(uv_udp_t *handle,
                                   const char *interface_addr);
int uv_udp_set_broadcast(uv_udp_t *handle, int on);
int uv_udp_set_ttl(uv_udp_t *handle, int ttl);
int uv_udp_send(uv_udp_send_t *req,
                uv_udp_t *handle,
                const uv_buf_t bufs[],
                unsigned int nbufs,
                const struct sockaddr *addr,
                uv_udp_send_cb send_cb);
int uv_udp_try_send(uv_udp_t *handle,
                    const uv_buf_t bufs[],
                    unsigned int nbufs,
                    const struct sockaddr *addr);
int uv_udp_recv_start(uv_udp_t *handle,
                      uv_alloc_cb alloc_cb,
                      uv_udp_recv_cb recv_cb);
int uv_udp_using_recvmmsg(const uv_udp_t *handle);
int uv_udp_recv_stop(uv_udp_t *handle);
size_t uv_udp_get_send_queue_size(const uv_udp_t *handle);
size_t uv_udp_get_send_queue_count(const uv_udp_t *handle);

struct uv_tty_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  size_t write_queue_size;
  uv_alloc_cb alloc_cb;
  uv_read_cb read_cb;
  uv_connect_t *connect_req;
  uv_shutdown_t *shutdown_req;
  uv__io_t io_watcher;
  void *write_queue[2];
  void *write_completed_queue[2];
  uv_connection_cb connection_cb;
  int delayed_error;
  int accepted_fd;
  void *queued_fds;
  struct termios orig_termios;
  int mode;
};

typedef enum
{
  UV_TTY_MODE_NORMAL,
  UV_TTY_MODE_RAW,
  UV_TTY_MODE_IO
} uv_tty_mode_t;
typedef enum
{
  UV_TTY_SUPPORTED,
  UV_TTY_UNSUPPORTED
} uv_tty_vtermstate_t;
int uv_tty_init(uv_loop_t *, uv_tty_t *, uv_file fd, int readable);
int uv_tty_set_mode(uv_tty_t *, uv_tty_mode_t mode);
int uv_tty_reset_mode(void);
int uv_tty_get_winsize(uv_tty_t *, int *width, int *height);
void uv_tty_set_vterm_state(uv_tty_vtermstate_t state);
int uv_tty_get_vterm_state(uv_tty_vtermstate_t *state);
uv_handle_type uv_guess_handle(uv_file file);

struct uv_pipe_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  size_t write_queue_size;
  uv_alloc_cb alloc_cb;
  uv_read_cb read_cb;
  uv_connect_t *connect_req;
  uv_shutdown_t *shutdown_req;
  uv__io_t io_watcher;
  void *write_queue[2];
  void *write_completed_queue[2];
  uv_connection_cb connection_cb;
  int delayed_error;
  int accepted_fd;
  void *queued_fds;
  int ipc; /* non-zero if this pipe is used for passing handles */
  const char *pipe_fname;
};

int uv_pipe_init(uv_loop_t *, uv_pipe_t *handle, int ipc);
int uv_pipe_open(uv_pipe_t *, uv_file file);
int uv_pipe_bind(uv_pipe_t *handle, const char *name);
void uv_pipe_connect(uv_connect_t *req,
                     uv_pipe_t *handle,
                     const char *name,
                     uv_connect_cb cb);
int uv_pipe_getsockname(const uv_pipe_t *handle,
                        char *buffer,
                        size_t *size);
int uv_pipe_getpeername(const uv_pipe_t *handle,
                        char *buffer,
                        size_t *size);
void uv_pipe_pending_instances(uv_pipe_t *handle, int count);
int uv_pipe_pending_count(uv_pipe_t *handle);
uv_handle_type uv_pipe_pending_type(uv_pipe_t *handle);
int uv_pipe_chmod(uv_pipe_t *handle, int flags);

struct uv_poll_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  uv_poll_cb poll_cb;
  uv__io_t io_watcher;
};

enum uv_poll_event
{
  UV_READABLE = 1,
  UV_WRITABLE = 2,
  UV_DISCONNECT = 4,
  UV_PRIORITIZED = 8
};
int uv_poll_init(uv_loop_t *loop, uv_poll_t *handle, int fd);
int uv_poll_init_socket(uv_loop_t *loop,
                        uv_poll_t *handle,
                        uv_os_sock_t socket);
int uv_poll_start(uv_poll_t *handle, int events, uv_poll_cb cb);
int uv_poll_stop(uv_poll_t *handle);

struct uv_prepare_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  uv_prepare_cb prepare_cb;
  void *queue[2];
};

int uv_prepare_init(uv_loop_t *, uv_prepare_t *prepare);
int uv_prepare_start(uv_prepare_t *prepare, uv_prepare_cb cb);
int uv_prepare_stop(uv_prepare_t *prepare);

struct uv_check_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  uv_check_cb check_cb;
  void *queue[2];
};

int uv_check_init(uv_loop_t *, uv_check_t *check);
int uv_check_start(uv_check_t *check, uv_check_cb cb);
int uv_check_stop(uv_check_t *check);

struct uv_idle_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  uv_idle_cb idle_cb;
  void *queue[2];
};

int uv_idle_init(uv_loop_t *, uv_idle_t *idle);
int uv_idle_start(uv_idle_t *idle, uv_idle_cb cb);
int uv_idle_stop(uv_idle_t *idle);

struct uv_async_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  uv_async_cb async_cb;
  void *queue[2];
  int pending;
};

int uv_async_init(uv_loop_t *,
                  uv_async_t *async,
                  uv_async_cb async_cb);
int uv_async_send(uv_async_t *async);

struct uv_timer_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  void *heap_node[3];
  int unused;
  uint64_t timeout;
  uint64_t repeat;
  uint64_t start_id;
  uv_timer_cb timer_cb;
  uv_timer_cb timer_cb;
  void *heap_node[3];
  uint64_t timeout;
  uint64_t repeat;
  uint64_t start_id;
};

int uv_timer_init(uv_loop_t *, uv_timer_t *handle);
int uv_timer_start(uv_timer_t *handle,
                   uv_timer_cb cb,
                   uint64_t timeout,
                   uint64_t repeat);
int uv_timer_stop(uv_timer_t *handle);
int uv_timer_again(uv_timer_t *handle);
void uv_timer_set_repeat(uv_timer_t *handle, uint64_t repeat);
uint64_t uv_timer_get_repeat(const uv_timer_t *handle);
uint64_t uv_timer_get_due_in(const uv_timer_t *handle);

struct uv_getaddrinfo_s
{
  void *data;
  uv_req_type type;
  void *reserved[6];
  uv_loop_t *loop;
  struct uv__work work_req;
  uv_getaddrinfo_cb cb;
  struct addrinfo *hints;
  char *hostname;
  char *service;
  struct addrinfo *addrinfo;
  int retcode;
};

int uv_getaddrinfo(uv_loop_t *loop,
                   uv_getaddrinfo_t *req,
                   uv_getaddrinfo_cb getaddrinfo_cb,
                   const char *node,
                   const char *service,
                   const struct addrinfo *hints);
void uv_freeaddrinfo(struct addrinfo *ai);

struct uv_getnameinfo_s
{
  void *data;
  uv_req_type type;
  void *reserved[6];
  uv_loop_t *loop;
  struct uv__work work_req;
  uv_getnameinfo_cb getnameinfo_cb;
  struct sockaddr_storage storage;
  int flags;
  char host[1025];
  char service[32];
  int retcode;
};

int uv_getnameinfo(uv_loop_t *loop,
                   uv_getnameinfo_t *req,
                   uv_getnameinfo_cb getnameinfo_cb,
                   const struct sockaddr *addr,
                   int flags);
typedef enum
{
  UV_IGNORE = 0x00,
  UV_CREATE_PIPE = 0x01,
  UV_INHERIT_FD = 0x02,
  UV_INHERIT_STREAM = 0x04,
  UV_READABLE_PIPE = 0x10,
  UV_WRITABLE_PIPE = 0x20,
  UV_NONBLOCK_PIPE = 0x40,
  UV_OVERLAPPED_PIPE = 0x40
} uv_stdio_flags;
typedef struct uv_stdio_container_s
{
  uv_stdio_flags flags;
  union
  {
    uv_stream_t *stream;
    int fd;
  } data;
} uv_stdio_container_t;
typedef struct uv_process_options_s
{
  uv_exit_cb exit_cb;
  const char *file;
  char **args;
  char **env;
  const char *cwd;
  unsigned int flags;
  int stdio_count;
  uv_stdio_container_t *stdio;
  uv_uid_t uid;
  uv_gid_t gid;
} uv_process_options_t;
enum uv_process_flags
{
  UV_PROCESS_SETUID = (1 << 0),
  UV_PROCESS_SETGID = (1 << 1),
  UV_PROCESS_WINDOWS_VERBATIM_ARGUMENTS = (1 << 2),
  UV_PROCESS_DETACHED = (1 << 3),
  UV_PROCESS_WINDOWS_HIDE = (1 << 4),
  UV_PROCESS_WINDOWS_HIDE_CONSOLE = (1 << 5),
  UV_PROCESS_WINDOWS_HIDE_GUI = (1 << 6)
};

struct uv_process_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  uv_exit_cb exit_cb;
  int pid;
  void *queue[2];
  int status;
};

int uv_spawn(uv_loop_t *loop,
             uv_process_t *handle,
             const uv_process_options_t *options);
int uv_process_kill(uv_process_t *, int signum);
int uv_kill(int pid, int signum);
uv_pid_t uv_process_get_pid(const uv_process_t *);

struct uv_work_s
{
  void *data;
  uv_req_type type;
  void *reserved[6];
  uv_loop_t *loop;
  uv_work_cb work_cb;
  uv_after_work_cb after_work_cb;
  struct uv__work work_req;
};

int uv_queue_work(uv_loop_t *loop,
                  uv_work_t *req,
                  uv_work_cb work_cb,
                  uv_after_work_cb after_work_cb);
int uv_cancel(uv_req_t *req);
struct uv_cpu_times_s
{
  uint64_t user;
  uint64_t nice;
  uint64_t sys;
  uint64_t idle;
  uint64_t irq;
};
struct uv_cpu_info_s
{
  char *model;
  int speed;
  struct uv_cpu_times_s cpu_times;
};
struct uv_interface_address_s
{
  char *name;
  char phys_addr[6];
  int is_internal;
  union
  {
    struct sockaddr_in address4;
    struct sockaddr_in6 address6;
  } address;
  union
  {
    struct sockaddr_in netmask4;
    struct sockaddr_in6 netmask6;
  } netmask;
};
struct uv_passwd_s
{
  char *username;
  unsigned long uid;
  unsigned long gid;
  char *shell;
  char *homedir;
};
struct uv_utsname_s
{
  char sysname[256];
  char release[256];
  char version[256];
  char machine[256];
};
struct uv_statfs_s
{
  uint64_t f_type;
  uint64_t f_bsize;
  uint64_t f_blocks;
  uint64_t f_bfree;
  uint64_t f_bavail;
  uint64_t f_files;
  uint64_t f_ffree;
  uint64_t f_spare[4];
};
typedef enum
{
  UV_DIRENT_UNKNOWN,
  UV_DIRENT_FILE,
  UV_DIRENT_DIR,
  UV_DIRENT_LINK,
  UV_DIRENT_FIFO,
  UV_DIRENT_SOCKET,
  UV_DIRENT_CHAR,
  UV_DIRENT_BLOCK
} uv_dirent_type_t;
struct uv_dirent_s
{
  const char *name;
  uv_dirent_type_t type;
};
char **uv_setup_args(int argc, char **argv);
int uv_get_process_title(char *buffer, size_t size);
int uv_set_process_title(const char *title);
int uv_resident_set_memory(size_t *rss);
int uv_uptime(double *uptime);
uv_os_fd_t uv_get_osfhandle(int fd);
int uv_open_osfhandle(uv_os_fd_t os_fd);
typedef struct
{
  long tv_sec;
  long tv_usec;
} uv_timeval_t;
typedef struct
{
  int64_t tv_sec;
  int32_t tv_usec;
} uv_timeval64_t;
typedef struct
{
  uv_timeval_t ru_utime;
  uv_timeval_t ru_stime;
  uint64_t ru_maxrss;
  uint64_t ru_ixrss;
  uint64_t ru_idrss;
  uint64_t ru_isrss;
  uint64_t ru_minflt;
  uint64_t ru_majflt;
  uint64_t ru_nswap;
  uint64_t ru_inblock;
  uint64_t ru_oublock;
  uint64_t ru_msgsnd;
  uint64_t ru_msgrcv;
  uint64_t ru_nsignals;
  uint64_t ru_nvcsw;
  uint64_t ru_nivcsw;
} uv_rusage_t;
int uv_getrusage(uv_rusage_t *rusage);
int uv_os_homedir(char *buffer, size_t *size);
int uv_os_tmpdir(char *buffer, size_t *size);
int uv_os_get_passwd(uv_passwd_t *pwd);
void uv_os_free_passwd(uv_passwd_t *pwd);
uv_pid_t uv_os_getpid(void);
uv_pid_t uv_os_getppid(void);
int uv_os_getpriority(uv_pid_t pid, int *priority);
int uv_os_setpriority(uv_pid_t pid, int priority);
unsigned int uv_available_parallelism(void);
int uv_cpu_info(uv_cpu_info_t **cpu_infos, int *count);
void uv_free_cpu_info(uv_cpu_info_t *cpu_infos, int count);
int uv_interface_addresses(uv_interface_address_t **addresses,
                           int *count);
void uv_free_interface_addresses(uv_interface_address_t *addresses,
                                 int count);
struct uv_env_item_s
{
  char *name;
  char *value;
};
int uv_os_environ(uv_env_item_t **envitems, int *count);
void uv_os_free_environ(uv_env_item_t *envitems, int count);
int uv_os_getenv(const char *name, char *buffer, size_t *size);
int uv_os_setenv(const char *name, const char *value);
int uv_os_unsetenv(const char *name);
int uv_os_gethostname(char *buffer, size_t *size);
int uv_os_uname(uv_utsname_t *buffer);
uint64_t uv_metrics_idle_time(uv_loop_t *loop);
typedef enum
{
  UV_FS_UNKNOWN = -1,
  UV_FS_CUSTOM,
  UV_FS_OPEN,
  UV_FS_CLOSE,
  UV_FS_READ,
  UV_FS_WRITE,
  UV_FS_SENDFILE,
  UV_FS_STAT,
  UV_FS_LSTAT,
  UV_FS_FSTAT,
  UV_FS_FTRUNCATE,
  UV_FS_UTIME,
  UV_FS_FUTIME,
  UV_FS_ACCESS,
  UV_FS_CHMOD,
  UV_FS_FCHMOD,
  UV_FS_FSYNC,
  UV_FS_FDATASYNC,
  UV_FS_UNLINK,
  UV_FS_RMDIR,
  UV_FS_MKDIR,
  UV_FS_MKDTEMP,
  UV_FS_RENAME,
  UV_FS_SCANDIR,
  UV_FS_LINK,
  UV_FS_SYMLINK,
  UV_FS_READLINK,
  UV_FS_CHOWN,
  UV_FS_FCHOWN,
  UV_FS_REALPATH,
  UV_FS_COPYFILE,
  UV_FS_LCHOWN,
  UV_FS_OPENDIR,
  UV_FS_READDIR,
  UV_FS_CLOSEDIR,
  UV_FS_STATFS,
  UV_FS_MKSTEMP,
  UV_FS_LUTIME
} uv_fs_type;

struct uv_dir_s
{
  uv_dirent_t *dirents;
  size_t nentries;
  void *reserved[4];
  DIR *dir;
};

struct uv_fs_s
{
  void *data;
  uv_req_type type;
  void *reserved[6];
  uv_fs_type fs_type;
  uv_loop_t *loop;
  uv_fs_cb cb;
  ssize_t result;
  void *ptr;
  const char *path;
  uv_stat_t statbuf;
  const char *new_path;
  uv_file file;
  int flags;
  mode_t mode;
  unsigned int nbufs;
  uv_buf_t *bufs;
  off_t off;
  uv_uid_t uid;
  uv_gid_t gid;
  double atime;
  double mtime;
  struct uv__work work_req;
  uv_buf_t bufsml[4];
};

uv_fs_type uv_fs_get_type(const uv_fs_t *);
ssize_t uv_fs_get_result(const uv_fs_t *);
int uv_fs_get_system_error(const uv_fs_t *);
void *uv_fs_get_ptr(const uv_fs_t *);
const char *uv_fs_get_path(const uv_fs_t *);
uv_stat_t *uv_fs_get_statbuf(uv_fs_t *);
void uv_fs_req_cleanup(uv_fs_t *req);
int uv_fs_close(uv_loop_t *loop,
                uv_fs_t *req,
                uv_file file,
                uv_fs_cb cb);
int uv_fs_open(uv_loop_t *loop,
               uv_fs_t *req,
               const char *path,
               int flags,
               int mode,
               uv_fs_cb cb);
int uv_fs_read(uv_loop_t *loop,
               uv_fs_t *req,
               uv_file file,
               const uv_buf_t bufs[],
               unsigned int nbufs,
               int64_t offset,
               uv_fs_cb cb);
int uv_fs_unlink(uv_loop_t *loop,
                 uv_fs_t *req,
                 const char *path,
                 uv_fs_cb cb);
int uv_fs_write(uv_loop_t *loop,
                uv_fs_t *req,
                uv_file file,
                const uv_buf_t bufs[],
                unsigned int nbufs,
                int64_t offset,
                uv_fs_cb cb);
int uv_fs_copyfile(uv_loop_t *loop,
                   uv_fs_t *req,
                   const char *path,
                   const char *new_path,
                   int flags,
                   uv_fs_cb cb);
int uv_fs_mkdir(uv_loop_t *loop,
                uv_fs_t *req,
                const char *path,
                int mode,
                uv_fs_cb cb);
int uv_fs_mkdtemp(uv_loop_t *loop,
                  uv_fs_t *req,
                  const char *tpl,
                  uv_fs_cb cb);
int uv_fs_mkstemp(uv_loop_t *loop,
                  uv_fs_t *req,
                  const char *tpl,
                  uv_fs_cb cb);
int uv_fs_rmdir(uv_loop_t *loop,
                uv_fs_t *req,
                const char *path,
                uv_fs_cb cb);
int uv_fs_scandir(uv_loop_t *loop,
                  uv_fs_t *req,
                  const char *path,
                  int flags,
                  uv_fs_cb cb);
int uv_fs_scandir_next(uv_fs_t *req,
                       uv_dirent_t *ent);
int uv_fs_opendir(uv_loop_t *loop,
                  uv_fs_t *req,
                  const char *path,
                  uv_fs_cb cb);
int uv_fs_readdir(uv_loop_t *loop,
                  uv_fs_t *req,
                  uv_dir_t *dir,
                  uv_fs_cb cb);
int uv_fs_closedir(uv_loop_t *loop,
                   uv_fs_t *req,
                   uv_dir_t *dir,
                   uv_fs_cb cb);
int uv_fs_stat(uv_loop_t *loop,
               uv_fs_t *req,
               const char *path,
               uv_fs_cb cb);
int uv_fs_fstat(uv_loop_t *loop,
                uv_fs_t *req,
                uv_file file,
                uv_fs_cb cb);
int uv_fs_rename(uv_loop_t *loop,
                 uv_fs_t *req,
                 const char *path,
                 const char *new_path,
                 uv_fs_cb cb);
int uv_fs_fsync(uv_loop_t *loop,
                uv_fs_t *req,
                uv_file file,
                uv_fs_cb cb);
int uv_fs_fdatasync(uv_loop_t *loop,
                    uv_fs_t *req,
                    uv_file file,
                    uv_fs_cb cb);
int uv_fs_ftruncate(uv_loop_t *loop,
                    uv_fs_t *req,
                    uv_file file,
                    int64_t offset,
                    uv_fs_cb cb);
int uv_fs_sendfile(uv_loop_t *loop,
                   uv_fs_t *req,
                   uv_file out_fd,
                   uv_file in_fd,
                   int64_t in_offset,
                   size_t length,
                   uv_fs_cb cb);
int uv_fs_access(uv_loop_t *loop,
                 uv_fs_t *req,
                 const char *path,
                 int mode,
                 uv_fs_cb cb);
int uv_fs_chmod(uv_loop_t *loop,
                uv_fs_t *req,
                const char *path,
                int mode,
                uv_fs_cb cb);
int uv_fs_utime(uv_loop_t *loop,
                uv_fs_t *req,
                const char *path,
                double atime,
                double mtime,
                uv_fs_cb cb);
int uv_fs_futime(uv_loop_t *loop,
                 uv_fs_t *req,
                 uv_file file,
                 double atime,
                 double mtime,
                 uv_fs_cb cb);
int uv_fs_lutime(uv_loop_t *loop,
                 uv_fs_t *req,
                 const char *path,
                 double atime,
                 double mtime,
                 uv_fs_cb cb);
int uv_fs_lstat(uv_loop_t *loop,
                uv_fs_t *req,
                const char *path,
                uv_fs_cb cb);
int uv_fs_link(uv_loop_t *loop,
               uv_fs_t *req,
               const char *path,
               const char *new_path,
               uv_fs_cb cb);
int uv_fs_symlink(uv_loop_t *loop,
                  uv_fs_t *req,
                  const char *path,
                  const char *new_path,
                  int flags,
                  uv_fs_cb cb);
int uv_fs_readlink(uv_loop_t *loop,
                   uv_fs_t *req,
                   const char *path,
                   uv_fs_cb cb);
int uv_fs_realpath(uv_loop_t *loop,
                   uv_fs_t *req,
                   const char *path,
                   uv_fs_cb cb);
int uv_fs_fchmod(uv_loop_t *loop,
                 uv_fs_t *req,
                 uv_file file,
                 int mode,
                 uv_fs_cb cb);
int uv_fs_chown(uv_loop_t *loop,
                uv_fs_t *req,
                const char *path,
                uv_uid_t uid,
                uv_gid_t gid,
                uv_fs_cb cb);
int uv_fs_fchown(uv_loop_t *loop,
                 uv_fs_t *req,
                 uv_file file,
                 uv_uid_t uid,
                 uv_gid_t gid,
                 uv_fs_cb cb);
int uv_fs_lchown(uv_loop_t *loop,
                 uv_fs_t *req,
                 const char *path,
                 uv_uid_t uid,
                 uv_gid_t gid,
                 uv_fs_cb cb);
int uv_fs_statfs(uv_loop_t *loop,
                 uv_fs_t *req,
                 const char *path,
                 uv_fs_cb cb);
enum uv_fs_event
{
  UV_RENAME = 1,
  UV_CHANGE = 2
};

struct uv_fs_event_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  char *path;
  uv_fs_event_cb cb;
  void *watchers[2];
  int wd;
};

struct uv_fs_poll_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  void *poll_ctx;
};

int uv_fs_poll_init(uv_loop_t *loop, uv_fs_poll_t *handle);
int uv_fs_poll_start(uv_fs_poll_t *handle,
                     uv_fs_poll_cb poll_cb,
                     const char *path,
                     unsigned int interval);
int uv_fs_poll_stop(uv_fs_poll_t *handle);
int uv_fs_poll_getpath(uv_fs_poll_t *handle,
                       char *buffer,
                       size_t *size);

struct uv_signal_s
{
  void *data;
  uv_loop_t *loop;
  uv_handle_type type;
  uv_close_cb close_cb;
  void *handle_queue[2];
  union
  {
    int fd;
    void *reserved[4];
  } u;
  uv_handle_t *next_closing;
  unsigned int flags;
  uv_signal_cb signal_cb;
  int signum;
  struct
  {
    struct uv_signal_s *rbe_left;
    struct uv_signal_s *rbe_right;
    struct uv_signal_s *rbe_parent;
    int rbe_color;
  } tree_entry;
  unsigned int caught_signals;
  unsigned int dispatched_signals;
};

int uv_signal_init(uv_loop_t *loop, uv_signal_t *handle);
int uv_signal_start(uv_signal_t *handle,
                    uv_signal_cb signal_cb,
                    int signum);
int uv_signal_start_oneshot(uv_signal_t *handle,
                            uv_signal_cb signal_cb,
                            int signum);
int uv_signal_stop(uv_signal_t *handle);
void uv_loadavg(double avg[3]);
enum uv_fs_event_flags
{
  UV_FS_EVENT_WATCH_ENTRY = 1,
  UV_FS_EVENT_STAT = 2,
  UV_FS_EVENT_RECURSIVE = 4
};
int uv_fs_event_init(uv_loop_t *loop, uv_fs_event_t *handle);
int uv_fs_event_start(uv_fs_event_t *handle,
                      uv_fs_event_cb cb,
                      const char *path,
                      unsigned int flags);
int uv_fs_event_stop(uv_fs_event_t *handle);
int uv_fs_event_getpath(uv_fs_event_t *handle,
                        char *buffer,
                        size_t *size);
int uv_ip4_addr(const char *ip, int port, struct sockaddr_in *addr);
int uv_ip6_addr(const char *ip, int port, struct sockaddr_in6 *addr);
int uv_ip4_name(const struct sockaddr_in *src, char *dst, size_t size);
int uv_ip6_name(const struct sockaddr_in6 *src, char *dst, size_t size);
int uv_ip_name(const struct sockaddr *src, char *dst, size_t size);
int uv_inet_ntop(int af, const void *src, char *dst, size_t size);
int uv_inet_pton(int af, const char *src, void *dst);

struct uv_random_s
{
  void *data;
  uv_req_type type;
  void *reserved[6];
  uv_loop_t *loop;
  int status;
  void *buf;
  size_t buflen;
  uv_random_cb cb;
  struct uv__work work_req;
};

int uv_random(uv_loop_t *loop,
              uv_random_t *req,
              void *buf,
              size_t buflen,
              unsigned flags,
              uv_random_cb cb);
int uv_if_indextoname(unsigned int ifindex,
                      char *buffer,
                      size_t *size);
int uv_if_indextoiid(unsigned int ifindex,
                     char *buffer,
                     size_t *size);
int uv_exepath(char *buffer, size_t *size);
int uv_cwd(char *buffer, size_t *size);
int uv_chdir(const char *dir);
uint64_t uv_get_free_memory(void);
uint64_t uv_get_total_memory(void);
uint64_t uv_get_constrained_memory(void);
uint64_t uv_hrtime(void);
void uv_sleep(unsigned int msec);
void uv_disable_stdio_inheritance(void);
int uv_dlopen(const char *filename, uv_lib_t *lib);
void uv_dlclose(uv_lib_t *lib);
int uv_dlsym(uv_lib_t *lib, const char *name, void **ptr);
const char *uv_dlerror(const uv_lib_t *lib);
int uv_mutex_init(uv_mutex_t *handle);
int uv_mutex_init_recursive(uv_mutex_t *handle);
void uv_mutex_destroy(uv_mutex_t *handle);
void uv_mutex_lock(uv_mutex_t *handle);
int uv_mutex_trylock(uv_mutex_t *handle);
void uv_mutex_unlock(uv_mutex_t *handle);
int uv_rwlock_init(uv_rwlock_t *rwlock);
void uv_rwlock_destroy(uv_rwlock_t *rwlock);
void uv_rwlock_rdlock(uv_rwlock_t *rwlock);
int uv_rwlock_tryrdlock(uv_rwlock_t *rwlock);
void uv_rwlock_rdunlock(uv_rwlock_t *rwlock);
void uv_rwlock_wrlock(uv_rwlock_t *rwlock);
int uv_rwlock_trywrlock(uv_rwlock_t *rwlock);
void uv_rwlock_wrunlock(uv_rwlock_t *rwlock);
int uv_sem_init(uv_sem_t *sem, unsigned int value);
void uv_sem_destroy(uv_sem_t *sem);
void uv_sem_post(uv_sem_t *sem);
void uv_sem_wait(uv_sem_t *sem);
int uv_sem_trywait(uv_sem_t *sem);
int uv_cond_init(uv_cond_t *cond);
void uv_cond_destroy(uv_cond_t *cond);
void uv_cond_signal(uv_cond_t *cond);
void uv_cond_broadcast(uv_cond_t *cond);
int uv_barrier_init(uv_barrier_t *barrier, unsigned int count);
void uv_barrier_destroy(uv_barrier_t *barrier);
int uv_barrier_wait(uv_barrier_t *barrier);
void uv_cond_wait(uv_cond_t *cond, uv_mutex_t *mutex);
int uv_cond_timedwait(uv_cond_t *cond,
                      uv_mutex_t *mutex,
                      uint64_t timeout);
void uv_once(uv_once_t *guard, void (*callback)(void));
int uv_key_create(uv_key_t *key);
void uv_key_delete(uv_key_t *key);
void *uv_key_get(uv_key_t *key);
void uv_key_set(uv_key_t *key, void *value);
int uv_gettimeofday(uv_timeval64_t *tv);
typedef void (*uv_thread_cb)(void *arg);
int uv_thread_create(uv_thread_t *tid, uv_thread_cb entry, void *arg);
typedef enum
{
  UV_THREAD_NO_FLAGS = 0x00,
  UV_THREAD_HAS_STACK_SIZE = 0x01
} uv_thread_create_flags;
struct uv_thread_options_s
{
  unsigned int flags;
  size_t stack_size;
};
typedef struct uv_thread_options_s uv_thread_options_t;
int uv_thread_create_ex(uv_thread_t *tid,
                        const uv_thread_options_t *params,
                        uv_thread_cb entry,
                        void *arg);
uv_thread_t uv_thread_self(void);
int uv_thread_join(uv_thread_t *tid);
int uv_thread_equal(const uv_thread_t *t1, const uv_thread_t *t2);
union uv_any_handle
{
  uv_async_t async;
  uv_check_t check;
  uv_fs_event_t fs_event;
  uv_fs_poll_t fs_poll;
  uv_handle_t handle;
  uv_idle_t idle;
  uv_pipe_t pipe;
  uv_poll_t poll;
  uv_prepare_t prepare;
  uv_process_t process;
  uv_stream_t stream;
  uv_tcp_t tcp;
  uv_timer_t timer;
  uv_tty_t tty;
  uv_udp_t udp;
  uv_signal_t signal;
};
union uv_any_req
{
  uv_req_t req;
  uv_connect_t connect;
  uv_write_t write;
  uv_shutdown_t shutdown;
  uv_udp_send_t udp_send;
  uv_fs_t fs;
  uv_work_t work;
  uv_getaddrinfo_t getaddrinfo;
  uv_getnameinfo_t getnameinfo;
  uv_random_t random;
};

struct uv_loop_s
{
  void *data;
  unsigned int active_handles;
  void *handle_queue[2];
  union
  {
    void *unused;
    unsigned int count;
  } active_reqs;
  void *internal_fields;
  unsigned int stop_flag;
  // Linux only needs changing for macOS and posix
  unsigned long flags;
  int backend_fd;
  void *pending_queue[2];
  void *watcher_queue[2];
  uv__io_t **watchers;
  unsigned int nwatchers;
  unsigned int nfds;
  void *wq[2];
  uv_mutex_t wq_mutex;
  uv_async_t wq_async;
  uv_rwlock_t cloexec_lock;
  uv_handle_t *closing_handles;
  void *process_handles[2];
  void *prepare_handles[2];
  void *check_handles[2];
  void *idle_handles[2];
  void *async_handles[2];
  void (*async_unused)(void);
  uv__io_t async_io_watcher;
  int async_wfd;
  struct
  {
    void *min;
    unsigned int nelts;
  } timer_heap;
  uint64_t timer_counter;
  uint64_t time;
  int signal_pipefd[2];
  uv__io_t signal_io_watcher;
  uv_signal_t child_watcher;
  int emfile_fd;
  uv__io_t inotify_read_watcher;
  void *inotify_watchers;
  int inotify_fd;
};

void *uv_loop_get_data(const uv_loop_t *);
void uv_loop_set_data(uv_loop_t *, void *data);

typedef void *void_t;
typedef struct _php_uv_s
{
  void_t std; // for casting/storage of zval class objects

  int type;
  // for threading
  void ***thread_ctx;

  uv_os_sock_t sock;
  union
  {
    uv_tcp_t tcp;
    uv_udp_t udp;
    uv_pipe_t pipe;
    uv_idle_t idle;
    uv_timer_t timer;
    uv_async_t async;
    uv_handle_t handle;
    uv_stream_t stream;
    uv_prepare_t prepare;
    uv_check_t check;
    uv_process_t process;
    uv_fs_event_t fs_event;
    uv_tty_t tty;
    uv_poll_t poll;
    uv_signal_t signal;
  } uv;
} php_uv_t;

enum php_uv_lock_type
{
  IS_UV_RWLOCK = 1,
  IS_UV_RWLOCK_RD = 2,
  IS_UV_RWLOCK_WR = 3,
  IS_UV_MUTEX = 4,
  IS_UV_SEMAPHORE = 5,
};

typedef struct _php_uv_lock_s
{
  void_t std;

  int locked;
  enum php_uv_lock_type type;
  union
  {
    uv_rwlock_t rwlock;
    uv_mutex_t mutex;
    uv_sem_t semaphore;
  } lock;
} php_uv_lock_t;

typedef struct _php_uv_loop_t
{
  void_t std;
  uv_loop_t loop;
} php_uv_loop_t;

typedef struct _zend_uv_globals
{
  php_uv_loop_t default_loop;
} zend_uv_globals;

typedef zend_uv_globals uv_globals;
