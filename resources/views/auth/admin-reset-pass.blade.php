@extends('layouts.guest')
@section('title', 'Reset your password')
@section('content')
    <section class=" auth">
        <div class="container">
            <div class="pb-3 row justify-content-center">

                <div class="col-12 col-md-6 col-lg-6 col-sm-10 col-xl-6">

                    <div class="bg-dark shadow card login-page roundedd border-1 ">
                        <div class="card-body">
                            <x-error-alert />
                            <x-danger-alert/>
                            <x-success-alert />
                            <div class="mb-2 row flex-between-center">
                                <div class="col-auto">
                                    <h5>Reset your account password?</h5>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('restpass') }}">
                                @csrf
                                <div class="mb-3">
                                    <input class="form-control" name="email" type="email" value="{{ $email }}"
                                        placeholder="Email address" readonly />
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input class="form-control" type="number" name="token" placeholder="Token" />
                                    @error('token')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input class="form-control" name="password" type="password"
                                        placeholder="New Password" />
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input class="form-control" name="password_confirmation" type="password"
                                        placeholder="New Password Confirmation" />
                                    @error('password_confirmation')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3"><button class="mt-3 btn btn-primary d-block w-100" type="submit"
                                        name="submit">Reset Password</button></div>
                            </form>
                        </div>
                    </div>
                    <!---->
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
        <!--end container-->
    </section>
    <!--end section-->

@endsection

@section('scripts')
<script>
    // Auto-refresh CSRF token every 10 minutes to prevent 419 errors
    setInterval(function() {
        fetch('{{ route('forgotpass') }}', {
            method: 'GET',
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newToken = doc.querySelector('input[name="_token"]');
            const currentToken = document.querySelector('input[name="_token"]');
            if (newToken && currentToken) {
                currentToken.value = newToken.value;
                console.log('CSRF token refreshed');
            }
        })
        .catch(error => console.error('Failed to refresh CSRF token:', error));
    }, 600000); // 10 minutes
</script>
@endsection
