@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
])

@php
    $siteTitle = setting('meta_title', config('app.name'));
    $metaTitle = $title ? $title.' – '.$siteTitle : $siteTitle;
    $metaDescription = $description ?? setting('meta_description');
    // OG image: dari halaman → pengaturan situs → gambar hero bawaan. Harus URL absolut.
    $metaImage = $image ?? upload_url(setting('og_image')) ?? asset('images/hero.png');
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

    <meta property="og:type" content="{{ $type }}">
    <meta property="og:site_name" content="{{ $siteTitle }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    @if ($metaDescription)
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:locale" content="id_ID">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    @if ($metaDescription)
        <meta name="twitter:description" content="{{ $metaDescription }}">
    @endif
    <meta name="twitter:image" content="{{ $metaImage }}">

    <link rel="icon" type="image/png" href="{{ asset('images/mascot-head.png') }}">
    {{-- Meta tidak bisa membaca CSS variable; nilai ini = token "bg" di app.css. --}}
    <meta name="theme-color" content="#F7F7F5">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <a href="#konten" class="sr-only rounded-pill bg-ink text-sm font-semibold text-card focus:not-sr-only focus:px-5 focus:py-3 focus:fixed focus:top-4 focus:left-4 focus:z-[60]">
        Lewati ke konten
    </a>

    {{ $header ?? '' }}

    <main id="konten">
        {{ $slot }}
    </main>
</body>
</html>
