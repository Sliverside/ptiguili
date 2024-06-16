@php
$rootAttributes = $attributes
    ->except(['link'])
    ->class([
        'inline-flex',
        'items-center',
        'px-4',
        'py-2',
        'bg-white',
        'dark:bg-gray-800',
        'border',
        'border-gray-300',
        'dark:border-gray-500',
        'rounded-md',
        'font-semibold',
        'text-xs',
        'text-gray-700',
        'dark:text-gray-300',
        'uppercase',
        'tracking-widest',
        'shadow-sm',
        'hover:bg-gray-50',
        'dark:hover:bg-gray-700',
        'focus:outline-none',
        'focus:ring-2',
        'focus:ring-indigo-500',
        'focus:ring-offset-2',
        'dark:focus:ring-offset-gray-800',
        'disabled:opacity-25',
        'transition',
        'ease-in-out',
        'duration-150']);

if($attributes->has('link')) $rootAttributes = $rootAttributes->except(['href']);
else $rootAttributes = $rootAttributes->merge(['type' => 'button']);
@endphp

@if($attributes->has('link'))
<a href="{{ $link }}" {{ $rootAttributes }}>
    {{ $slot }}
</a>
@else
<button {{ $rootAttributes }}>
    {{ $slot }}
</button>
@endif
