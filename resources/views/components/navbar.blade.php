<header class="bg-white px-10 py-4 flex justify-end relative">
    <div class="flex gap-2 items-center">
        <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M13.5 0C6.05618 0 0 6.05618 0 13.5C0 20.9438 6.05618 27 13.5 27C20.9438 27 27 20.9438 27 13.5C27 6.05618 20.9438 0 13.5 0ZM13.6298 6.23077C14.5541 6.23077 15.4575 6.50484 16.226 7.01832C16.9945 7.53181 17.5935 8.26164 17.9472 9.11554C18.3009 9.96943 18.3934 10.909 18.2131 11.8155C18.0328 12.722 17.5877 13.5547 16.9342 14.2082C16.2806 14.8618 15.448 15.3068 14.5415 15.4871C13.635 15.6674 12.6954 15.5749 11.8415 15.2212C10.9876 14.8675 10.2578 14.2686 9.74429 13.5001C9.2308 12.7316 8.95673 11.8281 8.95673 10.9038C8.95673 9.66447 9.44907 8.47585 10.3254 7.59948C11.2018 6.72311 12.3904 6.23077 13.6298 6.23077ZM13.5 24.9231C11.9244 24.9237 10.3659 24.5976 8.92273 23.9653C7.4796 23.333 6.1833 22.4083 5.11572 21.2495C5.68947 18.2782 10.8428 17.6538 13.5 17.6538C16.1572 17.6538 21.3105 18.2782 21.8843 21.2489C20.8168 22.4078 19.5206 23.3327 18.0774 23.9652C16.6343 24.5976 15.0756 24.9238 13.5 24.9231Z"
                fill="#374957" />
        </svg>
        <!-- Tombol Hai Admin -->
      @if (Auth::check())
    <button onclick="toggleLogoutDropdown()" class="font-semibold text-[#374957] relative focus:outline-none">
        Hai,
        @if ($role === 'admin')
            Ketua ZI
        @elseif ($role === 'manager')
           Manager Area {{ $areaUser }}
        @else
            {{ ucfirst($role ?? 'User') }}
        @endif
    </button>
@endif

        <!-- Dropdown Logout -->
        <div id="logoutDropdown" class="hidden absolute right-10 top-16 w-28 -mt-1 bg-[#374957] shadow-lg rounded-md z-50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class=" gap-2 w-full text-left px-4 py-2 text-sm text-white hover:bg-[#146082] flex items-center rounded-md">
                    <svg class="mt-1" width="15" height="15" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.1875 2H23.875C25.6009 2 27 3.39911 27 5.125V23.875C27 25.6009 25.6009 27 23.875 27H19.1875M8.25 8.25L2 14.5M2 14.5L8.25 20.75M2 14.5H20.75" stroke="white" stroke-width="3.75" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>                        
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

@push('scripts')
    <script src="{{ asset('js/logout.js') }}"></script>
@endpush
