<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCVhXP3qqWTbQnr-VtTdl0anZZJT3cP9Q0&libraries=places"></script>
@extends('layouts.app')

@section('content')
    <customer-edit-form :customer-id={{ $id }}></customer-edit-form>
@endsection