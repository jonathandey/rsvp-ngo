<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ ($title ?? '') . config('app.name') }}</title>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/gridlex/2.7.1/gridlex.min.css">
    @livewireStyles
</head>
<body>
<nav>
    <h1>RSVPnGo</h1>
</nav>

<main>
    {{ $slot }}
</main>

<footer>
    RSVPnGo is a free to use event RSVP app. @if(request()->path() !== '/')<a href="/">Create your own event now.</a>@endif
</footer>
@livewireScripts
<script src="//cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
</body>
</html>
