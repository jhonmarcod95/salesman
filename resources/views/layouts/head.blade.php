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
<link type="text/css" href="{{ url('css/style.css') }}" rel="stylesheet">

<!-- Select2 -->
<link type="text/css" href="{{ url('select2/select2.min.css') }}" rel="stylesheet">
<link href='{{ url('fullcalendar/fullcalendar.min.css') }}' rel='stylesheet' />
<link href='{{ url('fullcalendar/fullcalendar.print.min.css') }}' rel='stylesheet' media='print' />

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">


<!-- Mapbox GL CSS -->
<link href="{{ url('css/mapbox-gl.css') }}" rel="stylesheet"/>

<!-- Mapbox GL JS -->
<script src="{{ url('js/mapbox-gl.js') }}"></script>

<!-- Mapbox GL DRAW CSS -->
<link rel='stylesheet' href="{{ url('css/mapbox-gl-draw.css') }}" type='text/css'/>

<!-- Mapbox GL GEOCODER CSS -->
<link href="{{ url('css/mapbox-gl-geocoder.css') }}" rel="stylesheet"/>


{{-- Loading Screen --}}
<style>
    #loading {
        background-color: rgba(0, 0, 0, 0.25);
        z-index: 999;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        display: none;
    }
    #geocoder {
        z-index: 1;
        width: 100%;
        text-align: center;
        top: 20px;
    }
    .mapboxgl-ctrl-geocoder {
        min-width: 100%;
    }
</style>

<script src="{{ url('js/turf.min.js') }}"></script>
<script src="{{ url('js/mapbox-gl-draw.js') }}"></script>
<script src="{{ url('js/mapbox-gl-geocoder.min.js') }}"></script>

{{-- Use to show maps autocomplete in bootstrap modal --}}
<style>
    .modal{
        z-index: 20;
    }
    .modal-backdrop{
        z-index: 10;
    }​
</style>