<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Overlay fade-in ── */
        @keyframes overlayFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* ── Logo drop-in ── */
        @keyframes logoDrop {
            0%   { opacity: 0; transform: translateY(-30px) scale(0.92); }
            60%  { transform: translateY(4px) scale(1.02); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Heading slide-up ── */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Card rise ── */
        @keyframes cardRise {
            from { opacity: 0; transform: translateY(32px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Floating particles ── */
        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.15; }
            33%       { transform: translateY(-18px) translateX(8px); opacity: 0.3; }
            66%       { transform: translateY(-8px) translateX(-6px); opacity: 0.2; }
        }

        /* ── Subtle shimmer on logo ── */
        @keyframes shimmer {
            0%, 100% { filter: drop-shadow(0 0 0px rgba(255,255,255,0)); }
            50%       { filter: drop-shadow(0 0 12px rgba(255,255,255,0.25)); }
        }

        .animate-overlay {
            animation: overlayFadeIn 0.8s ease both;
        }

        .animate-logo {
            animation: logoDrop 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both,
                       shimmer 4s ease-in-out 1.5s infinite;
        }

        .animate-heading {
            animation: slideUp 0.6s ease 0.65s both;
        }

        .animate-card {
            animation: cardRise 0.7s cubic-bezier(0.22, 1, 0.36, 1) 0.85s both;
        }

        /* ── Particle dots ── */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            pointer-events: none;
        }
        .p1  { width:6px;  height:6px;  top:12%;  left:8%;   animation: float 7s ease-in-out 0s infinite; }
        .p2  { width:4px;  height:4px;  top:25%;  left:82%;  animation: float 9s ease-in-out 1s infinite; }
        .p3  { width:8px;  height:8px;  top:60%;  left:15%;  animation: float 8s ease-in-out 2s infinite; }
        .p4  { width:5px;  height:5px;  top:75%;  left:70%;  animation: float 6s ease-in-out 0.5s infinite; }
        .p5  { width:3px;  height:3px;  top:40%;  left:90%;  animation: float 10s ease-in-out 3s infinite; }
        .p6  { width:7px;  height:7px;  top:88%;  left:40%;  animation: float 7.5s ease-in-out 1.5s infinite; }

        /* ── Card hover lift ── */
        .card-hover {
            transition: box-shadow 0.4s ease, transform 0.4s ease;
        }
        .card-hover:hover {
            box-shadow: 0 24px 48px rgba(0,0,0,0.18);
            transform: translateY(-2px);
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #dc2626 !important; /* red-600 */
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900 relative overflow-hidden"
      style="background-image: url('{{ asset('bg-login.png') }}'); 
             background-size: cover; ">

    <!-- Background dimmer — animated -->
    <div class="absolute inset-0 bg-black/40 animate-overlay"></div>

    <!-- Main content -->
    <div class="relative flex flex-col items-center min-h-screen pt-0 sm:pt-0 mt-16">

        <!-- Logo + tagline -->
        <div class="text-center">
            <img src="{{ asset('logo-kobin-one.png') }}"
                 alt="Kobin Tiles Logo"
                 class="mx-auto animate-logo"
                 style="height:100px;">
        </div>

        <div class="text-center mt-3">
            <div class="flex items-center justify-center gap-3 animate-heading">
                <h3 class="text-xl tracking-wide text-white">
                    Kobin Attendance System
                </h3>
            </div>
        </div>

        <!-- Card -->
        <div class="w-full px-6 py-4 mt-1 animate-card overflow-hidden
                    sm:max-w-md sm:rounded-lg">
            {{ $slot }}
        </div>
        
        <div class="text-center mt-6">
            <div class="flex items-center justify-center gap-3 animate-heading">
                <h3 class="text-1xl tracking-wide text-white">
                    by
                </h3>

                <img src="{{ asset('kobin-tiles-logo-white.png') }}"
                    alt="Kobin Tiles Logo"
                    class="w-32 h-auto">
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>