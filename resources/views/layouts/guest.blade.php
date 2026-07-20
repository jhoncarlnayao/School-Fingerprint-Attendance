<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'BANTAY') — Sign in</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --dark:#152B0C;
  --dark2:#1B3812;
  --primary:#3F5A2A;
  --secondary:#8BC34A;
  --bg:#F3F6F2;
  --card:#FFFFFF;
  --border:#E5E7EB;
  --text:#1F2937;
  --sub:#6B7280;
  --danger:#EF4444;
}
*{box-sizing:border-box;}
body{margin:0; background:#D9DED6; color:var(--text); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased;}
.font-display{font-family:'Poppins',sans-serif;}
</style>
{{ $head ?? '' }}
</head>
<body>
{{ $slot }}
</body>
</html>
