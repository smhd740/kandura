@extends('layouts.admin')

@section('title', __('Create Design Option'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.design-options.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Design Options') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-palette"></i> {{ __('Create Design Option') }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <form method="POST" action="{{ route('admin.design-options.store') }}" enctype="multipart/form-data" class="card">
                @csrf

                <div class="card-header">
                    <h3 class="card-title">{{ __('Option Information') }}</h3>
                </div>

                <div class="card-body">
                    {{-- Type Selection --}}
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Option Type') }}</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>
                                {{ __('Color') }}
                            </option>
                            <option value="fabric_type" {{ old('type') == 'fabric_type' ? 'selected' : '' }}>
                                {{ __('Fabric Type') }}
                            </option>
                            <option value="sleeve_type" {{ old('type') == 'sleeve_type' ? 'selected' : '' }}>
                                {{ __('Sleeve Type') }}
                            </option>
                            <option value="dome_type" {{ old('type') == 'dome_type' ? 'selected' : '' }}>
                                {{ __('Dome Type') }}
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    {{-- Image Upload --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('Option Image') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                        <input type="file"
                               name="image"
                               class="form-control @error('image') is-invalid @enderror"
                               accept="image/*"
                               id="imageInput">
                        <small class="form-hint">
                            {{ __('Upload an image representing this option. Max size: 2MB.') }}
                        </small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Image Preview --}}
                        <div id="imagePreview" class="mt-3"></div>
                    </div>

                    {{-- Status
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="form-check-label">{{ __('Active') }}</span>
                        </label>
                        <small class="form-hint text-muted">
                            {{ __('Inactive options will not be visible to users') }}
                        </small>
                    </div>

                    <hr> --}}

                    {{-- Name Translations --}}
                    @foreach(config('app.locales', ['en', 'ar']) as $locale)
                    <div class="mb-3">
                        <label class="form-label {{ $locale === 'en' ? 'required' : '' }}">
                            {{ __('Name') }} ({{ strtoupper($locale) }})
                        </label>
                        <input type="text"
                               name="name[{{ $locale }}]"
                               class="form-control @error('name.' . $locale) is-invalid @enderror"
                               value="{{ old('name.' . $locale) }}"
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
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-info-circle icon alert-icon"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">{{ __('Tip') }}</h4>
                                <div class="text-secondary">
                                    {{ __('Users will select from these options when creating their kandura designs.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Examples --}}
                    <div class="card bg-light">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Examples by Type') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong><i class="ti ti-palette text-azure"></i> {{ __('Color') }}:</strong>
                                    <p class="text-muted mb-0">{{ __('White, Black, Navy Blue, Beige...') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="ti ti-shirt text-green"></i> {{ __('Fabric Type') }}:</strong>
                                    <p class="text-muted mb-0">{{ __('Cotton, Silk, Linen, Polyester...') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="ti ti-hand-finger text-purple"></i> {{ __('Sleeve Type') }}:</strong>
                                    <p class="text-muted mb-0">{{ __('Full Sleeve, Half Sleeve, Short...') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="ti ti-circle text-orange"></i> {{ __('Dome Type') }}:</strong>
                                    <p class="text-muted mb-0">{{ __('Round, Square, Omani Style...') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('admin.design-options.index') }}" class="btn btn-link">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ti ti-check"></i> {{ __('Create Option') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Image Preview
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';

        const file = e.target.files[0];

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail';
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';

                preview.appendChild(img);
            };

            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
