<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.head')
</head>
<body>

<div id="app">
<version-release :authenticated="false" :user-roles="[]"></version-release>
<a class="h2 p-2 text-primary text-center" href="{{ url('/home') }}">Return to login</a>
</div>

@include('layouts.script')

</body>
</html>
