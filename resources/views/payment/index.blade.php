@extends('layouts.app')

@section('content')
    <payment-index :user-id={{ Auth::user()->id }}></payment-index>
@endsection