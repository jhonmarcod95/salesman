<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.head')
</head>
<body>

<div id="app">
<version-release :authenticated="false" :user-roles="[]"></version-release>
<span class="mt-4 h3 px-2 py-4">
    <a class="text-primary" href="{{ url('/home') }}"><i class="fas fa-chevron-left mr-2"></i>Return to login</a>
</span>
</div>

@include('layouts.script')

</body>
</html>
