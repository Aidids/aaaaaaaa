<template>
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 pl-md-0">
        <div class="login-form d-flex align-items-center">
            <div class="mx-auto sign-in-sign-up-form w-100">
                <div class="text-center mb-4">
                    <img src="/img/logo/logomini.png" alt class="img-fluid logo">
                </div>
                {{ Form::open(['route' => 'login', 'method' => 'POST']) }}
                <div class="form-row">
                    <div class="form-group col-12">
                        <h6 class="text-center mb-0">Hi!</h6>
                        <label class="text-center d-block">Login to the portal</label>
                    </div>
                </div>
                <div class="mx-auto form-group mb-3">
                    <label for="username" class="mb-2">Username</label>

                    <div>
                        <input id="username" type="text" class="form-control" name="username" required autocomplete="username" autofocus>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="mb-2">Password</label>

                    <div class="">
                        <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary py-2 w-100" style="width: 20rem; letter-spacing: 2px;">
                        Login
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "auth.vue"
}
</script>
