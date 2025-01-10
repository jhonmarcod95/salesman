<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.head')
</head>
<body>

<div id="app">
<version-release :authenticated="false" :user-roles="[]"></version-release>
</div>

@include('layouts.script')

</body>
</html>
