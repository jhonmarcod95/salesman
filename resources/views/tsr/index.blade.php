@extends('layouts.app')

@section('content')
    <tsr-index role="{{ Auth::user()->roles[0]->name }}"></tsr-index>
@endsection
