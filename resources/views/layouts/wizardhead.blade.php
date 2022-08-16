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

<!-- CSS Files -->
<link href="{{ url('css/gsdk-bootstrap-wizard.css') }}" rel="stylesheet" />

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
<!-- Fonts and Icons -->
<link href="https://netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
<link href="assets/css/themify-icons.css" rel="stylesheet">

<!-- Mapbox GL CSS -->
<link href="{{ url('css/mapbox-gl.css') }}" rel="stylesheet"/>

<!-- Mapbox GL JS -->
<script src="{{ url('js/mapbox-gl.js') }}"></script>

<!-- Mapbox GL DRAW CSS -->
<link rel='stylesheet' href="{{ url('css/mapbox-gl-draw.css') }}" type='text/css'/>


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
</style>

<script src="{{ url('js/turf.min.js') }}"></script>
<script src="{{ url('js/mapbox-gl-draw.js') }}"></script>

{{-- Map Autocomplete --}}
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyBVLmW1RxKWUE37XG5j0zPqMqAF1RzIWnE"></script>
<script>
    // function initMap(){
    //     let mapAutocomplete = new google.maps.places.Autocomplete((document.getElementById('add-address')),
    //         {
    //             componentRestrictions: {country: 'ph'}
    //         });
    //         google.maps.event.addListener(mapAutocomplete, 'place_changed', function() {
    //     });
    //     mapAutocomplete = new google.maps.places.Autocomplete((document.getElementById('address')),
    //         {
    //             componentRestrictions: {country: 'ph'}
    //         });
    //     google.maps.event.addListener(mapAutocomplete, 'place_changed', function() {
    //     });
    // }
</script>
{{-- Use to show maps autocomplete in bootstrap modal --}}
<style>
    .modal{
        z-index: 20;
    }
    .modal-backdrop{
        z-index: 10;
    }​
</style>