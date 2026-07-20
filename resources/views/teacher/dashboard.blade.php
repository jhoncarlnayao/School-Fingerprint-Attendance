@extends('layouts.teacher')

@section('title', 'Dashboard')

@section('head')
<style>
.search-bar{
  display:flex; align-items:center; justify-content:space-between; background:#fff; border-radius:20px;
  padding:18px 20px; box-shadow:0 1px 3px rgba(0,0,0,.04); margin-bottom:16px; gap:16px; flex-wrap:wrap;
}
.search-bar input{
  border:none; outline:none; font-family:'Poppins',sans-serif; font-size:20px; font-weight:600; color:#C9C9C6;
  background:transparent; flex:1; min-width:180px;
}
.search-bar input::placeholder{color:#C9C9C6;}
.search-icon{width:40px; height:40px; border-radius:50%; background:#F1F1EF; display:flex; align-items:center; justify-content:center; flex-shrink:0;}
.hstat{display:flex; gap:34px; flex-wrap:wrap;}
.hstat-item .hstat-label{font-size:11.5px; color:var(--sub); font-weight:600; margin-bottom:4px;}
.hstat-item .hstat-value{display:flex; align-items:baseline; gap:8px; font-family:'Poppins',sans-serif; font-weight:700; font-size:26px; color:var(--dark);}
.hstat-pill{font-size:10.5px; font-weight:700; padding:3px 8px; border-radius:99px;}
.pill-up{background:#DFF3E3; color:#1F8A45;}
.pill-warn{background:#FBE1E1; color:var(--danger);}

.bento{display:grid; grid-template-columns:1fr 1fr 1fr 1.1fr; gap:12px; margin-bottom:12px; align-items:stretch;}
.bcard{background:#fff; border-radius:20px; box-shadow:0 1px 3px rgba(0,0,0,.04); padding:18px; display:flex; flex-direction:column;}
.bcard-head{display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;}
.bcard-title{font-family:'Poppins',sans-serif; font-weight:700; font-size:14px;}
.bcard-plus{width:24px; height:24px; border-radius:50%; border:1px solid #ECECEC; display:flex; align-items:center; justify-content:center; color:var(--sub);}

.gauge-wrap{display:flex; flex-direction:column; align-items:center; margin-top:6px;}
.gauge{width:150px; height:82px; position:relative;}
.gauge-value{position:absolute; bottom:0; left:0; right:0; text-align:center;}
.gauge-value .num{font-family:'Poppins',sans-serif; font-weight:700; font-size:26px;}
.gauge-value .lbl{font-size:10px; color:var(--sub);}
.mini-row{display:flex; gap:8px; margin-top:14px;}
.mini-chip{width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; position:relative;}
.mini-chip .badge-count{position:absolute; top:-4px; right:-4px; background:var(--danger); color:#fff; font-size:9px; font-weight:700; padding:1px 5px; border-radius:99px;}

.cta-card{background:linear-gradient(160deg, var(--primary), var(--primary2)); color:#fff; border-radius:20px; padding:18px; display:flex; flex-direction:column; box-shadow:0 4px 14px rgba(255,122,61,.28);}
.cta-btn{margin-top:6px; align-self:flex-start; background:#fff; color:var(--dark); border-radius:999px; padding:8px 14px; font-size:11.5px; font-weight:700; display:inline-flex; align-items:center; gap:6px; text-decoration:none;}
.cta-num{font-family:'Poppins',sans-serif; font-weight:700; font-size:34px; margin-top:auto;}
.cta-sub{font-size:11px; opacity:.9;}

.note-item{padding:9px 0; border-bottom:1px solid #F5F5F3; font-size:11.5px;}
.note-item:last-child{border-bottom:none;}
.note-time{color:#B3B3AF; font-size:10px; display:flex; align-items:center; gap:4px; margin-bottom:5px;}
.note-title{font-weight:700; color:var(--text); line-height:1.35;}
.note-tag{display:inline-flex; align-items:center; gap:5px; font-size:10px; color:var(--sub); margin-top:6px;}
.note-tag i{width:6px; height:6px; border-radius:50%; background:var(--primary); display:inline-block;}

.snap-icon{width:32px; height:32px; border-radius:50%; background:#FFE9DD; display:flex; align-items:center; justify-content:center; margin:8px auto 10px;}
.snap-num{display:flex; align-items:baseline; justify-content:center; gap:8px;}
.snap-num .n{font-family:'Poppins',sans-serif; font-weight:700; font-size:30px;}
.snap-num .t{font-size:10.5px; color:var(--sub); text-align:left; line-height:1.3;}

.lower{display:grid; grid-template-columns:1.5fr 1fr; gap:12px;}
.chart-card{background:#fff; border-radius:20px; box-shadow:0 1px 3px rgba(0,0,0,.04); padding:18px;}
.legend{display:flex; gap:14px; font-size:11px; color:var(--sub);}
.legend span{display:inline-flex; align-items:center; gap:5px;}
.legend i{width:8px; height:8px; border-radius:50%; display:inline-block;}

.week-card{background:#fff; border-radius:20px; box-shadow:0 1px 3px rgba(0,0,0,.04); padding:16px; position:relative;}
.week-strip{display:flex; justify-content:space-between; margin-top:10px;}
.week-day{display:flex; flex-direction:column; align-items:center; gap:6px; font-size:10.5px; color:var(--sub);}
.week-dot{width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:10px; color:#fff; background:#F1F1EF; color:var(--sub);}
.week-day.today .week-dot{background:var(--dark); color:#fff; box-shadow:0 0 0 3px #F1F1EF;}

.today-panel{
  margin-top:14px; background:var(--dark); border-radius:16px; padding:16px; color:#fff;
}
.today-panel .tp-title{font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; margin-bottom:10px;}
.tp-badge{display:inline-flex; align-items:center; gap:6px; background:var(--primary); font-size:10.5px; font-weight:700; padding:5px 10px; border-radius:99px;}
.tp-row{display:flex; align-items:center; justify-content:space-between; margin-top:12px;}
.tp-avatars{display:flex; align-items:center;}
.tp-avatars span{width:26px; height:26px; border-radius:50%; background:#3A3A3A; border:2px solid var(--dark); display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; margin-left:-8px;}
.tp-avatars span:first-child{margin-left:0;}
.tp-plus{width:30px; height:30px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; color:#fff; text-decoration:none;}
</style>
@endsection

@section('content')

<div class="search-bar">
  <div style="display:flex; align-items:center; gap:14px; flex:1;">
    <input type="text" placeholder="Search a student in your section…">
  </div>
  <div class="hstat">
    <div class="hstat-item">
      <div class="hstat-label">Section Students</div>
      <div class="hstat-value">{{ $sectionStudentCount }} <span class="hstat-pill pill-up">Gr.{{ $teacher->assigned_grade_level ?? '—' }}</span></div>
    </div>
    <div class="hstat-item">
      <div class="hstat-label">Present Today</div>
      <div class="hstat-value">{{ $presentToday }} <span class="hstat-pill pill-up">{{ $sectionStudentCount > 0 ? round($presentToday / $sectionStudentCount * 100) : 0 }}%</span></div>
    </div>
    <div class="hstat-item">
      <div class="hstat-label">Absent Today</div>
      <div class="hstat-value">{{ $absentToday }} <span class="hstat-pill pill-warn">{{ $notScannedToday }} not scanned</span></div>
    </div>
  </div>
  <div class="search-icon">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--sub)" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
  </div>
</div>

<div class="bento">

  {{-- Gauge card --}}
  <div class="bcard">
    <div class="bcard-head">
      <div class="bcard-title">Weekly Rate</div>
      <div class="bcard-plus">+</div>
    </div>
    <div class="gauge-wrap">
      <div class="gauge">
        <svg viewBox="0 0 150 82" width="150" height="82">
          <path d="M10,80 A65,65 0 0 1 140,80" fill="none" stroke="#F1F1EF" stroke-width="14" stroke-linecap="round"/>
          <path d="M10,80 A65,65 0 0 1 140,80" fill="none" stroke="var(--primary)" stroke-width="14" stroke-linecap="round"
                stroke-dasharray="{{ round(2.04 * $attendanceRate) }} 500"/>
        </svg>
        <div class="gauge-value">
          <div class="num">{{ $attendanceRate }}%</div>
          <div class="lbl">7-day average</div>
        </div>
      </div>
    </div>
    <div class="mini-row">
      <div class="mini-chip" style="background:#DFF3E3; color:#1F8A45;">P
        @if($presentToday) <span class="badge-count">{{ $presentToday }}</span> @endif
      </div>
      <div class="mini-chip" style="background:#FFE9DD; color:var(--primary2);">L</div>
      <div class="mini-chip" style="background:#FBE1E1; color:var(--danger);">A
        @if($absentToday) <span class="badge-count">{{ $absentToday }}</span> @endif
      </div>
      <div class="mini-chip" style="background:#F1F1EF; color:var(--sub);">—
        @if($notScannedToday) <span class="badge-count" style="background:var(--sub);">{{ $notScannedToday }}</span> @endif
      </div>
    </div>
  </div>

  {{-- Orange CTA card --}}
  <div class="cta-card">
    <div class="bcard-title" style="color:#fff;">Take Attendance</div>
    @if ($teacher->assigned_grade_level && $teacher->assigned_section)
      <a href="{{ route('teacher.attendance.index') }}" class="cta-btn">+ Open Roster</a>
    @endif
    <div class="cta-num">{{ $presentToday }}</div>
    <div class="cta-sub">marked of {{ $sectionStudentCount }} today</div>
  </div>

  {{-- Announcements --}}
  <div class="bcard">
    <div class="bcard-head">
      <div class="bcard-title">Announcements</div>
      <div class="bcard-plus">+</div>
    </div>
    <div>
      @forelse ($announcements->take(2) as $a)
        <div class="note-item">
          <div class="note-time">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            {{ $a->created_at->diffForHumans() }}
          </div>
          <div class="note-title">{{ $a->title }}</div>
          <div class="note-tag"><i></i> BANTAY</div>
        </div>
      @empty
        <div class="empty" style="padding:24px 0;">No announcements yet.</div>
      @endforelse
    </div>
  </div>

  {{-- Section snapshot --}}
  <div class="bcard">
    <div class="bcard-head">
      <div class="bcard-title">My Section</div>
      <a href="{{ route('teacher.attendance.index') }}" class="bcard-plus" style="text-decoration:none;">↗</a>
    </div>
    <div class="snap-icon">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--primary2)" stroke-width="2.2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-6 8-6s8 2 8 6"/></svg>
    </div>
    <div class="snap-num">
      <div class="n">{{ $sectionStudentCount }}</div>
      <div class="t">Grade {{ $teacher->assigned_grade_level ?? '—' }}<br>{{ $teacher->assigned_section ?? 'Unassigned' }}</div>
    </div>
  </div>
</div>

<div class="lower">
  {{-- Weekly bar chart --}}
  <div class="chart-card">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
      <div class="bcard-title">Weekly Attendance</div>
      <div class="legend">
        <span><i style="background:var(--primary);"></i>Present</span>
        <span><i style="background:#ECECEC;"></i>Absent</span>
      </div>
    </div>
    <canvas id="teacherAttendanceChart" height="150"></canvas>
  </div>

  {{-- Week strip + today panel --}}
  <div class="week-card">
    <div class="bcard-title">This Week</div>
    <div class="week-strip">
      @foreach ($weekStrip as $d)
        <div class="week-day {{ $d['isToday'] ? 'today' : '' }}">
          <span>{{ $d['label'] }}</span>
          <div class="week-dot">{{ $d['rate'] !== null ? $d['rate'].'%' : $d['day'] }}</div>
        </div>
      @endforeach
    </div>

    <div class="today-panel">
      <div class="tp-title">Today's Snapshot</div>
      <span class="tp-badge">{{ now()->format('M d') }} · {{ $presentToday }}/{{ $sectionStudentCount }} present</span>
      <div class="tp-row">
        <div class="tp-avatars">
          @forelse ($recentLogs as $log)
            <span title="{{ $log->student->fullName() }}">{{ $log->student->initials() }}</span>
          @empty
            <span>—</span>
          @endforelse
        </div>
        <a href="{{ route('teacher.attendance.index') }}" class="tp-plus">→</a>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('teacherAttendanceChart'), {
  type: 'bar',
  data: {
    labels: {!! json_encode($chartLabels) !!},
    datasets: [
      { label: 'Present', data: {!! json_encode($chartPresent) !!}, backgroundColor: '#FF7A3D', borderRadius: 6, maxBarThickness: 26 },
      { label: 'Absent', data: {!! json_encode($chartAbsent) !!}, backgroundColor: '#ECECEC', borderRadius: 6, maxBarThickness: 26 }
    ]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false } },
      y: { grid: { color: '#F5F5F3' }, beginAtZero: true }
    }
  }
});
</script>
@endsection