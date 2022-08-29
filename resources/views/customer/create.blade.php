{{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCVhXP3qqWTbQnr-VtTdl0anZZJT3cP9Q0&libraries=places"></script> --}}
@extends('layouts.app')

@section('content')
    <customer-form :company-id={{ Auth::user()->companies->pluck('id')[0] }}></customer-form>
@endsection
