@extends('layouts.app')

@section('content')

    <div class="d-flex login-container" style="background-image: url({{url('img/desb.png')}});">
        <div class="col-10 col-md-6 col-xl-3 mx-auto card my-auto">
            <div class="mx-auto mb-4">
                <img src="/img/logo/logomini.png" alt class="img-fluid logo">
            </div>
            {{ Form::open(['route' => 'login', 'method' => 'POST']) }}
            <div class="form-group col-12">
                <h6 class="text-center mb-0">Welcome back</h6>
                <label class="text-center d-block">Login to the portal</label>
            </div>
            @if ($error = $errors->first('username'))
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endif
            <div class="mb-3">
                <label for="username" class="mb-2">Username</label>
                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username"
                       value="{{ old('username') }}"
                       required
                       autocomplete="username"
                       autofocus>
            </div>

            <div class="form-group mb-4">
                <label for="password" class="mb-2">Password</label>
                <input id="password" type="password" class="form-control @error('username') is-invalid @enderror" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-success w-100" style="letter-spacing: 2px;">Login</button>
            {{ Form::close() }}
        </div>
    </div>

@endsection
