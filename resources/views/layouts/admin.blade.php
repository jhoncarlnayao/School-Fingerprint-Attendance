<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Dashboard') — BANTAY Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --dark:#1A1A1A;
  --dark2:#111111;
  --primary:#FF7A3D;
  --primary2:#E8672A;
  --secondary:#FFB088;
  --bg:#ECECEA;
  --card:#FFFFFF;
  --border:#ECECEC;
  --text:#1A1A1A;
  --sub:#8A8A8E;
  --success:#22C55E;
  --warning:#F5A623;
  --danger:#E5484D;
}
*{box-sizing:border-box;}
body{margin:0; background:var(--bg); color:var(--text); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased;}
.font-display{font-family:'Poppins',sans-serif;}
a{text-decoration:none; color:inherit;}
::-webkit-scrollbar{width:8px;height:8px;}
::-webkit-scrollbar-thumb{background:#dcdcda;border-radius:8px;}

html, body{height:100%;}
.page-wrap{width:100%; margin:0; padding:0;}
.shell{background:var(--bg); min-height:100vh; display:flex; flex-direction:column;}

/* ===== Topbar ===== */
.topbar{
  height:60px; background:#fff; border-bottom:1px solid var(--border);
  display:flex; align-items:center; justify-content:space-between; padding:0 22px;
}
.brand{display:flex; align-items:center; gap:8px;}
.brand-icon{
  width:32px; height:32px; border-radius:9px; background:linear-gradient(135deg, var(--primary), var(--primary2));
  display:flex; align-items:center; justify-content:center; flex-shrink:0; color:#fff; font-weight:800; font-family:'Poppins',sans-serif; font-size:13px;
}
.brand-icon svg{width:17px; height:17px;}
.brand-name{font-family:'Poppins',sans-serif; font-weight:800; font-size:14.5px; color:var(--dark);}

.tabs{display:flex; align-items:center; gap:2px; background:#F1F1EF; border-radius:99px; padding:4px;}
.tab{padding:8px 15px; border-radius:99px; font-size:12px; font-weight:600; color:var(--sub); cursor:pointer;}
.tab.active{background:var(--dark); color:#fff;}
.tab:hover:not(.active){color:var(--text);}

.top-right{display:flex; align-items:center; gap:8px;}
.icon-btn{width:36px; height:36px; border-radius:50%; background:#F1F1EF; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--sub); position:relative;}
.icon-btn:hover{background:#FFE9DD; color:var(--primary2);}
.icon-btn .dot{position:absolute; top:6px; right:7px; width:6px; height:6px; border-radius:50%; background:var(--danger); border:1.5px solid #fff;}
.user-chip{display:flex; align-items:center; gap:8px; padding:4px 10px 4px 4px; border-radius:99px; cursor:pointer;}
.user-chip:hover{background:#F1F1EF;}
.user-avatar{width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg, var(--secondary), var(--primary2)); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:12px; font-family:'Poppins',sans-serif;}
.user-name{font-size:12px; font-weight:600; color:var(--text); line-height:1.25;}
.user-email{font-size:10.5px; color:var(--sub);}

/* ===== Shell body ===== */
.shell-body{display:flex; flex:1; min-height:0;}

.sidebar{width:64px; background:#fff; border-right:1px solid var(--border); flex-shrink:0; display:flex; flex-direction:column; align-items:center; padding:16px 0; gap:6px;}
.sb-icon{
  width:40px; height:40px; border-radius:13px; display:flex; align-items:center; justify-content:center; cursor:pointer;
  color:var(--sub); position:relative;
}
.sb-icon svg{width:17px; height:17px;}
.sb-icon:hover{background:#F6F6F4; color:var(--primary2);}
.sb-icon.active{background:var(--dark); color:#fff;}
.sb-icon .sb-tooltip{
  position:absolute; left:50px; top:50%; transform:translateY(-50%); background:var(--dark); color:#fff; font-size:11px;
  font-weight:600; padding:6px 10px; border-radius:8px; white-space:nowrap; opacity:0; pointer-events:none; transition:opacity .12s ease; z-index:40;
}
.sb-icon:hover .sb-tooltip{opacity:1;}
.sb-spacer{flex:1;}

.content{flex:1; min-width:0; padding:22px 24px 32px;}

.page-head{margin-bottom:18px;}
.page-title{font-family:'Poppins',sans-serif; font-weight:700; font-size:21px; color:var(--dark); margin:0 0 3px;}
.page-sub{font-size:12.5px; color:var(--sub); margin:0;}

.card{background:#fff; border:none; border-radius:20px; box-shadow:0 1px 3px rgba(0,0,0,.04);}

.btn{display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:999px; font-size:12.5px; font-weight:600; cursor:pointer; border:none; background:#F1F1EF; color:var(--text);}
.btn:hover{background:#E7E7E4;}
.btn-primary{background:var(--dark); color:#fff;}
.btn-primary:hover{background:var(--dark2);}
.btn-outline{background:#fff; box-shadow:0 1px 2px rgba(0,0,0,.04);}
.btn-outline:hover{background:#FAFAF9;}

.badge{display:inline-flex; align-items:center; gap:5px; font-size:10.5px; font-weight:700; padding:4px 10px; border-radius:99px;}
.badge-green{background:#DFF3E3; color:#1F8A45;}
.badge-orange{background:#FFE9DD; color:var(--primary2);}
.badge-red{background:#FBE1E1; color:var(--danger);}

.form-field{margin-bottom:14px;}
.form-field label{display:block; font-size:12px; font-weight:600; color:var(--text); margin-bottom:5px;}
.form-field input,
.form-field select,
.form-field textarea{
  width:100%; padding:10px 13px; border:1px solid var(--border); border-radius:12px;
  font-size:12.5px; font-family:'Inter',sans-serif; outline:none; background:#FAFAF9; color:var(--text);
}
.form-field input:focus, .form-field select:focus, .form-field textarea:focus{border-color:var(--primary); box-shadow:0 0 0 3px rgba(255,122,61,.15); background:#fff;}
.field-error{color:var(--danger); font-size:11.5px; margin-top:5px;}
.form-hint{font-size:11.5px; color:var(--sub); margin-top:5px;}

.alert{padding:11px 15px; border-radius:14px; font-size:12.5px; margin-bottom:16px;}
.alert-success{background:#DFF3E3; color:#1F6B36;}
.alert-error{background:#FBE1E1; color:var(--danger);}
.alert-warning{background:#FFF0D6; color:#8A5B00;}

table.tbl{width:100%; border-collapse:collapse; font-size:12.5px;}
table.tbl thead th{text-align:left; padding:11px 18px; font-size:10px; text-transform:uppercase; letter-spacing:.03em; color:var(--sub); font-weight:700; border-bottom:1px solid #F2F2F0;}
table.tbl tbody td{padding:11px 18px; border-bottom:1px solid #F5F5F3;}
table.tbl tbody tr:last-child td{border-bottom:none;}
table.tbl tbody tr:hover{background:#FAFAF9;}

/* ===== Notification bell dropdown ===== */
.notif-wrap{position:relative;}
.notif-panel{
  position:absolute; top:46px; right:0; width:340px; max-height:400px; overflow-y:auto;
  background:#fff; border:none; border-radius:18px; box-shadow:0 12px 32px rgba(0,0,0,.12);
  z-index:50; display:none;
}
.notif-panel.open{display:block;}
.notif-head{padding:14px 16px; border-bottom:1px solid #F2F2F0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; display:flex; align-items:center; justify-content:space-between;}
.notif-item{padding:11px 16px; border-bottom:1px solid #F5F5F3; display:flex; gap:10px; align-items:flex-start;}
.notif-item:last-child{border-bottom:none;}
.notif-dot{width:7px; height:7px; border-radius:50%; background:var(--primary); margin-top:5px; flex-shrink:0;}
.notif-dot.warn{background:var(--danger);}
.notif-text{font-size:12px; color:var(--text); line-height:1.45;}
.notif-time{font-size:10.5px; color:var(--sub); margin-top:2px;}
.notif-empty{padding:24px 16px; text-align:center; color:var(--sub); font-size:12px;}

@media (max-width:900px){
  .tabs{display:none;}
}
</style>
@yield('head')
</head>
<body>
<div class="page-wrap">
  <div class="shell">

    <div class="topbar">
      <div class="brand">
        <div class="brand-icon">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L3 6v6c0 5 3.8 8.7 9 10 5.2-1.3 9-5 9-10V6l-9-4z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
            <path d="M9 12l2 2 4-4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="brand-name">BANTAY</div>
      </div>

      <div class="tabs">
        <a class="tab {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Overview</a>
        <a class="tab {{ request()->routeIs('admin.sections.*') ? 'active' : '' }}" href="{{ route('admin.sections.index') }}">Sections</a>
        <a class="tab {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}" href="{{ route('admin.teachers.register') }}">Teachers</a>
        <a class="tab {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="{{ route('admin.students.create') }}">Students</a>
        <a class="tab {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">Attendance</a>
        <a class="tab {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">Announcements</a>
      </div>

      <div class="top-right">
        <div class="icon-btn" title="Search">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
        @php
          $__navNotifications = \App\Models\ActivityLog::with('user')->latest()->take(8)->get();
          $__navUnread = $__navNotifications->whereNull('read_at')->count();
        @endphp
        <div class="notif-wrap">
          <div class="icon-btn" title="Notifications" onclick="document.getElementById('notif-panel').classList.toggle('open')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            @if($__navUnread > 0)<span class="dot"></span>@endif
          </div>
          <div class="notif-panel" id="notif-panel">
            <div class="notif-head">
              <span>Admin Activity</span>
              <span style="font-size:11px; font-weight:600; color:var(--sub);">{{ $__navNotifications->count() }} recent</span>
            </div>
            @forelse ($__navNotifications as $n)
              <div class="notif-item">
                <span class="notif-dot {{ $n->is_warning ? 'warn' : '' }}"></span>
                <div>
                  <div class="notif-text">{{ $n->description }}</div>
                  <div class="notif-time">{{ $n->timeAgo() }}</div>
                </div>
              </div>
            @empty
              <div class="notif-empty">No activity yet.</div>
            @endforelse
          </div>
        </div>
        <div class="user-chip">
          <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
          <div>
            <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
            <div class="user-email">{{ auth()->user()->email ?? '' }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="shell-body">
      <div class="sidebar">
        <a class="sb-icon {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v10h14V10"/></svg>
          <span class="sb-tooltip">Dashboard</span>
        </a>
        <a class="sb-icon {{ request()->routeIs('admin.sections.*') ? 'active' : '' }}" href="{{ route('admin.sections.index') }}">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
          <span class="sb-tooltip">Sections</span>
        </a>
        <a class="sb-icon {{ request()->routeIs('admin.teachers.register') ? 'active' : '' }}" href="{{ route('admin.teachers.register') }}">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
          <span class="sb-tooltip">Register Teacher</span>
        </a>
        <a class="sb-icon {{ request()->routeIs('admin.teachers.assign') ? 'active' : '' }}" href="{{ route('admin.teachers.assign') }}">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 17H4v-2a3 3 0 0 1 3-3h2"/><circle cx="9" cy="7" r="3"/><path d="M15 17h5v-2a3 3 0 0 0-3-3h-2"/><circle cx="17" cy="7" r="3"/><path d="M12 13v4"/></svg>
          <span class="sb-tooltip">Assign Teacher</span>
        </a>
        <a class="sb-icon {{ request()->routeIs('admin.students.create') ? 'active' : '' }}" href="{{ route('admin.students.create') }}">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.5 2.7 3 6 3s6-1.5 6-3v-5"/></svg>
          <span class="sb-tooltip">Add Student</span>
        </a>
        <a class="sb-icon {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
          <span class="sb-tooltip">Attendance</span>
        </a>
        <a class="sb-icon {{ request()->routeIs('admin.announcements.index') || request()->routeIs('admin.announcements.create') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5-5 18-4-8-8-4Z"/></svg>
          <span class="sb-tooltip">Announcement</span>
        </a>

        <div class="sb-spacer"></div>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="sb-icon" type="submit" style="border:none; background:transparent; cursor:pointer;">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span class="sb-tooltip">Log out</span>
          </button>
        </form>
      </div>

      <div class="content">
        @if (session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session('warning'))
          <div class="alert alert-warning">⚠️ {{ session('warning') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        @yield('content')
      </div>
    </div>

  </div>
</div>
<script>
  document.addEventListener('click', function (e) {
    var wrap = document.querySelector('.notif-wrap');
    var panel = document.getElementById('notif-panel');
    if (wrap && panel && panel.classList.contains('open') && !wrap.contains(e.target)) {
      panel.classList.remove('open');
    }
  });
</script>
@yield('scripts')
</body>
</html>