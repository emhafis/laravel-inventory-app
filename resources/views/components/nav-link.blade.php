@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center rounded-md px-2 py-1.5 text-sm font-medium bg-slate-800 text-white ring-1 ring-slate-700'
    : 'inline-flex items-center rounded-md px-2 py-1.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
