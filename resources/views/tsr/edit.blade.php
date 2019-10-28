@extends('layouts.app')

@section('content')
    <tsr-edit-form :tsr-id={{ $id }} role="{{ Auth::user()->roles[0]->name }}"></tsr-edit-form>
@endsection