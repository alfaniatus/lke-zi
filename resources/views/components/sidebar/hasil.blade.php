@if ($role === 'manager')
<a href="{{ route('manager-area.hasil.index') }}"
   class="flex items-center space-x-2 rounded-lg py-2 w-full px-2
       {{ request()->routeIs('manager-area.hasil.index') ? 'bg-[#146082] text-white' : 'text-[#374957]' }}
       transition-all hover:bg-[#146082] hover:text-white">

    <svg class="text-inherit" width="18" height="18" viewBox="0 0 24 24" fill="none"
         xmlns="http://www.w3.org/2000/svg">
        <rect x="2" y="2" width="20" height="20" rx="3" ry="3"
              stroke="currentColor" stroke-width="2" fill="none" />
        <path d="M6 13L10 17L18 9" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>

    <span class="font-semibold mt-1">Hasil</span>
</a>
@endif
