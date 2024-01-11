@php
  $rootAttributes = $attributes
    ->except(['link'])
    ->class([$attributes->has('link') ? 'p-0' : 'p-3']);
@endphp
<td {{ $rootAttributes }}>
  @if ($attributes->has('link'))
    <a class="block p-3" href="{{ $link }}">{{ $slot }}</a>
  @else
    {{ $slot }}
  @endif
</td>
