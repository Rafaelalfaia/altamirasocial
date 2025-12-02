@if ($errors->any())
  <div class="mb-4 rounded border border-red-200 bg-red-50 p-4 text-red-700">
    <div class="font-semibold">Corrija os erros abaixo:</div>
    <ul class="mt-2 list-disc pl-5 space-y-1">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if (session('error'))
  <div class="mb-4 rounded border border-red-200 bg-red-50 p-4 text-red-700">
    {{ session('error') }}
  </div>
@endif
