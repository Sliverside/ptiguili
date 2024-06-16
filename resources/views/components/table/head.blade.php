@php
  $rootAttributes = $attributes
    ->except(['headings'])
    ->class([
      'bg-gray-400',
      'text-gray-700',
      'text-left',
      'uppercase'
    ]);
@endphp
<thead {{ $rootAttributes }}>
  <tr>
    @foreach ($headings as $head)
      <th class="p-3">{{ $head }}</th>
    @endforeach
  </tr>
</thead>
