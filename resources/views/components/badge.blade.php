@props(['value' => null, 'variant' => 'default'])
@php
$classes = match($variant) {
    'success'     => 'bg-green-100 text-green-800',
    'warning'     => 'bg-yellow-100 text-yellow-800',
    'danger'      => 'bg-red-100 text-red-800',
    'secondary'   => 'bg-gray-100 text-gray-700',
    default       => 'bg-blue-100 text-blue-800',
};
@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $classes }}">
    {{ $value ?? $slot }}
</span>
