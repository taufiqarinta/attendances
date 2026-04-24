<nav x-data="{ open: false }"
    style="
    background-image: url('{{ asset('bg-login.png') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    ">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('welcome') }}">
                        {{-- <x-application-logo class="block w-auto text-white fill-current h-9" /> --}}
                        <img src="{{ asset('logo-kobin-one.png') }}" alt="Logo Kobin" class="w-24 h-10">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')"
                        class="text-white"
                        onmouseover="this.style.color='#dc2626'"
                        onmouseout="this.style.color='white'">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('biodata')" :active="request()->routeIs('biodata')"
                        class="text-white"
                        onmouseover="this.style.color='#dc2626'"
                        onmouseout="this.style.color='white'">
                        {{ __('Biodata') }}
                    </x-nav-link>

                    <!-- <div class="hidden sm:flex sm:items-center sm:ms-10 granitfiesta">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('daftartoko.*') ? 'text-white' : '' }}"
                                    onmouseover="this.style.color='#dc2626'"
                                    onmouseout="this.style.color='white'">
                                    <div>{{ __('Biodata') }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('biodata')" :active="request()->routeIs('biodata')">
                                    {{ __('Biodata') }}
                                </x-dropdown-link>
                                @if(session('comp') == '0001' && session('nik') == '924330')
                                <x-dropdown-link :href="route('approval.biodata')" :active="request()->routeIs('approval.biodata')">
                                    {{ __('Approval Biodata') }}
                                </x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div> -->

                    <div class="hidden sm:flex sm:items-center sm:ms-10 granitfiesta">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('daftartoko.*') ? 'text-white' : '' }}"
                                    onmouseover="this.style.color='#dc2626'"
                                    onmouseout="this.style.color='white'">
                                    <div>{{ __('Data Absensi') }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('absensi.index')" :active="request()->routeIs('absensi.index')">
                                    {{ __('Absensi') }}
                                </x-dropdown-link>
                                @if(session('comp') == '0001' && session('nik') == '924330')
                                <x-dropdown-link :href="route('allabsensi.index')" :active="request()->routeIs('allabsensi.index')">
                                    {{ __('Absensi Seluruh Karyawan') }}
                                </x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ms-10 granitfiesta">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('daftartoko.*') ? 'text-white' : '' }}"
                                    onmouseover="this.style.color='#dc2626'"
                                    onmouseout="this.style.color='white'">
                                    <div>{{ __('Time Management') }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('izin.index')" :active="request()->routeIs('izin.*')">
                                    {{ __('Leave') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('selfreport.index')" :active="request()->routeIs('selfreport.*')">
                                    {{ __('Report') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    @if(session('comp') == '0001' && session('nik') == '924330')
                    <div class="hidden sm:flex sm:items-center sm:ms-10 granitfiesta">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('daftartoko.*') ? 'text-white' : '' }}"
                                    onmouseover="this.style.color='#dc2626'"
                                    onmouseout="this.style.color='white'">
                                    <div>{{ __('Summary Report') }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('report.index')" :active="request()->routeIs('report.index')">
                                    {{ __('Employee') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('summary.index')" :active="request()->routeIs('summary.index')">
                                    {{ __('Plant') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif

                    @if(session('comp') == '0001' && session('nik') == '924330')
                    <div class="hidden sm:flex sm:items-center sm:ms-10 granitfiesta">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('daftartoko.*') ? 'text-white' : '' }}"
                                    onmouseover="this.style.color='#dc2626'"
                                    onmouseout="this.style.color='white'">
                                    <div>{{ __('Application') }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('approval.page')" :active="request()->routeIs('approval.page')">
                                    {{ __('Leave Approval') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('history.index')" :active="request()->routeIs('history.index')">
                                    {{ __('History') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif
                </div>
                
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out text-white border border-transparent rounded-md focus:outline-none"
                                onmouseover="this.style.color='#dc2626'"
                                onmouseout="this.style.color='white'">
                            <div>{{ session('username') }}</div>

                            <div class="ms-1">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Change Password') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="GET" action="{{ route('logout.get') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout.get')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 text-white transition duration-150 ease-in-out rounded-md focus:outline-none"
                        onmouseover="this.style.color='#dc2626'"
                        onmouseout="this.style.color='white'">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')"
                style="{{ request()->routeIs('welcome') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                onmouseover="this.style.color='#dc2626'"
                onmouseout="this.style.color='{{ request()->routeIs('welcome') ? '#dc2626' : 'white' }}'">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <x-responsive-nav-link :href="route('biodata')" :active="request()->routeIs('biodata')"
            style="{{ request()->routeIs('biodata') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='{{ request()->routeIs('biodata') ? '#dc2626' : 'white' }}'">
            {{ __('Biodata') }}
        </x-responsive-nav-link>
        @if(session('comp') == '0001' && session('nik') == '924330')
        <!-- <x-responsive-nav-link :href="route('approval.biodata')" :active="request()->routeIs('approval.biodata')"
            style="{{ request()->routeIs('approval.biodata') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='{{ request()->routeIs('approval.biodata') ? '#dc2626' : 'white' }}'">
            {{ __('Approval Biodata') }}
        </x-responsive-nav-link> -->
        @endif
        <x-responsive-nav-link :href="route('absensi.index')" :active="request()->routeIs('absensi.index')"
            style="{{ request()->routeIs('absensi.index') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='{{ request()->routeIs('absensi.index') ? '#dc2626' : 'white' }}'">
            {{ __('Absensi') }}
        </x-responsive-nav-link>
        
        @if(session('comp') == '0001' && session('nik') == '924330')
        <x-responsive-nav-link :href="route('allabsensi.index')" :active="request()->routeIs('allabsensi.index')"
            style="{{ request()->routeIs('absensi.index') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='{{ request()->routeIs('allabsensi.index') ? '#dc2626' : 'white' }}'">
            {{ __('Absensi Seluruh Karyawan') }}
        </x-responsive-nav-link>
        @endif
        
        <x-responsive-nav-link :href="route('izin.index')" :active="request()->routeIs('izin.index')"
            style="{{ request()->routeIs('izin.index') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='{{ request()->routeIs('izin.index') ? '#dc2626' : 'white' }}'">
            {{ __('Leave') }}
        </x-responsive-nav-link>
        
        <x-responsive-nav-link :href="route('selfreport.index')" :active="request()->routeIs('selfreport.index')"
            style="{{ request()->routeIs('selfreport.index') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='{{ request()->routeIs('selfreport.index') ? '#dc2626' : 'white' }}'">
            {{ __('Report') }}
        </x-responsive-nav-link>
        
        @if(session('comp') == '0001' && session('nik') == '924330')
        <x-responsive-nav-link :href="route('report.index')" :active="request()->routeIs('report.index')"
            style="{{ request()->routeIs('report.index') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='{{ request()->routeIs('report.index') ? '#dc2626' : 'white' }}'">
            {{ __('Summary Employee') }}
        </x-responsive-nav-link>
        
        <x-responsive-nav-link :href="route('summary.index')" :active="request()->routeIs('summary.index')"
            style="{{ request()->routeIs('summary.index') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='{{ request()->routeIs('summary.index') ? '#dc2626' : 'white' }}'">
            {{ __('Summary Plant') }}
        </x-responsive-nav-link>
        
        <x-responsive-nav-link :href="route('approval.page')" :active="request()->routeIs('approval.page')"
            style="{{ request()->routeIs('approval.page') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='{{ request()->routeIs('approval.page') ? '#dc2626' : 'white' }}'">
            {{ __('Leave Approval') }}
        </x-responsive-nav-link>
        
        <x-responsive-nav-link :href="route('history.index')" :active="request()->routeIs('history.index')"
            style="{{ request()->routeIs('history.index') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
            onmouseover="this.style.color='#dc2626'"
            onmouseout="this.style.color='{{ request()->routeIs('history.index') ? '#dc2626' : 'white' }}'">
            {{ __('History Approval') }}
        </x-responsive-nav-link>
        @endif

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="text-base font-medium text-white">{{ session('username') }}</div>
                <div class="text-sm font-medium text-white">{{ session('email') }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')"
                    :active="request()->routeIs('profile.edit')"
                    style="{{ request()->routeIs('profile.edit') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('profile.edit') ? '#dc2626' : 'white' }}'">
                    {{ __('Profile') }}
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="GET" action="{{ route('logout.get') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout.get')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        style="{{ request()->routeIs('logout.get') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                        onmouseover="this.style.color='#dc2626'"
                        onmouseout="this.style.color='{{ request()->routeIs('logout.get') ? '#dc2626' : 'white' }}'">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
