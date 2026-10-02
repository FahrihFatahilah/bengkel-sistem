@props(['title', 'value', 'color' => 'blue', 'sub' => null])
<div class="bg-white rounded-lg border border-gray-200 p-4">
    <p class="text-xs text-gray-500 font-medium">{{ $title }}</p>
    <p class="text-2xl font-bold mt-1 text-{{ $color }}-600">{{ $value }}</p>
    @if($sub)<p class="text-xs text-gray-400 mt-0.5">{{ $sub }}</p>@endif
</div>
