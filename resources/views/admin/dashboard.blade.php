@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
<div class="page-head">
  <div class="page-title">Good morning, {{ explode(' ', auth()->user()->name)[0] }}</div>
  <div class="page-sub">Stay on top of attendance, teachers, and school announcements.</div>
</div>

<div style="display:grid; grid-template-columns:320px 1fr 1fr; gap:14px; align-items:start;">

  {{-- Total students / teachers overview --}}
  <div class="card" style="padding:20px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
      <div style="font-size:12.5px; font-weight:600; color:var(--sub);">Total Enrolled Students</div>
      <div class="badge badge-green">Active</div>
    </div>
    <div class="font-display" style="font-size:30px; font-weight:700; color:var(--dark);">{{ number_format($totalStudents) }}</div>
    <div style="font-size:11.5px; color:var(--primary2); margin-top:4px;">across {{ $totalSections }} sections</div>

    <div style="display:flex; gap:8px; margin-top:18px;">
      <a href="{{ route('admin.students.create') }}" class="btn btn-primary" style="flex:1; justify-content:center;">+ Add Student</a>
      <a href="{{ route('admin.teachers.register') }}" class="btn btn-outline" style="flex:1; justify-content:center;">+ Teacher</a>
    </div>

    <div style="margin-top:20px; border-top:1px solid #F2F2F0; padding-top:14px;">
      <div style="font-size:12.5px; font-weight:600; margin-bottom:8px;">Teachers &amp; Sections</div>
      <div style="display:flex; align-items:center; justify-content:space-between; padding:7px 0; font-size:12.5px;">
        <span style="color:var(--sub);">Total Teachers</span>
        <span style="font-weight:700;">{{ $totalTeachers }}</span>
      </div>
      <div style="display:flex; align-items:center; justify-content:space-between; padding:7px 0; font-size:12.5px; border-top:1px solid #F5F5F3;">
        <span style="color:var(--sub);">Unassigned Teachers</span>
        <span style="font-weight:700; color:{{ $unassignedTeachers > 0 ? 'var(--warning)' : 'var(--primary2)' }};">{{ $unassignedTeachers }}</span>
      </div>
    </div>
  </div>

  {{-- Present / Absent small stat cards --}}
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
    <div class="card" style="padding:16px; background:linear-gradient(150deg, var(--primary), var(--primary2)); color:#fff; box-shadow:0 4px 14px rgba(255,122,61,.28);">
      <div style="display:flex; align-items:center; justify-content:space-between;">
        <div style="font-size:12.5px; font-weight:600; opacity:.9;">Present Today</div>
        <div style="width:28px; height:28px; border-radius:8px; background:rgba(255,255,255,.22); display:flex; align-items:center; justify-content:center;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
      </div>
      <div class="font-display" style="font-size:24px; font-weight:700; margin-top:12px;">{{ $presentToday }}</div>
      <div style="font-size:11px; opacity:.85; margin-top:2px;">of {{ $totalStudents }} students</div>
    </div>

    <div class="card" style="padding:16px;">
      <div style="display:flex; align-items:center; justify-content:space-between;">
        <div style="font-size:12.5px; font-weight:600; color:var(--text);">Absent Today</div>
        <div style="width:28px; height:28px; border-radius:8px; background:#FBE1E1; display:flex; align-items:center; justify-content:center;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2.4" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </div>
      </div>
      <div class="font-display" style="font-size:24px; font-weight:700; margin-top:12px;">{{ $absentToday }}</div>
      <div style="font-size:11px; color:var(--sub); margin-top:2px;">Needs follow-up</div>
    </div>

    <div class="card" style="padding:16px;">
      <div style="display:flex; align-items:center; justify-content:space-between;">
        <div style="font-size:12.5px; font-weight:600; color:var(--text);">Active Teachers</div>
        <div style="width:28px; height:28px; border-radius:8px; background:#DFF3E3; display:flex; align-items:center; justify-content:center;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1F8A45" stroke-width="2.2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-6 8-6s8 2 8 6"/></svg>
        </div>
      </div>
      <div class="font-display" style="font-size:24px; font-weight:700; margin-top:12px;">{{ $totalTeachers }}</div>
      <div style="font-size:11px; color:var(--sub); margin-top:2px;">{{ $totalTeachers - $unassignedTeachers }} assigned</div>
    </div>

    <div class="card" style="padding:16px;">
      <div style="display:flex; align-items:center; justify-content:space-between;">
        <div style="font-size:12.5px; font-weight:600; color:var(--text);">Announcements</div>
        <div style="width:28px; height:28px; border-radius:8px; background:#FFE9DD; display:flex; align-items:center; justify-content:center;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--primary2)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5-5 18-4-8-8-4Z"/></svg>
        </div>
      </div>
      <div class="font-display" style="font-size:24px; font-weight:700; margin-top:12px;">{{ $totalAnnouncements }}</div>
      <div style="font-size:11px; color:var(--sub); margin-top:2px;">Posted this term</div>
    </div>
  </div>

  {{-- Weekly attendance chart --}}
  <div class="card" style="padding:18px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
      <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:14px;">Weekly Attendance</div>
      <div style="display:flex; gap:10px; font-size:11px; color:var(--sub);">
        <span style="display:flex; align-items:center; gap:5px;"><i style="width:8px;height:8px;border-radius:50%;background:var(--primary); display:inline-block;"></i>Present</span>
        <span style="display:flex; align-items:center; gap:5px;"><i style="width:8px;height:8px;border-radius:50%;background:#ECECEC; display:inline-block;"></i>Absent</span>
      </div>
    </div>
    <canvas id="attendanceChart" height="170"></canvas>
  </div>
</div>

{{-- Progress + Recent activity --}}
<div style="display:grid; grid-template-columns:320px 1fr; gap:14px; margin-top:14px;">
  <div class="card" style="padding:18px;">
    <div style="font-weight:700; font-family:'Poppins',sans-serif; font-size:14px; margin-bottom:12px;">Monthly Attendance Rate</div>
    <div style="height:8px; background:#F1F1EF; border-radius:99px; overflow:hidden;">
      <div style="height:100%; width:{{ $attendanceRate }}%; background:linear-gradient(90deg, var(--secondary), var(--primary)); border-radius:99px;"></div>
    </div>
    <div style="display:flex; justify-content:space-between; font-size:11.5px; color:var(--sub); margin-top:8px;">
      <span>{{ $attendanceRate }}% average</span>
      <span>Target 95%</span>
    </div>

    <div style="margin-top:18px; display:flex; flex-direction:column; gap:8px;">
      <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary" style="justify-content:center;">+ Post &amp; Email Announcement</a>
      <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline" style="justify-content:center;">View Announcements</a>
    </div>
  </div>

  <div class="card">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:15px 18px; border-bottom:1px solid #F2F2F0;">
      <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:14px;">Recent Activity</div>
      <a href="{{ route('admin.students.create') }}" class="btn" style="padding:7px 13px; font-size:12px;">Manage Students</a>
    </div>
    <div style="max-height:320px; overflow:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Admin</th>
            <th>Action</th>
            <th>What happened</th>
            <th>When</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($recentActivity as $item)
            <tr>
              <td style="font-weight:600;">{{ $item->user->name ?? 'System' }}</td>
              <td>
                @php
                  $actionBadge = match($item->action) {
                    'created' => 'badge-green',
                    'deleted' => 'badge-red',
                    default => 'badge-orange',
                  };
                @endphp
                <span class="badge {{ $actionBadge }}">{{ ucfirst($item->action) }}</span>
                @if($item->is_warning)
                  <span class="badge badge-red" style="margin-left:4px;">Warning</span>
                @endif
              </td>
              <td style="color:var(--sub);">{{ $item->description }}</td>
              <td style="color:var(--sub); white-space:nowrap;">{{ $item->timeAgo() }}</td>
            </tr>
          @empty
            <tr><td colspan="4" style="padding:28px 18px; text-align:center; color:var(--sub);">No activity yet — register a teacher or add a student to get started.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('attendanceChart'), {
  type: 'bar',
  data: {
    labels: {!! json_encode($chartLabels) !!},
    datasets: [
      { label: 'Present', data: {!! json_encode($chartPresent) !!}, backgroundColor: '#FF7A3D', borderRadius: 6, maxBarThickness: 22 },
      { label: 'Absent', data: {!! json_encode($chartAbsent) !!}, backgroundColor: '#ECECEC', borderRadius: 6, maxBarThickness: 22 }
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