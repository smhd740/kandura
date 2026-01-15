@extends('layouts.admin')

@section('title', __('Edit Design Option'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.design-options.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Design Options') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-palette"></i> {{ __('Edit Design Option') }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <form method="POST" action="{{ route('admin.design-options.update', $designOption) }}" class="card">
                @csrf
                @method('PUT')

                <div class="card-header">
                    <h3 class="card-title">{{ __('Option Information') }}</h3>
                </div>

                <div class="card-body">
                    {{-- Type Selection --}}
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Option Type') }}</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="color" {{ old('type', $designOption->type) == 'color' ? 'selected' : '' }}>
                                {{ __('Color') }}
                            </option>
                            <option value="fabric_type" {{ old('type', $designOption->type) == 'fabric_type' ? 'selected' : '' }}>
                                {{ __('Fabric Type') }}
                            </option>
                            <option value="sleeve_type" {{ old('type', $designOption->type) == 'sleeve_type' ? 'selected' : '' }}>
                                {{ __('Sleeve Type') }}
                            </option>
                            <option value="dome_type" {{ old('type', $designOption->type) == 'dome_type' ? 'selected' : '' }}>
                                {{ __('Dome Type') }}
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    {{-- Name Translations --}}
                    @foreach(config('app.locales', ['en', 'ar']) as $locale)
                    <div class="mb-3">
                        <label class="form-label {{ $locale === 'en' ? 'required' : '' }}">
                            {{ __('Name') }} ({{ strtoupper($locale) }})
                        </label>
                        <input type="text"
                               name="name[{{ $locale }}]"
                               class="form-control @error('name.' . $locale) is-invalid @enderror"
                               value="{{ old('name.' . $locale, $designOption->getTranslation('name', $locale)) }}"
                               {{ $locale === 'en' ? 'required' : '' }}
                               placeholder="{{ __('Enter option name in :lang', ['lang' => strtoupper($locale)]) }}"
                               {{ $locale === 'ar' ? 'dir=rtl' : '' }}>
                        @error('name.' . $locale)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($locale === 'en')
                            <small class="form-hint">
                                {{ __('English name is required and will be used as fallback') }}
                            </small>
                        @endif
                    </div>
                    @endforeach

                    {{-- Info Box --}}
                    <div class="alert alert-warning">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-alert-triangle icon alert-icon"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">{{ __('Note') }}</h4>
                                <div class="text-secondary">
                                    {{ __('Changing this option may affect existing designs that use it.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Current Usage Stats --}}
                    @if(isset($designOption->designs_count) && $designOption->designs_count > 0)
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="ti ti-info-circle fs-1 text-azure"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ __('Usage Statistics') }}</div>
                                    <div class="text-muted">
                                        {{ __('This option is used in :count design(s)', ['count' => $designOption->designs_count]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('admin.design-options.index') }}" class="btn btn-link">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ti ti-check"></i> {{ __('Update Option') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
