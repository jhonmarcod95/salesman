@extends('authority-to-deduct.layout')

@section('content')
    <authority-to-deduct-index name="{{ $name }}" email="{{ $email }}"></authority-to-deduct-index>
@endsection
