<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.head')
</head>
<body>

<div id="app">

    <div class="main-content">
        @include('layouts.nav')

        @yield('content')

        @include('layouts.footer')
    </div>
</div>

@include('layouts.script')

</body>
</html>
