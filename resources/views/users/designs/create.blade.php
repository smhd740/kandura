@extends('layouts.admin')

@section('title', __('Create Design'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('user.designs.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to My Designs') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-palette"></i> {{ __('Create New Design') }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('user.designs.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
            {{-- Main Form --}}
            <div class="col-lg-8">
                {{-- Basic Information --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Basic Information') }}</h3>
                    </div>
                    <div class="card-body">
                        {{-- Name Translations --}}
                        @foreach(config('app.locales', ['en', 'ar']) as $locale)
                        <div class="mb-3">
                            <label class="form-label {{ $locale === 'en' ? 'required' : '' }}">
                                {{ __('Design Name') }} ({{ strtoupper($locale) }})
                            </label>
                            <input type="text"
                                   name="name[{{ $locale }}]"
                                   class="form-control @error('name.' . $locale) is-invalid @enderror"
                                   value="{{ old('name.' . $locale) }}"
                                   {{ $locale === 'en' ? 'required' : '' }}
                                   placeholder="{{ __('Enter design name in :lang', ['lang' => strtoupper($locale)]) }}"
                                   {{ $locale === 'ar' ? 'dir=rtl' : '' }}>
                            @error('name.' . $locale)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endforeach

                        <hr>

                        {{-- Description Translations --}}
                        @foreach(config('app.locales', ['en', 'ar']) as $locale)
                        <div class="mb-3">
                            <label class="form-label {{ $locale === 'en' ? 'required' : '' }}">
                                {{ __('Description') }} ({{ strtoupper($locale) }})
                            </label>
                            <textarea name="description[{{ $locale }}]"
                                      rows="5"
                                      class="form-control @error('description.' . $locale) is-invalid @enderror"
                                      {{ $locale === 'en' ? 'required' : '' }}
                                      placeholder="{{ __('Describe your design in :lang', ['lang' => strtoupper($locale)]) }}"
                                      {{ $locale === 'ar' ? 'dir=rtl' : '' }}>{{ old('description.' . $locale) }}</textarea>
                            @error('description.' . $locale)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endforeach

                        {{-- Price --}}
                        <div class="mb-3">
                            <label class="form-label required">{{ __('Price') }} ({{ __('SYP') }})</label>
                            <input type="number"
                                   name="price"
                                   class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price') }}"
                                   step="0.01"
                                   min="0"
                                   required
                                   placeholder="0.00">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Images --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Design Images') }}</h3>
                        <div class="card-actions">
                            <span class="badge bg-primary">{{ __('Required: At least 1 image') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">{{ __('Upload Images') }}</label>
                            <input type="file"
                                   name="images[]"
                                   class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                                   multiple
                                   accept="image/*"
                                   required
                                   id="imageInput">
                            <small class="form-hint">
                                {{ __('You can select multiple images. Supported formats: JPG, PNG, GIF. Max size: 2MB per image.') }}
                            </small>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Image Preview --}}
                        <div id="imagePreview" class="row g-2 mt-3"></div>
                    </div>
                </div>

                {{-- Design Options (Optional) --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Design Options') }}</h3>
                        <div class="card-actions">
                            <span class="badge bg-secondary-lt">{{ __('Optional') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            {{ __('Select design options that best describe your kandura. You can select multiple options of the same type.') }}
                        </p>

                        @foreach(['color' => 'Colors', 'fabric_type' => 'Fabric Types', 'sleeve_type' => 'Sleeve Types', 'dome_type' => 'Dome Types'] as $type => $label)
                            @php
                                $options = $designOptions->where('type', $type);
                            @endphp
                            @if($options->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label">
                                    @switch($type)
                                        @case('color')
                                            <i class="ti ti-palette text-azure"></i>
                                            @break
                                        @case('fabric_type')
                                            <i class="ti ti-shirt text-green"></i>
                                            @break
                                        @case('sleeve_type')
                                            <i class="ti ti-hand-finger text-purple"></i>
                                            @break
                                        @case('dome_type')
                                            <i class="ti ti-circle text-orange"></i>
                                            @break
                                    @endswitch
                                    {{ __($label) }}
                                </label>
                                <div class="form-selectgroup">
                                    @foreach($options as $option)
                                    <label class="form-selectgroup-item">
                                        <input type="checkbox"
                                               name="design_options[]"
                                               value="{{ $option->id }}"
                                               class="form-selectgroup-input"
                                               {{ in_array($option->id, old('design_options', [])) ? 'checked' : '' }}>
                                        <span class="form-selectgroup-label">
                                            {{ $option->getTranslation('name', app()->getLocale()) }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach

                        @if($designOptions->isEmpty())
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-info-circle icon alert-icon"></i>
                                </div>
                                <div>
                                    {{ __('No design options available yet. Contact admin to add options.') }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Sizes --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Available Sizes') }}</h3>
                        <div class="card-actions">
                            <span class="badge bg-primary">{{ __('Required: At least 1') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">{{ __('Select all sizes available for this design') }}</p>

                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                            @foreach($measurements as $measurement)
                            <label class="form-selectgroup-item flex-fill">
                                <input type="checkbox"
                                       name="measurements[]"
                                       value="{{ $measurement->id }}"
                                       class="form-selectgroup-input"
                                       {{ in_array($measurement->id, old('measurements', [])) ? 'checked' : '' }}>
                                <div class="form-selectgroup-label d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="form-selectgroup-title fw-bold">{{ $measurement->size }}</span>
                                    </div>
                                    <div class="text-muted">
                                        <i class="ti ti-check"></i>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        @error('measurements')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Help Card --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Help & Tips') }}</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="ti ti-photo text-azure fs-2"></i>
                                </div>
                                <div class="col">
                                    <small class="text-muted">
                                        {{ __('Use high-quality images that clearly show your design') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="ti ti-writing text-green fs-2"></i>
                                </div>
                                <div class="col">
                                    <small class="text-muted">
                                        {{ __('Write detailed descriptions to help customers understand your design') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="ti ti-currency-dollar text-purple fs-2"></i>
                                </div>
                                <div class="col">
                                    <small class="text-muted">
                                        {{ __('Set competitive prices based on materials and complexity') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <a href="{{ route('user.designs.index') }}" class="btn btn-link">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary ms-auto">
                                <i class="ti ti-check"></i> {{ __('Create Design') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Image Preview
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';

        const files = Array.from(e.target.files);

        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-4';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.height = '150px';
                    img.style.width = '100%';
                    img.style.objectFit = 'cover';

                    col.appendChild(img);
                    preview.appendChild(col);
                };

                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush
