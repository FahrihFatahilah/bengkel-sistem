@props(['href', 'active' => false])
<a href="{{ $href }}"
   @click="if (mobile) sidebarOpen = false"
   class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm transition-colors
          {{ $active ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
    @isset($icon)<span class="text-base leading-none">{{ $icon }}</span>@endisset
    {{ $slot }}
</a>
