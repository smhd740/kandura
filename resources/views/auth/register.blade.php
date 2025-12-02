<x-guest-layout>
    <h2 class="h2 text-center mb-4">
        <i class="ti ti-user-plus"></i> Create new account
    </h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label class="form-label required">Full Name</label>
            <input type="text"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="Enter your full name"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label required">Email Address</label>
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="your@email.com"
                   value="{{ old('email') }}"
                   required
                   autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

       <!-- Phone Number -->
<div class="mb-3">
    <label class="form-label required">Phone Number</label>
    <input type="tel"
           name="phone"
           class="form-control @error('phone') is-invalid @enderror"
           placeholder="+963 XXX XXX XXX"
           value="{{ old('phone') }}"
           required
           autocomplete="tel">
    @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Enter with country code (e.g., +963 XXX XXX XXX)</small>
</div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label required">Password</label>
            <input type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Minimum 8 characters"
                   required
                   autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label class="form-label required">Confirm Password</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control"
                   placeholder="Confirm your password"
                   required
                   autocomplete="new-password">
        </div>

        <!-- Terms -->
        <div class="mb-3">
            <label class="form-check">
                <input type="checkbox" class="form-check-input" required>
                <span class="form-check-label">
                    I agree to the <a href="#" tabindex="-1">terms and policy</a>.
                </span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">
                <i class="ti ti-user-plus"></i> Create new account
            </button>
        </div>
    </form>

    <!-- Login Link -->
    <div class="text-center text-muted mt-3">
        Already have account?
        <a href="{{ route('login') }}" tabindex="-1">Sign in</a>
    </div>
</x-guest-layout>
