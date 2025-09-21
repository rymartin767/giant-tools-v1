<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
<meta name="mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta name="theme-color" content="#3b82f6" />

<title>{{ $title ?? config('app.name') }}</title>

<!-- PWA Manifest -->
<link rel="manifest" href="/manifest.json" />

<!-- Icons -->
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="72x72" href="/icons/icon-72x72.png">
<link rel="apple-touch-icon" sizes="96x96" href="/icons/icon-96x96.png">
<link rel="apple-touch-icon" sizes="128x128" href="/icons/icon-128x128.png">
<link rel="apple-touch-icon" sizes="144x144" href="/icons/icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
<link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">
<link rel="apple-touch-icon" sizes="384x384" href="/icons/icon-384x384.png">
<link rel="apple-touch-icon" sizes="512x512" href="/icons/icon-512x512.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
