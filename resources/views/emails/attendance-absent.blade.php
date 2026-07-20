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
  .foot{padding:16px 24px; font-size:11.5px; color:#6B7280; text-align:center;}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <div class="head"><span>BANTAY</span></div>
    <div class="body">
      <p class="title">{{ $log->student->fullName() }} was marked absent</p>
      <p class="text">
        No fingerprint scan was recorded for your child at school on {{ $log->date->format('F j, Y') }}.
        If this is a mistake — for example the scanner was offline, or your child was excused —
        please contact the school office so the record can be corrected.
      </p>
    </div>
  </div>
  <div class="foot">This is an automated attendance notice &middot; BANTAY School System</div>
</div>
</body>
</html>
