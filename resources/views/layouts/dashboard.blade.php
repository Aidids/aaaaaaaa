<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <!-- font awesome  -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous" />

    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css'])
</head>

<body>
    <div class="d-flex" id="app">
        @include('layouts.sidebar')
        <div class="main-body">
            @include('layouts.navbar')
            <div class="main-window" style="page-break-inside: avoid;">
            @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        let token = "{{ Auth::user()->remember_token }}";
        let isAdmin = "{{ Auth::user()->is_admin }}";
        let user_id = "{{ Auth::user()->id }}";
        let currentUrl = "{{env('APP_URL')}}";

        localStorage.setItem('token', token);
        localStorage.setItem('isAdmin', isAdmin);
        localStorage.setItem('user_id', user_id);
        localStorage.setItem('currentUrl', currentUrl);
    </script>
</body>
</html>
