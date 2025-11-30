<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','To-Do App')</title>

  @vite(['resources/css/app.css','resources/js/app.js'])

  <style>
    :root{
      --purple-700:#6d28d9; --purple-400:#a78bfa;
      --glass: rgba(255,255,255,0.03);
      --muted: #9aa0be;
    }

    html,body { height:100%; }
    body {
      margin:0;
      font-family:Inter, sans-serif;
      color:#eaeaf6;
      background: linear-gradient(180deg,#0B0F1A,#0F1220);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    .glass{
      background:var(--glass);
      backdrop-filter: blur(10px);
      border:1px solid rgba(167,139,250,0.04);
      padding:12px;
    }

    a { text-decoration:none; color:inherit; }
    .purple-btn{ background:linear-gradient(90deg,var(--purple-700),var(--purple-400)); color:white; padding:8px 12px; border-radius:10px; }
    .muted{ color:var(--muted); font-size:0.95rem; }
    .fab { position: fixed; right: 22px; bottom: 22px; z-index:50; width:56px; height:56px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 10px 30px rgba(108,58,255,0.18); cursor:pointer; background:linear-gradient(90deg,var(--purple-700),var(--purple-400)); color:white; }

    header nav .nav-btn{ padding:10px 16px; border-radius:10px; background:rgba(255,255,255,0.04); }
    header nav .nav-btn:hover{ background:rgba(255,255,255,0.06); }
    #snow-canvas{ position:fixed; inset:0; pointer-events:none; z-index:-1; }
  </style>
  @stack('head')
</head>
<body>

<canvas id="snow-canvas"></canvas>

<div class="max-w-7xl mx-auto px-4 py-6">
  <header class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
    <div class="flex items-center gap-4">
      <div style="width:46px;height:46px;border-radius:10px;background:linear-gradient(135deg,var(--purple-700),var(--purple-400));box-shadow:0 8px 28px rgba(109,40,217,0.12)"></div>
      <div>
        <a href="{{ route('dashboard') }}" class="text-2xl font-bold">To-Do</a>
        <div class="text-sm muted">For Students • Rizki Ramadhan Zafitra</div>
      </div>
    </div>

    <nav class="flex items-center gap-3 flex-wrap">
      <a href="{{ route('dashboard') }}" class="nav-btn">Dashboard</a>
      <a href="{{ route('tasks.index') }}" class="nav-btn">Tasks</a>
      <a href="{{ route('tasks.filter','pending') }}" class="nav-btn">Pending</a>
      <a href="{{ route('tasks.filter','done') }}" class="nav-btn">Done</a>
      <a href="{{ route('tasks.create') }}" class="purple-btn">+ Task</a>
    </nav>
  </header>

  @if(session('success'))
    <div class="glass p-3 rounded mb-4 text-green-200">{{ session('success') }}</div>
  @endif

  @yield('content')

  <footer class="mt-10 text-center muted">
    Built by <strong>Rizki Ramadhan Zafitra</strong> • © {{ date('Y') }}
  </footer>
</div>

<a href="{{ route('tasks.create') }}" class="fab" title="Tambah Tugas">
  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
</a>

{{-- Snow script --}}
<script>
(function(){
    const canvas = document.getElementById("snow-canvas");
    if(!canvas) return;
    const ctx = canvas.getContext("2d");
    function resize(){ canvas.width = innerWidth; canvas.height = innerHeight; }
    resize(); window.addEventListener('resize', resize);

    const flakes = [];
    for(let i=0;i<80;i++){
        flakes.push({ x:Math.random()*innerWidth, y:Math.random()*innerHeight, r:1+Math.random()*2, s:0.4+Math.random()*0.8 });
    }

    function loop(){
        ctx.clearRect(0,0,canvas.width,canvas.height);
        flakes.forEach(f=>{
            f.y += f.s;
            f.x += Math.sin(f.y*0.01) * 0.3;
            if(f.y>canvas.height){ f.y = -5; f.x = Math.random()*canvas.width; }
            ctx.beginPath();
            ctx.fillStyle = 'rgba(255,255,255,0.85)';
            ctx.arc(f.x,f.y,f.r,0,Math.PI*2);
            ctx.fill();
        });
        requestAnimationFrame(loop);
    }
    loop();
})();
</script>

@stack('scripts')
</body>
</html>
