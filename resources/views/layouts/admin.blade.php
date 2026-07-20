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
  --dark:#152B0C;
  --dark2:#1B3812;
  --primary:#3F5A2A;
  --secondary:#8BC34A;
  --bg:#F3F6F2;
  --card:#FFFFFF;
  --border:#E5E7EB;
  --text:#1F2937;
  --sub:#6B7280;
  --success:#22C55E;
  --warning:#F59E0B;
  --danger:#EF4444;
}
*{box-sizing:border-box;}
body{margin:0; background:#E7EAE5; color:var(--text); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased;}
.font-display{font-family:'Poppins',sans-serif;}
a{text-decoration:none; color:inherit;}
::-webkit-scrollbar{width:8px;height:8px;}
::-webkit-scrollbar-thumb{background:#d7ddd9;border-radius:8px;}

html, body{height:100%;}
.page-wrap{width:100%; margin:0; padding:0;}
.shell{background:var(--bg); min-height:100vh; display:flex; flex-direction:column;}

/* ===== Topbar ===== */
.topbar{
  height:76px; background:#fff; border-bottom:1px solid var(--border);
  display:flex; align-items:center; justify-content:space-between; padding:0 28px;
}
.brand{display:flex; align-items:center; gap:10px;}
.brand-icon{
  width:38px; height:38px; border-radius:10px; background:linear-gradient(135deg, var(--secondary), var(--primary));
  display:flex; align-items:center; justify-content:center; flex-shrink:0; color:#fff; font-weight:800; font-family:'Poppins',sans-serif;
}
.brand-name{font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:var(--dark);}

.tabs{display:flex; align-items:center; gap:4px; background:#F3F6F2; border-radius:99px; padding:4px;}
.tab{padding:9px 18px; border-radius:99px; font-size:13.5px; font-weight:600; color:var(--sub); cursor:pointer;}
.tab.active{background:var(--dark); color:#fff;}
.tab:hover:not(.active){color:var(--text);}

.top-right{display:flex; align-items:center; gap:12px;}
.icon-btn{width:38px; height:38px; border-radius:10px; background:#F3F6F2; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--sub); position:relative;}
.icon-btn:hover{background:#E7F3DE; color:var(--primary);}
.icon-btn .dot{position:absolute; top:6px; right:6px; width:7px; height:7px; border-radius:50%; background:var(--danger);}
.user-chip{display:flex; align-items:center; gap:10px; padding:5px 10px 5px 5px; border-radius:12px; cursor:pointer;}
.user-chip:hover{background:#F3F6F2;}
.user-avatar{width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg, var(--secondary), var(--primary)); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:13px; font-family:'Poppins',sans-serif;}
.user-name{font-size:13px; font-weight:600; color:var(--text); line-height:1.3;}
.user-email{font-size:11px; color:var(--sub);}

/* ===== Shell body ===== */
.shell-body{display:flex; flex:1; min-height:0;}

.sidebar{width:76px; background:#fff; border-right:1px solid var(--border); flex-shrink:0; display:flex; flex-direction:column; align-items:center; padding:20px 0; gap:8px;}
.sb-icon{
  width:46px; height:46px; border-radius:14px; display:flex; align-items:center; justify-content:center; cursor:pointer;
  color:var(--sub); position:relative;
}
.sb-icon:hover{background:#F1F4F1; color:var(--primary);}
.sb-icon.active{background:var(--dark); color:#fff;}
.sb-icon .sb-tooltip{
  position:absolute; left:58px; top:50%; transform:translateY(-50%); background:var(--dark); color:#fff; font-size:11.5px;
  font-weight:600; padding:7px 11px; border-radius:8px; white-space:nowrap; opacity:0; pointer-events:none; transition:opacity .12s ease;
}
.sb-icon:hover .sb-tooltip{opacity:1;}
.sb-spacer{flex:1;}

.content{flex:1; min-width:0; padding:30px 32px 40px;}

.page-head{margin-bottom:26px;}
.page-title{font-family:'Poppins',sans-serif; font-weight:700; font-size:26px; color:var(--dark); margin:0 0 4px;}
.page-sub{font-size:13.5px; color:var(--sub); margin:0;}

.card{background:#fff; border:1px solid var(--border); border-radius:16px;}

.btn{display:inline-flex; align-items:center; gap:8px; padding:11px 18px; border-radius:10px; font-size:13.5px; font-weight:600; cursor:pointer; border:1px solid var(--border); background:#fff; color:var(--text);}
.btn:hover{background:#F6F8F7;}
.btn-primary{background:var(--dark); color:#fff; border-color:var(--dark);}
.btn-primary:hover{background:var(--dark2);}
.btn-outline{background:transparent; border:1px solid var(--dark); color:var(--dark);}
.btn-outline:hover{background:#F1F4F1;}

.badge{display:inline-flex; align-items:center; gap:6px; font-size:11.5px; font-weight:700; padding:4px 10px; border-radius:99px;}
.badge-green{background:#E7F3DE; color:var(--primary);}
.badge-orange{background:#FDECD8; color:#C2660B;}
.badge-red{background:#FBDADA; color:var(--danger);}

.form-field{margin-bottom:18px;}
.form-field label{display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:6px;}
.form-field input,
.form-field select,
.form-field textarea{
  width:100%; padding:11px 14px; border:1px solid var(--border); border-radius:10px;
  font-size:13.5px; font-family:'Inter',sans-serif; outline:none; background:#fff; color:var(--text);
}
.form-field input:focus, .form-field select:focus, .form-field textarea:focus{border-color:var(--secondary); box-shadow:0 0 0 3px rgba(139,195,74,.18);}
.field-error{color:var(--danger); font-size:12px; margin-top:6px;}
.form-hint{font-size:12px; color:var(--sub); margin-top:6px;}

.alert{padding:12px 14px; border-radius:10px; font-size:13px; margin-bottom:18px;}
.alert-success{background:#E7F3DE; color:var(--primary);}
.alert-error{background:#FBDADA; color:var(--danger);}
.alert-warning{background:#FEF3D6; color:#92620A; border:1px solid #F5D77E;}

/* ===== Notification bell dropdown ===== */
.notif-wrap{position:relative;}
.notif-panel{
  position:absolute; top:48px; right:0; width:360px; max-height:420px; overflow-y:auto;
  background:#fff; border:1px solid var(--border); border-radius:14px; box-shadow:0 12px 32px rgba(21,43,12,.16);
  z-index:50; display:none;
}
.notif-panel.open{display:block;}
.notif-head{padding:14px 16px; border-bottom:1px solid var(--border); font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; display:flex; align-items:center; justify-content:space-between;}
.notif-item{padding:12px 16px; border-bottom:1px solid #F1F2F0; display:flex; gap:10px; align-items:flex-start;}
.notif-item:last-child{border-bottom:none;}
.notif-dot{width:8px; height:8px; border-radius:50%; background:var(--secondary); margin-top:5px; flex-shrink:0;}
.notif-dot.warn{background:var(--danger);}
.notif-text{font-size:12.5px; color:var(--text); line-height:1.45;}
.notif-time{font-size:11px; color:var(--sub); margin-top:2px;}
.notif-empty{padding:26px 16px; text-align:center; color:var(--sub); font-size:12.5px;}

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
        <div class="brand-icon">B</div>
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
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
        @php
          $__navNotifications = \App\Models\ActivityLog::with('user')->latest()->take(8)->get();
          $__navUnread = $__navNotifications->whereNull('read_at')->count();
        @endphp
        <div class="notif-wrap">
          <div class="icon-btn" title="Notifications" onclick="document.getElementById('notif-panel').classList.toggle('open')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
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
