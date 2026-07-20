@extends('layouts.admin')

@section('title', 'Attendance')

@section('head')
<script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
<div class="page-head" style="display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap;">
  <div>
    <div class="page-title">Attendance</div>
    <div class="page-sub">Fingerprint scan activity for {{ $date->format('F j, Y') }}.</div>
  </div>
  <form method="GET" style="display:flex; gap:8px; align-items:center;">
    <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}"
      style="padding:9px 12px; border:1px solid var(--border); border-radius:10px; font-size:13px;"
      onchange="this.form.submit()">
  </form>
</div>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:20px;">
  <div class="card" style="padding:18px;">
    <div style="font-size:12.5px; color:var(--sub); font-weight:600;">Present</div>
    <div class="font-display" style="font-size:26px; font-weight:700; color:var(--primary); margin-top:8px;">{{ $present }}</div>
  </div>
  <div class="card" style="padding:18px;">
    <div style="font-size:12.5px; color:var(--sub); font-weight:600;">Late</div>
    <div class="font-display" style="font-size:26px; font-weight:700; color:#C2660B; margin-top:8px;">{{ $late }}</div>
  </div>
  <div class="card" style="padding:18px;">
    <div style="font-size:12.5px; color:var(--sub); font-weight:600;">Absent</div>
    <div class="font-display" style="font-size:26px; font-weight:700; color:var(--danger); margin-top:8px;">{{ $absent }}</div>
  </div>
  <div class="card" style="padding:18px;">
    <div style="font-size:12.5px; color:var(--sub); font-weight:600;">Not yet scanned</div>
    <div class="font-display" style="font-size:26px; font-weight:700; color:var(--sub); margin-top:8px;">{{ $notYetScanned }}</div>
  </div>
</div>

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">

  <div class="card">
    <div style="padding:16px 18px; border-bottom:1px solid var(--border); font-family:'Poppins',sans-serif; font-weight:700; font-size:15px;">
      Today's Log ({{ $logs->count() }})
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="background:var(--dark); color:#fff;">
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Student</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Time In</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Time Out</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Status</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Source</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($logs->sortBy(fn($l) => $l->student->fullName()) as $log)
          <tr style="border-bottom:1px solid #F1F2F0;">
            <td style="padding:11px 16px; font-weight:600;">{{ $log->student->fullName() }}</td>
            <td style="padding:11px 16px; color:var(--sub);">{{ $log->timeInFormatted() ?? '—' }}</td>
            <td style="padding:11px 16px; color:var(--sub);">{{ $log->timeOutFormatted() ?? '—' }}</td>
            <td style="padding:11px 16px;"><span class="badge {{ $log->badgeClass() }}">{{ $log->statusLabel() }}</span></td>
            <td style="padding:11px 16px; color:var(--sub);">{{ ucfirst($log->source) }}</td>
            <td style="padding:11px 16px; text-align:right;">
              <a href="{{ route('admin.attendance.show', $log->student) }}" class="btn" style="padding:6px 12px; font-size:12px;">History</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="padding:26px 16px; text-align:center; color:var(--sub);">No scans recorded yet for this date.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="display:flex; flex-direction:column; gap:16px;">
    <div class="card">
      <div style="padding:16px 18px; border-bottom:1px solid var(--border); font-family:'Poppins',sans-serif; font-weight:700; font-size:15px;">
        Per Section
      </div>
      <div style="padding:6px 0;">
        @forelse ($sections as $s)
          <div style="padding:10px 18px; border-bottom:1px solid #F1F2F0; font-size:12.5px;">
            <div style="display:flex; justify-content:space-between; font-weight:600; margin-bottom:4px;">
              <span>{{ $s['label'] }}</span>
              <span style="color:var(--sub); font-weight:500;">{{ $s['present'] }}/{{ $s['total'] }}</span>
            </div>
            <div style="height:6px; background:#F1F2F0; border-radius:99px; overflow:hidden;">
              <div style="height:100%; width:{{ $s['total'] > 0 ? round(($s['present'] / $s['total']) * 100) : 0 }}%; background:var(--secondary);"></div>
            </div>
          </div>
        @empty
          <div style="padding:20px 18px; text-align:center; color:var(--sub); font-size:12.5px;">No sections yet.</div>
        @endforelse
      </div>
    </div>

    <div class="card" style="padding:16px 18px;">
      <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:15px; margin-bottom:12px;">
        Quick Manual Override
      </div>
      @if($unscannedStudents->isEmpty())
        <div style="font-size:12.5px; color:var(--sub);">Everyone has a scan recorded for this date.</div>
      @else
        <form method="POST" action="" id="manual-override-form">
          @csrf
          <div class="form-field">
            <label>Student (no scan yet)</label>
            <select id="manual-student" required onchange="document.getElementById('manual-override-form').action = this.options[this.selectedIndex].dataset.url">
              <option value="" disabled selected>Select student</option>
              @foreach ($unscannedStudents as $s)
                <option value="{{ $s->id }}" data-url="{{ route('admin.attendance.mark', $s) }}">{{ $s->fullName() }} — Gr.{{ $s->grade_level }} {{ $s->section }}</option>
              @endforeach
            </select>
          </div>
          <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
          <div class="form-field">
            <label>Status</label>
            <select name="status" required>
              <option value="present">Present</option>
              <option value="late">Late</option>
              <option value="absent">Absent</option>
              <option value="excused">Excused</option>
            </select>
          </div>
          <div class="form-field">
            <label>Note</label>
            <input name="note" placeholder="e.g. scanner offline">
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Save Override</button>
        </form>
      @endif
    </div>
  </div>
</div>
@endsection
