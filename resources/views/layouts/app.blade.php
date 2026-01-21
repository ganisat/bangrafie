<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800">
<div class="min-h-screen flex">

    {{-- Sidebar (desktop) - STICKY --}}
    <aside class="hidden lg:flex lg:w-72 lg:flex-col bg-white border-r border-slate-200
                  lg:sticky lg:top-0 lg:h-screen">

        {{-- Brand --}}
        <div class="h-16 flex items-center px-6 border-b border-slate-200 shrink-0">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold">
                    TD
                </div>
                <div>
                    <p class="text-sm font-semibold leading-4">Toko Dashboard</p>
                    <p class="text-xs text-slate-500">Ringkas • Cepat • Rapi</p>
                </div>
            </div>
        </div>

        {{-- Menu (scrollable kalau panjang) --}}
        <nav class="px-3 py-4 space-y-1 text-sm flex-1 overflow-y-auto">
            @php
                $items = [
                    ['name'=>'Beranda', 'route'=>'dashboard'],
                    ['name'=>'Penjualan', 'route'=>'penjualan'],
                    ['name'=>'Produk & Kategori', 'route'=>'produk_kategori'],
                    ['name'=>'Stok & Reorder', 'route'=>'stok_reorder'],
                    ['name'=>'Cashflow', 'route'=>'cashflow'],
                    ['name'=>'Piutang/Utang', 'route'=>'piutang_utang'],
                    ['name'=>'Pasar', 'route'=>'pasar'],
                    ['name'=>'Laporan', 'route'=>'laporan'],
                    ['name'=>'Pengaturan', 'route'=>'pengaturan'],
                ];
            @endphp

            @foreach($items as $it)
                @php $active = request()->routeIs($it['route']); @endphp
                <a href="{{ route($it['route']) }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                   {{ $active ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <span class="h-2 w-2 rounded-full {{ $active ? 'bg-white' : 'bg-slate-300' }}"></span>
                    <span class="font-medium">{{ $it['name'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Tips (tetap di bawah, tidak ikut scroll menu) --}}
        <div class="p-4 shrink-0">
            <div class="rounded-2xl bg-slate-900 text-white p-4">
                <p class="text-sm font-semibold">Tips</p>
                <p class="text-xs text-slate-200 mt-1">
                    Mulai dari Dashboard, lalu isi data transaksi & produk.
                </p>
            </div>
        </div>
    </aside>

    {{-- Right side: Topbar + Content --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Mobile menu button (dummy) --}}
                <div class="lg:hidden">
                    <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                        ☰
                    </div>
                </div>

                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate">@yield('header', 'Dashboard')</p>
                    <p class="text-xs text-slate-500 truncate">@yield('subheader', 'Ringkasan performa toko hari ini')</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:block">
                    <input class="w-72 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-300"
                           placeholder="Cari transaksi / produk...">
                </div>
                <div class="h-10 w-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-semibold">
                    U
                </div>
            </div>
        </header>

        {{-- IMPORTANT: yang scroll = area ini --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-8">
            @yield('content')
            <div class="py-6 text-xs text-slate-500">
                © {{ date('Y') }} Toko Dashboard • Laravel + Tailwind
            </div>
        </main>

    </div>
</div>
</body>
</html>
