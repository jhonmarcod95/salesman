@extends('layouts.app')

@section('content')
    <expense-submitted-index :expense-entry-id={{json_encode($ids)}} :date-entry= "'{{ $date }}'" :current-week="'{{ $currentWeek }}'"></expense-submitted-index>
@endsection
