@extends('layouts.app')

@section('content')
    <map-analytics-report-map-customers :user-role="{{ Auth::user()->roles->pluck('id') }}"></map-analytics-report-map-customers>
@endsection
