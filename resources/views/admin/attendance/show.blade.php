@extends('layouts.admin')

@section('title', $student->fullName().' — Attendance')

@section('content')
<div class="page-head" style="display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap;">
  <div>
    <div class="page-title">{{ $student->fullName() }}</div>
    <div class="page-sub">Gr.{{ $student->grade_level }} — {{ $student->section }} · {{ $student->student_no }}</div>
  </div>
  <div style="display:flex; gap:10px;">
    <a href="{{ route('admin.attendance.index') }}" class="btn">Back to Dashboard</a>
    <a href="{{ route('admin.attendance.export', $student) }}" class="btn btn-outline">Export CSV</a>
  </div>
</div>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:20px;">
  <div class="card" style="padding:18px;">
    <div style="font-size:12.5px; color:var(--sub); font-weight:600;">Present</div>
    <div class="font-display" style="font-size:24px; font-weight:700; color:var(--primary); margin-top:8px;">{{ $summary['present'] }}</div>
  </div>
  <div class="card" style="padding:18px;">
    <div style="font-size:12.5px; color:var(--sub); font-weight:600;">Late</div>
    <div class="font-display" style="font-size:24px; font-weight:700; color:#C2660B; margin-top:8px;">{{ $summary['late'] }}</div>
  </div>
  <div class="card" style="padding:18px;">
    <div style="font-size:12.5px; color:var(--sub); font-weight:600;">Absent</div>
    <div class="font-display" style="font-size:24px; font-weight:700; color:var(--danger); margin-top:8px;">{{ $summary['absent'] }}</div>
  </div>
  <div class="card" style="padding:18px;">
    <div style="font-size:12.5px; color:var(--sub); font-weight:600;">Excused</div>
    <div class="font-display" style="font-size:24px; font-weight:700; color:var(--sub); margin-top:8px;">{{ $summary['excused'] }}</div>
  </div>
</div>

@if(!$student->guardian_email)
  <div class="alert alert-warning">⚠️ No guardian email on file for this student — attendance emails won't be sent. Add one on the Students page.</div>
@endif
@if(!$student->hasFingerprintOnFile())
  <div class="alert alert-warning">⚠️ No fingerprint ID on file for this student — the scanner can't match them yet. Add one on the Students page.</div>
@endif

<div class="card">
  <div style="padding:16px 18px; border-bottom:1px solid var(--border); font-family:'Poppins',sans-serif; font-weight:700; font-size:15px;">
    History
  </div>
  <table style="width:100%; border-collapse:collapse; font-size:13px;">
    <thead>
      <tr style="background:var(--dark); color:#fff;">
        <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Date</th>
        <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Time In</th>
        <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Time Out</th>
        <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Status</th>
        <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Source</th>
        <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Note</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($logs as $log)
        <tr style="border-bottom:1px solid #F1F2F0;">
          <td style="padding:11px 16px; font-weight:600;">{{ $log->date->format('M d, Y') }}</td>
          <td style="padding:11px 16px; color:var(--sub);">{{ $log->timeInFormatted() ?? '—' }}</td>
          <td style="padding:11px 16px; color:var(--sub);">{{ $log->timeOutFormatted() ?? '—' }}</td>
          <td style="padding:11px 16px;"><span class="badge {{ $log->badgeClass() }}">{{ $log->statusLabel() }}</span></td>
          <td style="padding:11px 16px; color:var(--sub);">{{ ucfirst($log->source) }}</td>
          <td style="padding:11px 16px; color:var(--sub);">{{ $log->note ?? '—' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" style="padding:26px 16px; text-align:center; color:var(--sub);">No attendance history yet.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div style="padding:14px 18px;">{{ $logs->links() }}</div>
</div>
@endsection
