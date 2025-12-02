@props(['id','titulo'=>'','type'=>'bar','dataUrl'=>null])
<div class="bg-white rounded-2xl shadow p-4">
  <div class="flex items-center justify-between mb-2">
    <h3 class="font-semibold text-gray-800">{{ $titulo }}</h3>
  </div>
  <div class="h-72">
    <canvas id="{{ $id }}" data-type="{{ $type }}" data-url="{{ $dataUrl ?? ($attributes->get('data-url')) }}"></canvas>
  </div>
</div>

@once
  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      document.addEventListener('alpine:init', () => {
        // vazio — apenas para garantir Alpine antes se usar
      });
      document.addEventListener('DOMContentLoaded', () => {
        window._charts = window._charts || {};
        document.querySelectorAll('canvas[data-url]').forEach(async cv=>{
          const id=cv.id; if(window._charts[id]) return;
          const url=cv.dataset.url, type=cv.dataset.type||'bar';
          const res=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest'}});
          const json=await res.json();
          const ctx=cv.getContext('2d');
          window._charts[id]=new Chart(ctx,{type,
            data:{labels:json.labels||[],datasets:(json.datasets||[]).map(d=>({...d,borderWidth:2,tension:type==='line'?0.35:0}))},
            options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'top'}}}});
        });
      });
    </script>
  @endpush
@endonce
