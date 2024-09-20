@extends('authority-to-deduct.layout')

@section('content')
    <authority-to-deduct-index 
        name="{{ $name }}" 
        email="{{ $email }}" 
        :atd_accepted="{{ $atd_accepted }}"
        atd_accepted_date="{{ $atd_accepted_date }}"
    ></authority-to-deduct-index>
@endsection
