{{-- @extends('layouts.admin')

@section('title', __('Create New Permission'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.permissions.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Permissions') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-key-plus"></i> {{ __('Create New Permission') }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <form method="POST" action="{{ route('admin.permissions.store') }}" class="card">
                @csrf

                <div class="card-header">
                    <h3 class="card-title">{{ __('Permission Information') }}</h3>
                </div>

                <div class="card-body">
                    <!-- Permission Name -->
                    <div class="mb-4">
                        <label class="form-label required">{{ __('Permission Name') }}</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-key"></i>
                            </span>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="{{ __('e.g., view users, create orders') }}"
                                   value="{{ old('name') }}"
                                   required
                                   autofocus>
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle"></i>
                            {{ __('Use format: action + module (e.g., "view users", "edit orders")') }}
                        </small>
                    </div>

                    <hr class="my-4">

                    <!-- Common Examples -->
                    <div class="mb-3">
                        <label class="form-label">{{ __('Common Actions') }}</label>
                        <p class="text-muted small mb-2">{{ __('Click to use as a template') }}</p>
                        <div class="btn-group mb-2" role="group">
                            @foreach($actions as $action)
                                <button type="button"
                                        class="btn btn-outline-primary btn-sm action-template"
                                        data-action="{{ $action }}">
                                    {{ $action }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if($modules->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">{{ __('Existing Modules') }}</label>
                            <p class="text-muted small mb-2">{{ __('Click to use as a template') }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($modules as $module)
                                    <button type="button"
                                            class="btn btn-outline-secondary btn-sm module-template"
                                            data-module="{{ $module }}">
                                        {{ $module }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Examples Card -->
                    <div class="alert alert-info mb-0">
                        <h4 class="alert-title">
                            <i class="ti ti-bulb"></i> {{ __('Examples') }}
                        </h4>
                        <ul class="mb-0">
                            <li><code>view users</code> - {{ __('Permission to view users list') }}</li>
                            <li><code>create orders</code> - {{ __('Permission to create new orders') }}</li>
                            <li><code>edit designs</code> - {{ __('Permission to edit designs') }}</li>
                            <li><code>delete coupons</code> - {{ __('Permission to delete coupons') }}</li>
                            <li><code>approve reviews</code> - {{ __('Permission to approve reviews') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-link">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ti ti-device-floppy"></i> {{ __('Create Permission') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.querySelector('input[name="name"]');
        let selectedAction = '';
        let selectedModule = '';

        // Action template buttons
        document.querySelectorAll('.action-template').forEach(function(btn) {
            btn.addEventListener('click', function() {
                selectedAction = this.dataset.action;
                updatePermissionName();

                // Highlight selected
                document.querySelectorAll('.action-template').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Module template buttons
        document.querySelectorAll('.module-template').forEach(function(btn) {
            btn.addEventListener('click', function() {
                selectedModule = this.dataset.module;
                updatePermissionName();

                // Highlight selected
                document.querySelectorAll('.module-template').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        function updatePermissionName() {
            if (selectedAction && selectedModule) {
                nameInput.value = selectedAction + ' ' + selectedModule;
            }
        }
    });
</script>
@endpush --}}
