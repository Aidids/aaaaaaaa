@extends('layouts.dashboard')
@section('content')
    <expense-show :form_id="{{$travel->id}}"></expense-show>
@endsection
