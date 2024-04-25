<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ ($title ?? '') . config('app.name') }}</title>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/gridlex/2.7.1/gridlex.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/tippy.js@6.3.7/dist/tippy.min.css">
    @livewireStyles
    <style>
        body {
            max-width: 1024px;
        }
        input[type="date"]:before,
        input[type="time"]:before{
            content: attr(placeholder) !important;
            color: #949494;
            margin-right: 0.5em;
        }
        input[type="date"]:focus:before,
        input[type="date"]:valid:before,
        input[type="time"]:focus:before,
        input[type="time"]:valid:before {
            content: "";
        }
    </style>
    @turnstileScripts()
</head>
<body>
<nav>
    <h1>RSVPnGo</h1>
</nav>

<main>
    {{ $slot }}
</main>

<footer>
    RSVPnGo is a free to use event RSVP app. @if(request()->path() !== '/')<a href="/">Create your own event, now.</a>@endif
</footer>
@livewireScripts
<script src="//cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
<script src="//unpkg.com/@popperjs/core@2"></script>
<script src="//unpkg.com/tippy.js@6"></script>
<script>
    var clipboard = new ClipboardJS('.copy-btn');

    clipboard.on('success', function(e) {

        var tooltip = tippy(e.trigger, {
            content: "Copied!"
        });

        setTimeout(function () {
            tooltip.destroy();
        }, 500)

        e.clearSelection();
    });
</script>
</body>
</html>
