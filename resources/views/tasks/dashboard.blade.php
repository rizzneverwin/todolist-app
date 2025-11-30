@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <div class="lg:col-span-2 space-y-6">
    <div class="glass p-6 rounded-2xl">
      <div class="flex justify-between items-start">
        <div>
          <h1 class="text-2xl font-bold">Selamat Datang 👋</h1>
          <p class="muted mt-1">Kelola tugas harian, atur prioritas, dan jangan lewatkan deadline.</p>
        </div>

        <div class="text-right">
          <div id="clock" class="text-2xl font-mono font-semibold"></div>
          <div id="date" class="muted text-sm mt-1"></div>
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-lg glass text-center">
          <div class="text-sm muted">Total Tugas</div>
          <div class="text-2xl font-bold text-purple-200">{{ $total ?? 0 }}</div>
        </div>

        <div class="p-4 rounded-lg glass text-center">
          <div class="text-sm muted">Selesai</div>
          <div class="text-2xl font-bold text-green-300">{{ $done ?? 0 }}</div>
        </div>

        <div class="p-4 rounded-lg glass text-center">
          <div class="text-sm muted">Belum</div>
          <div class="text-2xl font-bold text-yellow-300">{{ $pending ?? 0 }}</div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="glass p-6 rounded-2xl">
        <h3 class="font-semibold text-lg">Progress Chart</h3>
        <div class="mt-4"><canvas id="taskChart" style="max-height:300px;"></canvas></div>
      </div>

      <div class="glass p-6 rounded-2xl">
        <div class="flex items-center justify-between">
          <h3 class="font-semibold text-lg">Upcoming Tasks</h3>
          <a href="{{ route('tasks.index') }}" class="text-sm muted underline">Lihat semua</a>
        </div>

        <div class="mt-4 space-y-3 max-h-[300px] overflow-auto pr-2">
          @forelse($upcoming as $t)
            <div class="glass p-3 rounded-md flex justify-between items-center">
              <div class="min-w-0">
                <div class="font-semibold truncate">{{ $t->name }}</div>
                <div class="text-sm muted truncate">{{ $t->description ?: '-' }}</div>
              </div>
              <div class="text-right">
                <div class="text-sm muted">Due</div>
                <div class="font-semibold">{{ $t->due_date ? $t->due_date->format('d M Y') : '-' }}</div>
              </div>
            </div>
          @empty
            <div class="muted">Tidak ada tugas mendatang.</div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="glass p-6 rounded-2xl">
      <h3 class="font-semibold mb-3">Daily Summary</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="p-3 glass rounded text-center">
          <div class="text-sm muted">Tugas Hari Ini</div>
          <div class="text-xl font-bold">{{ $todayCount ?? 0 }}</div>
        </div>
        <div class="p-3 glass rounded text-center">
          <div class="text-sm muted">Terlambat</div>
          <div class="text-xl font-bold text-orange-300">{{ $overdueCount ?? 0 }}</div>
        </div>
        <div class="p-3 glass rounded text-center">
          <div class="text-sm muted">Progress Subtasks</div>
          <div class="text-xl font-bold">{{ $avgSubtaskProgress ?? 0 }}%</div>
        </div>
      </div>
    </div>

  </div>

  <div class="space-y-6">
    <div class="glass p-6 rounded-2xl">
      <h3 class="font-semibold">Category Summary</h3>
      <div class="space-y-2 mt-3">
        @forelse($categories as $cat)
          <div class="flex items-center justify-between p-3 glass rounded">
            <div>
              <div class="font-semibold">{{ $cat->name }}</div>
              <div class="text-xs muted">{{ $cat->tasks_count }} tugas</div>
            </div>
            <div class="text-purple-300">{{ $cat->tasks_count }}</div>
          </div>
        @empty
          <div class="muted">Belum ada kategori.</div>
        @endforelse
      </div>
    </div>

    <div class="glass p-6 rounded-2xl">
      <h3 class="font-semibold mb-3">Kalender Bulanan</h3>
      <div id="full-calendar"></div>
    </div>

    <div class="glass p-5 rounded-2xl">
      <h3 class="font-semibold">Developer</h3>
      <p class="muted mt-2"><strong>Rizki Ramadhan Zafitra</strong></p>
      <p class="muted text-sm mt-1">Pembuat aplikasi ini.</p>
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
  // clock
  function updateClock(){
    const now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString();
    document.getElementById('date').textContent = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
  }
  setInterval(updateClock,1000); updateClock();

  // chart
  (function(){
    const ctx = document.getElementById('taskChart');
    if(!ctx) return;
    const labels = @json($chartData['labels'] ?? ['Selesai','Belum']);
    const values = @json($chartData['values'] ?? [0, 0]);

    new Chart(ctx, {
      type:'doughnut',
      data:{ labels, datasets:[{ data: values, backgroundColor:['#7C3AED','#A78BFA'], borderWidth:0 }]},
      options:{ plugins:{ legend:{ position:'bottom' } }, maintainAspectRatio:false }
    });
  })();

  // calendar (uses public/holidays.json)
  (function(){
    const container = document.getElementById('full-calendar');
    if(!container) return;
    fetch('/holidays.json').then(r=>r.json()).then(holidays=>{
      const upcoming = @json($upcoming->pluck('due_date')->map(fn($d)=> $d? $d->format('Y-m-d'):null));
      const marks = new Set(upcoming);
      let offset=0;
      function render(monthOffset=0){
        const now = new Date();
        const y = now.getFullYear();
        const m = now.getMonth()+monthOffset;
        const target = new Date(y, m, 1);
        const monthName = target.toLocaleString('id-ID',{ month:'long' });
        const displayYear = target.getFullYear();
        const daysInMonth = new Date(displayYear, target.getMonth()+1, 0).getDate();
        const firstDay = new Date(displayYear, target.getMonth(),1).getDay();

        let html = `<div class="flex items-center justify-between mb-3">
          <button id="cal-prev" class="px-2 py-1 rounded glass">Prev</button>
          <div class="text-center font-semibold">${monthName} ${displayYear}</div>
          <button id="cal-next" class="px-2 py-1 rounded glass">Next</button>
        </div>
        <div class="grid grid-cols-7 gap-2 text-center text-xs muted mb-2">
          <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
        </div>
        <div class="grid grid-cols-7 gap-2">`;

        for(let i=0;i<firstDay;i++) html += `<div></div>`;
        const today = new Date();
        for(let d=1; d<=daysInMonth; d++){
          const mm = String(target.getMonth()+1).padStart(2,'0');
          const dd = String(d).padStart(2,'0');
          const dateStr = `${target.getFullYear()}-${mm}-${dd}`;
          const isToday = (d===today.getDate() && target.getMonth()===today.getMonth() && target.getFullYear()===today.getFullYear());
          const isHoliday = holidays[String(target.getFullYear())] && holidays[String(target.getFullYear())][`${mm}-${dd}`];
          const isMarked = marks.has(dateStr);

          html += `<div class="p-2 rounded-lg ${isToday? 'bg-purple-600 text-white font-semibold':'glass text-gray-200'} relative">
            <div>${d}</div>
            ${isMarked?'<div class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 rounded-full bg-yellow-300"></div>':''}
            ${isHoliday?`<div class="text-[10px] text-red-300 mt-1">${isHoliday}</div>`:''}
          </div>`;
        }
        html += `</div>`;
        container.innerHTML = html;
        document.getElementById('cal-prev').onclick = ()=> render(monthOffset - 1);
        document.getElementById('cal-next').onclick = ()=> render(monthOffset + 1);
      }
      render(0);
    });
  })();
</script>
@endpush
