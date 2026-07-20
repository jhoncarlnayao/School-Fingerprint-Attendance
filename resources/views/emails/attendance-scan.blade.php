<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body{margin:0; background:#F3F6F2; font-family:Arial, sans-serif; color:#1F2937;}
  .wrap{max-width:520px; margin:0 auto; padding:32px 20px;}
  .card{background:#fff; border-radius:16px; overflow:hidden; border:1px solid #E5E7EB;}
  .head{background:linear-gradient(135deg, #152B0C, #1B3812); padding:22px 24px;}
  .head span{color:#fff; font-weight:800; font-size:16px;}
  .body{padding:26px 24px;}
  .title{font-size:19px; font-weight:700; margin:0 0 10px; color:#152B0C;}
  .text{font-size:14px; line-height:1.6; color:#374151;}
  .pill{display:inline-block; font-size:11.5px; font-weight:700; padding:4px 10px; border-radius:99px; margin-left:6px;}
  .pill-present{background:#E7F3DE; color:#3F5A2A;}
  .pill-late{background:#FDECD8; color:#C2660B;}
  .foot{padding:16px 24px; font-size:11.5px; color:#6B7280; text-align:center;}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <div class="head"><span>BANTAY</span></div>
    <div class="body">
      @if ($event === 'out')
        <p class="title">{{ $log->student->fullName() }} checked out</p>
        <p class="text">
          Your child checked out of school at <strong>{{ $log->timeOutFormatted() }}</strong> on {{ $log->date->format('F j, Y') }}.
        </p>
      @else
        <p class="title">{{ $log->student->fullName() }} arrived at school
          <span class="pill {{ $log->status === 'late' ? 'pill-late' : 'pill-present' }}">{{ $log->statusLabel() }}</span>
        </p>
        <p class="text">
          Your child arrived at <strong>{{ $log->timeInFormatted() }}</strong> on {{ $log->date->format('F j, Y') }}.
        </p>
      @endif
    </div>
  </div>
  <div class="foot">This is an automated attendance notice &middot; BANTAY School System</div>
</div>
</body>
</html>
