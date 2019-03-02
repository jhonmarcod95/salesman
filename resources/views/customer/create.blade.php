@extends('layouts.app')

@section('content')
    <customer-form :company-id={{ Auth::user()->companies->pluck('id')[0] }}></customer-form>
@endsection
