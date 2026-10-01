@props(['title' => null, 'description' => null, 'noindex' => false, 'og_image' => null, 'og_url' => null])

@php
    $fullTitle   = $title ? $title.' | Local-Farm-Fresh' : 'Local-Farm-Fresh | Fresh. Local. Direct.';
    $fullDesc    = $description ?? 'Buy fresh produce directly from small-scale farmers near you. Pay cash on delivery.';
    $canonicalUrl = $og_url ?? url()->current();
@endphp

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#0F3D27">
@if ($noindex)
<meta name="robots" content="noindex, nofollow">
@endif
<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $fullDesc }}">

{{-- Open Graph --}}
<meta property="og:site_name" content="Local-Farm-Fresh">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $fullDesc }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $canonicalUrl }}">
@if ($og_image)
<meta property="og:image" content="{{ $og_image }}">
<meta name="twitter:card" content="summary_large_image">
@else
<meta name="twitter:card" content="summary">
@endif

<link rel="canonical" href="{{ $canonicalUrl }}">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
