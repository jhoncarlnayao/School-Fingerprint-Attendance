@extends('layouts.teacher')

@section('title', $student->fullName().' — Attendance')

@section('content')
<div class="welcome-row">
  <div>
    <div class="welcome">{{ $student->fullName() }}</div>
    <div class="sub">Gr.{{ $student->grade_level }} — {{ $student->section }} · {{ $student->student_no }}</div>
  </div>
  <div style="display:flex; gap:8px;">
    <a href="{{ route('teacher.attendance.index') }}" class="btn">Back to Attendance</a>
    <a href="{{ route('teacher.attendance.export', $student) }}" class="btn btn-outline">Export CSV</a>
  </div>
</div>

<div class="grid">
  <div class="stat">
    <div class="stat-label">Present</div>
    <div class="stat-value" style="color:var(--primary);">{{ $summary['present'] }}</div>
  </div>
  <div class="stat">
    <div class="stat-label">Late</div>
    <div class="stat-value" style="color:#C2660B;">{{ $summary['late'] }}</div>
  </div>
  <div class="stat">
    <div class="stat-label">Absent</div>
    <div class="stat-value" style="color:var(--danger);">{{ $summary['absent'] }}</div>
  </div>
  <div class="stat">
    <div class="stat-label">Excused</div>
    <div class="stat-value">{{ $summary['excused'] }}</div>
  </div>
</div>

<div class="card">
  <div class="card-head">History</div>
  <table class="tbl">
    <thead>
      <tr>
        <th>Date</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Status</th>
        <th>Source</th>
        <th>Note</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($logs as $log)
        <tr>
          <td style="font-weight:600;">{{ $log->date->format('M d, Y') }}</td>
          <td style="color:var(--sub);">{{ $log->timeInFormatted() ?? '—' }}</td>
          <td style="color:var(--sub);">{{ $log->timeOutFormatted() ?? '—' }}</td>
          <td><span class="badge {{ $log->badgeClass() }}">{{ $log->statusLabel() }}</span></td>
          <td style="color:var(--sub);">{{ ucfirst($log->source) }}</td>
          <td style="color:var(--sub);">{{ $log->note ?? '—' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" style="padding:24px 15px; text-align:center; color:var(--sub);">No attendance history yet.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div style="padding:13px 16px;">{{ $logs->links() }}</div>
</div>
@endsection