@extends('layouts.dashboard')

@section('content')
    <calendar-index :dept-id="{{$dept}}"/>
@endsection
