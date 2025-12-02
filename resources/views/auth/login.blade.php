<x-guest-layout>
    <h2 class="h2 text-center mb-4">
        <i class="ti ti-login"></i> Login to your account
    </h2>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-check icon alert-icon"></i></div>
                <div>{{ session('status') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label class="form-label required">Email</label>
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="your@email.com"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label required">
                Password
                <span class="form-label-description">
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </span>
            </label>
            <input type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Your password"
                   required
                   autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3">
            <label class="form-check">
                <input type="checkbox" class="form-check-input" name="remember">
                <span class="form-check-label">Remember me on this device</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">
                <i class="ti ti-login"></i> Sign in
            </button>
        </div>
    </form>

    <!-- Register Link -->
    <div class="text-center text-muted mt-3">
        Don't have account yet?
        <a href="{{ route('register') }}" tabindex="-1">Sign up</a>
    </div>
</x-guest-layout>
