<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Teacher Dashboard — BANTAY</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --dark:#152B0C; --dark2:#1B3812; --primary:#3F5A2A; --secondary:#8BC34A;
  --bg:#F3F6F2; --border:#E5E7EB; --text:#1F2937; --sub:#6B7280; --warning:#F59E0B;
}
*{box-sizing:border-box;}
body{margin:0; background:var(--bg); color:var(--text); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased;}
.font-display{font-family:'Poppins',sans-serif;}

/* Compact topbar */
.topbar{
  height:58px; background:linear-gradient(135deg, var(--dark), var(--dark2));
  display:flex; align-items:center; justify-content:space-between; padding:0 20px;
}
.brand{font-family:'Poppins',sans-serif; font-weight:800; font-size:15px; color:#fff; display:flex; align-items:center; gap:8px;}
.brand-dot{width:8px; height:8px; border-radius:50%; background:var(--secondary);}
.top-user{display:flex; align-items:center; gap:10px;}
.top-user-name{font-size:12.5px; color:#fff; font-weight:600; line-height:1.2;}
.top-user-role{font-size:10.5px; color:rgba(255,255,255,.65);}
.logout-form button{
  padding:7px 13px; border-radius:8px; border:1px solid rgba(255,255,255,.3);
  background:transparent; color:#fff; font-size:12px; font-weight:600; cursor:pointer;
}
.logout-form button:hover{background:rgba(255,255,255,.08);}

/* Compact content */
.content{max-width:1040px; margin:0 auto; padding:20px 18px 32px;}
.welcome-row{display:flex; align-items:baseline; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:6px;}
.welcome{font-family:'Poppins',sans-serif; font-weight:700; font-size:19px;}
.sub{color:var(--sub); font-size:12.5px;}

.grid{display:grid; grid-template-columns:repeat(4, 1fr); gap:12px; margin-bottom:16px;}
.stat{background:#fff; border:1px solid var(--border); border-radius:12px; padding:14px 16px;}
.stat-label{font-size:11px; font-weight:600; color:var(--sub); text-transform:uppercase; letter-spacing:.03em;}
.stat-value{font-family:'Poppins',sans-serif; font-weight:700; font-size:21px; margin-top:5px; color:var(--dark);}
.stat-hint{font-size:11px; color:var(--sub); margin-top:2px;}

.panels{display:grid; grid-template-columns:1.15fr 1fr; gap:14px; align-items:start;}
.card{background:#fff; border:1px solid var(--border); border-radius:14px;}
.card-head{padding:12px 16px; border-bottom:1px solid var(--border); font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; display:flex; align-items:center; justify-content:space-between;}
.card-body{padding:6px 0;}

.assign-row{display:flex; align-items:center; justify-content:space-between; padding:9px 16px; font-size:12.5px; border-bottom:1px solid #F1F2F0;}
.assign-row:last-child{border-bottom:none;}
.assign-row span:first-child{color:var(--sub);}
.assign-row span:last-child{font-weight:700; color:var(--text);}
.badge-mini{background:#FDECD8; color:#C2660B; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:99px;}

.ann-item{padding:10px 16px; border-bottom:1px solid #F1F2F0;}
.ann-item:last-child{border-bottom:none;}
.ann-title{font-size:12.5px; font-weight:700; color:var(--text);}
.ann-body{font-size:11.5px; color:var(--sub); margin-top:2px; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;}
.ann-meta{font-size:10.5px; color:#9CA3AF; margin-top:4px;}
.empty{padding:22px 16px; text-align:center; color:var(--sub); font-size:12px;}

@media (max-width:760px){
  .grid{grid-template-columns:repeat(2, 1fr);}
  .panels{grid-template-columns:1fr;}
}
</style>
</head>
<body>
<div class="topbar">
  <div class="brand"><span class="brand-dot"></span>BANTAY · Teacher</div>
  <div class="top-user">
    <div style="text-align:right;">
      <div class="top-user-name">{{ $teacher->name }}</div>
      <div class="top-user-role">{{ $teacher->email }}</div>
    </div>
    <form class="logout-form" method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit">Log out</button>
    </form>
  </div>
</div>

<div class="content">
  <div class="welcome-row">
    <div>
      <div class="welcome">Welcome, {{ explode(' ', $teacher->name)[0] }} 👋</div>
      <div class="sub">
        @if ($teacher->assigned_grade_level && $teacher->assigned_section)
          Handling <strong>Grade {{ $teacher->assigned_grade_level }} - {{ $teacher->assigned_section }}</strong>{{ $teacher->assigned_subject ? ' · ' . $teacher->assigned_subject : '' }}
        @else
          You haven't been assigned to a section yet.
        @endif
      </div>
    </div>
  </div>

  <div class="grid">
    <div class="stat">
      <div class="stat-label">My Section</div>
      <div class="stat-value">{{ $teacher->assigned_section ?? '—' }}</div>
      <div class="stat-hint">{{ $teacher->assigned_grade_level ? 'Grade '.$teacher->assigned_grade_level : 'Not assigned' }}</div>
    </div>
    <div class="stat">
      <div class="stat-label">Subject</div>
      <div class="stat-value" style="font-size:16px;">{{ $teacher->assigned_subject ?? '—' }}</div>
      <div class="stat-hint">Assigned by admin</div>
    </div>
    <div class="stat">
      <div class="stat-label">Students</div>
      <div class="stat-value">{{ $sectionStudentCount }}</div>
      <div class="stat-hint">in your section</div>
    </div>
    <div class="stat">
      <div class="stat-label">Announcements</div>
      <div class="stat-value">{{ $announcements->count() }}</div>
      <div class="stat-hint">recent</div>
    </div>
  </div>

  <div class="panels">
    <div class="card">
      <div class="card-head">My Assignment</div>
      <div class="card-body">
        <div class="assign-row"><span>Grade Level</span><span>{{ $teacher->assigned_grade_level ? 'Grade '.$teacher->assigned_grade_level : 'Not set' }}</span></div>
        <div class="assign-row"><span>Section</span><span>{{ $teacher->assigned_section ?? 'Not set' }}</span></div>
        <div class="assign-row"><span>Subject</span><span>{{ $teacher->assigned_subject ?? 'Not set' }}</span></div>
        <div class="assign-row"><span>Enrolled Students</span><span>{{ $sectionStudentCount }}</span></div>
        @unless($teacher->assigned_section)
          <div style="padding:10px 16px;"><span class="badge-mini">Ask your admin to assign a section</span></div>
        @endunless
      </div>
    </div>

    <div class="card">
      <div class="card-head">Announcements</div>
      <div class="card-body">
        @forelse ($announcements as $a)
          <div class="ann-item">
            <div class="ann-title">{{ $a->title }}</div>
            <div class="ann-body">{{ $a->body }}</div>
            <div class="ann-meta">{{ $a->created_at->diffForHumans() }}</div>
          </div>
        @empty
          <div class="empty">No announcements yet.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
</body>
</html>
