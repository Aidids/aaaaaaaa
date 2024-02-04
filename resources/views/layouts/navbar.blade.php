<div class="nav-bar d-print-none">
    <div id="navBarLogo" class="logo d-none ps-2">
        <img src="{{asset('img/logo/logomini.png')}}" alt="" height="35">
    </div>
    <div class="profile ms-auto">
        <div class="profile-info">
            <p class="fw-bold text-truncate">{{ Auth::user()->name }}</p>
            <p class="fw-light text-truncate">{{ Auth::user()->title }}</p>
            <p class="fw-lighter ms-auto">{{ date('l j F Y') }}</p>
        </div>
        <img src="{{ asset('img/icons/profile.png') }}" alt="image">
    </div>
</div>
@if (session('impersonated_by'))
    <div class="text-center alert alert-danger w-100" role="alert">
        <strong>You are currently impersonating {{ Auth::user()->name }}</strong>
        <a class="d-block ms-2" href="{{ route('impersonate-leave')}}">Leave Impersonation</a>
    </div>
@endif

