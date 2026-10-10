@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'noindex' => false,
])

@php
    $siteTitle = setting('meta_title', config('app.name'));
    $metaTitle = $title ? $title.' – '.$siteTitle : $siteTitle;
    $metaDescription = $description ?? setting('meta_description');
    // OG image: dari halaman → pengaturan situs → gambar hero bawaan. Harus URL absolut.
    $ogUpload = $image ?? upload_url(setting('og_image'));
    $metaImage = $ogUpload ? url($ogUpload) : asset('images/og-default.jpg');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle }}</title>
    @if ($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    @if ($noindex)
        <meta name="robots" content="noindex, nofollow">
    @endif

    <meta property="og:type" content="{{ $type }}">
    <meta property="og:site_name" content="{{ $siteTitle }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    @if ($metaDescription)
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:image:alt" content="{{ $metaTitle }}">
    @unless ($ogUpload)
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endunless
    <meta property="og:locale" content="id_ID">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    @if ($metaDescription)
        <meta name="twitter:description" content="{{ $metaDescription }}">
    @endif
    <meta name="twitter:image" content="{{ $metaImage }}">

    {{-- Favicon dari kepala maskot --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    {{-- Meta tidak bisa membaca CSS variable; nilai ini = token "bg" di app.css. --}}
    <meta name="theme-color" content="#F7F7F5">
    {{-- Preload 2 font yang langsung terlihat di layar pertama (heading Archivo & body Inter); sisanya font-display: swap. --}}
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('node_modules/@fontsource-variable/archivo/files/archivo-latin-wght-normal.woff2') }}">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('node_modules/@fontsource-variable/inter/files/inter-latin-wght-normal.woff2') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="flex min-h-screen flex-col">
    {{ $overlay ?? '' }}

    <a href="#konten" class="sr-only rounded-pill bg-ink text-sm font-semibold text-card focus:not-sr-only focus:px-5 focus:py-3 focus:fixed focus:top-4 focus:left-4 focus:z-[60]">
        Lewati ke konten
    </a>

    {{ $header ?? '' }}

    <main id="konten">
        {{ $slot }}
    </main>

    <x-site.footer />

    {{ $floating ?? '' }}
</body>
</html>
