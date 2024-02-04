@extends('layouts.dashboard')
@section('content')
    <h4 class="mb-0">Travel Authorization Details</h4>
    <travel-show :form_id="{{$travel->id}}"></travel-show>
@endsection
