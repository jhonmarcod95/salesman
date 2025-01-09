<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.head')
</head>
<body>

<div id="app">

    @include('layouts.sidebar')

    <div class="main-content">
        <div class="pb-6"> @include('layouts.nav') </div>

        <div class="p-1"> @yield('content') </div>

        @include('layouts.footer')
    </div>
</div>

@include('layouts.script')

</body>
</html>
