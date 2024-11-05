<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/ptiguili.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <header id="mainHeader">
        <a href="{{ route('gifts') }}"><img src="/logo.svg" alt="Ptiguili"></a>
        <span class="totalCoins">{{ Auth::user()->wallet->coins }}</span>
    </header>
    <hr>

    @yield('content')
    <div id="flashes" class="flashes">
        @foreach (\App\Services\Flashes::all() as $flash)
            <div {!! $flash->class() !!}>
                {{ $flash->content }}
            </div>
        @endforeach
    </div>
</body>

</html>
