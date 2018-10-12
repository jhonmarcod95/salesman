<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'Laravel') }}</title>

<!-- Favicon -->
<link href="{{ url('img/brand/favicon.png') }}" rel="icon" type="image/png">

<!-- Icons -->
<link href="{{ url('vendor/nucleo/css/nucleo.css') }}" rel="stylesheet">
<link href="{{ url('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
<link href="{{ url('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

<!-- Argon CSS -->
<link type="text/css" href="{{ url('css/argon.css?v=1.0.0') }}" rel="stylesheet">

<!-- Select2 -->
<link type="text/css" href="{{ url('select2/select2.min.css') }}" rel="stylesheet">
<link href='{{ url('fullcalendar/fullcalendar.min.css') }}' rel='stylesheet' />
<link href='{{ url('fullcalendar/fullcalendar.print.min.css') }}' rel='stylesheet' media='print' />

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

{{--<script src="{{ asset('js/all.js') }}" defer></script>--}}