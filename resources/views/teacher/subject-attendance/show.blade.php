@extends('layouts.teacher')

@section('title', $student->fullName().' — '.$teacher->assigned_subject)

@section('content')
<div class="welcome-row">
  <div>
    <div class="welcome">{{ $student->fullName() }}</div>
    <div class="sub">{{ $teacher->assigned_subject }} · Gr.{{ $student->grade_level }} — {{ $student->section }} · {{ $student->student_no }}</div>
  </div>
  <a href="{{ route('teacher.subject-attendance.index') }}" class="btn">Back to Roster</a>
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
  <div class="card-head">{{ $teacher->assigned_subject }} History</div>
  <table style="width:100%; border-collapse:collapse; font-size:13px;">
    <thead>
      <tr style="background:var(--dark); color:#fff;">
        <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Date</th>
        <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Status</th>
        <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Guardian Notified</th>
        <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Note</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($logs as $log)
        <tr style="border-bottom:1px solid #F1F2F0;">
          <td style="padding:11px 16px; font-weight:600;">{{ $log->date->format('M d, Y') }}</td>
          <td style="padding:11px 16px;"><span class="badge {{ $log->badgeClass() }}">{{ $log->statusLabel() }}</span></td>
          <td style="padding:11px 16px; color:var(--sub);">{{ $log->guardian_notified_at?->format('M d, g:i A') ?? '—' }}</td>
          <td style="padding:11px 16px; color:var(--sub);">{{ $log->note ?? '—' }}</td>
        </tr>
      @empty
        <tr><td colspan="4" style="padding:26px 16px; text-align:center; color:var(--sub);">No {{ $teacher->assigned_subject }} attendance recorded yet.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div style="padding:14px 18px;">{{ $logs->links() }}</div>
</div>
@endsection
