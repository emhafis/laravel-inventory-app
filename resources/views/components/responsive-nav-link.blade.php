@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full rounded-md px-3 py-2 text-start text-base font-medium text-white bg-slate-800'
    : 'block w-full rounded-md px-3 py-2 text-start text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
