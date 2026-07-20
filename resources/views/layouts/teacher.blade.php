<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Teacher') — BANTAY</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --dark:#1A1A1A; --dark2:#111111; --primary:#FF7A3D; --primary2:#E8672A; --secondary:#FFB088;
  --bg:#ECECEA; --frame:#F6F6F4; --border:#ECECEC; --text:#1A1A1A; --sub:#8A8A8E; --warning:#F5A623; --danger:#E5484D;
}
*{box-sizing:border-box;}
body{margin:0; background:var(--bg); color:var(--text); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased;}
.font-display{font-family:'Poppins',sans-serif;}
::-webkit-scrollbar{width:8px;height:8px;}
::-webkit-scrollbar-thumb{background:#dcdcda;border-radius:8px;}

.fusion-frame{
  max-width:1400px; margin:0 auto; padding:16px 24px 30px;
}

/* ===== Topbar (pill nav, Fusion-style) ===== */
.fusion-topbar{display:flex; align-items:center; justify-content:space-between; gap:14px; padding:10px 0 20px;}
.fusion-brand{
  display:flex; align-items:center; gap:8px; background:#fff; border-radius:999px; padding:8px 16px 8px 10px;
  font-family:'Poppins',sans-serif; font-weight:800; font-size:13.5px; color:var(--dark); box-shadow:0 1px 2px rgba(0,0,0,.04);
}
.fusion-brand-icon{
  width:24px; height:24px; border-radius:7px; background:linear-gradient(135deg, var(--primary), var(--primary2));
  display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:800;
}
.fusion-nav{display:flex; align-items:center; gap:2px; background:#fff; border-radius:999px; padding:5px; box-shadow:0 1px 2px rgba(0,0,0,.04);}
.fusion-nav a{
  display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:999px; font-size:12.5px; font-weight:600;
  color:var(--sub); text-decoration:none;
}
.fusion-nav a svg{width:14px; height:14px;}
.fusion-nav a.active{background:var(--dark); color:#fff;}
.fusion-nav a:hover:not(.active){color:var(--text);}

.fusion-actions{display:flex; align-items:center; gap:8px;}
.fusion-icon-btn{
  width:38px; height:38px; border-radius:50%; background:#fff; border:none; display:flex; align-items:center; justify-content:center;
  cursor:pointer; color:var(--text); position:relative; box-shadow:0 1px 2px rgba(0,0,0,.04);
}
.fusion-icon-btn .dot{position:absolute; top:8px; right:9px; width:7px; height:7px; border-radius:50%; background:var(--danger); border:1.5px solid #fff;}
.fusion-avatar{
  width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg, var(--secondary), var(--primary2));
  color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; font-family:'Poppins',sans-serif;
}

/* Compact content */
.content{padding:0;}
.welcome-row{display:flex; align-items:baseline; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:8px;}
.page-head{margin-bottom:16px;}
.welcome{font-family:'Poppins',sans-serif; font-weight:700; font-size:19px;}
.sub{color:var(--sub); font-size:12.5px;}

.grid{display:grid; grid-template-columns:repeat(4, 1fr); gap:12px; margin-bottom:14px;}
.stat{background:#fff; border:none; border-radius:18px; padding:15px 16px; box-shadow:0 1px 2px rgba(0,0,0,.03);}
.stat-label{font-size:10.5px; font-weight:600; color:var(--sub); text-transform:uppercase; letter-spacing:.03em;}
.stat-value{font-family:'Poppins',sans-serif; font-weight:700; font-size:20px; margin-top:5px; color:var(--dark);}
.stat-hint{font-size:10.5px; color:var(--sub); margin-top:2px;}

.panels{display:grid; grid-template-columns:1.15fr 1fr; gap:12px; align-items:start;}
.card{background:#fff; border:none; border-radius:20px; box-shadow:0 1px 3px rgba(0,0,0,.04);}
.card-head{padding:14px 18px; border-bottom:1px solid #F2F2F0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; display:flex; align-items:center; justify-content:space-between;}
.card-body{padding:5px 0;}

.assign-row{display:flex; align-items:center; justify-content:space-between; padding:9px 18px; font-size:12.5px; border-bottom:1px solid #F5F5F3;}
.assign-row:last-child{border-bottom:none;}
.assign-row span:first-child{color:var(--sub);}
.assign-row span:last-child{font-weight:700; color:var(--text);}
.badge-mini{background:#FFE9DD; color:var(--primary2); font-size:10px; font-weight:700; padding:3px 9px; border-radius:99px;}

.badge{display:inline-block; padding:4px 10px; border-radius:99px; font-size:10.5px; font-weight:700;}
.badge-green{background:#DFF3E3; color:#1F8A45;}
.badge-orange{background:#FFE9DD; color:var(--primary2);}
.badge-red{background:#FBE1E1; color:var(--danger);}

.ann-item{padding:11px 18px; border-bottom:1px solid #F5F5F3;}
.ann-item:last-child{border-bottom:none;}
.ann-title{font-size:12.5px; font-weight:700; color:var(--text);}
.ann-body{font-size:11.5px; color:var(--sub); margin-top:2px; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;}
.ann-meta{font-size:10.5px; color:#B3B3AF; margin-top:5px;}
.empty{padding:22px 18px; text-align:center; color:var(--sub); font-size:12px;}

.btn{
  display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:999px;
  border:none; background:#F1F1EF; color:var(--text); font-size:12px; font-weight:600;
  text-decoration:none; cursor:pointer;
}
.btn:hover{background:#E7E7E4;}
.btn-primary{background:var(--dark); color:#fff;}
.btn-primary:hover{background:var(--dark2);}
.btn-outline{background:#fff; box-shadow:0 1px 2px rgba(0,0,0,.04);}

.form-field{margin-bottom:11px;}
.form-field label{display:block; font-size:11px; font-weight:600; color:var(--sub); margin-bottom:4px;}
.form-field input, .form-field select{
  width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:12px; font-size:12.5px; font-family:inherit; background:#FAFAF9;
}
.form-field input:focus, .form-field select:focus{outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(255,122,61,.15);}

.alert{padding:11px 16px; border-radius:14px; font-size:12px; margin-bottom:14px;}
.alert-success{background:#DFF3E3; color:#1F6B36;}
.alert-warning{background:#FFF0D6; color:#8A5B00;}

table.tbl{width:100%; border-collapse:collapse; font-size:12.5px;}
table.tbl thead th{text-align:left; padding:11px 18px; font-size:10px; text-transform:uppercase; letter-spacing:.03em; color:var(--sub); font-weight:700; border-bottom:1px solid #F2F2F0;}
table.tbl tbody td{padding:11px 18px; border-bottom:1px solid #F5F5F3;}
table.tbl tbody tr:last-child td{border-bottom:none;}
table.tbl tbody tr:hover{background:#FAFAF9;}

@media (max-width:900px){
  .fusion-nav{display:none;}
}
@media (max-width:760px){
  .grid{grid-template-columns:repeat(2, 1fr);}
  .panels{grid-template-columns:1fr;}
}
</style>
@yield('head')
</head>
<body>
<div class="fusion-frame">

  <div class="fusion-topbar">
    <div class="fusion-brand"><span class="fusion-brand-icon">B</span> BANTAY</div>

    <div class="fusion-nav">
      <a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v10h14V10"/></svg>
        Dashboard
      </a>
      <a href="{{ route('teacher.attendance.index') }}" class="{{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
        Attendance
      </a>
    </div>

    <div class="fusion-actions">
      <div class="fusion-icon-btn" title="Notifications">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        <span class="dot"></span>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="fusion-icon-btn" title="Log out">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </button>
      </form>
      <div class="fusion-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'T', 0, 1)) }}</div>
    </div>
  </div>

  <div class="content">
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('warning'))
      <div class="alert alert-warning">⚠️ {{ session('warning') }}</div>
    @endif

    @yield('content')
  </div>

</div>
@yield('scripts')
</body>
</html>