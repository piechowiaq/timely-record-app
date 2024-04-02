<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Timely Record') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Favicon -->
    <link rel="icon" href="https://timely-record.s3.eu-central-1.amazonaws.com/public/timely_record_favicon.svg"
          type="image/svg+xml">

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
    <script src="https://kit.fontawesome.com/99f9d18d54.js" crossorigin="anonymous"></script>
</head>
<body class="font-sans antialiased">

@inertia
</body>
</html>
