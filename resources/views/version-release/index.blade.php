@extends('layouts.app')
@section('content')
    <version-release :authenticated="{{ Auth::check() ? 'true' : 'false' }}" :user-roles="{{Auth::check() ? Auth::user()->roles->pluck('name') : '[]' }}"></version-release>
@endsection