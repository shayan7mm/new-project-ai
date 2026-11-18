<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- فونت و آیکون‌ها از خود سایت لود می‌شوند (بدون CDN) --}}
<link rel="preload" href="{{ asset('fonts/vazirmatn/Vazirmatn-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="{{ asset('fonts/vazirmatn/vazirmatn.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
