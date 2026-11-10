{{--
    Field form: label eksplisit, petunjuk, dan pesan error di dekat input.
    Pemakaian: <x-field name="email" label="Email" type="email" required hint="..." />
    Error server (Laravel) dibaca otomatis dari $errors; error frontend diisi oleh assets/js/pitlabs.js
    pada form yang memakai atribut data-pit-validate. type="password" otomatis dapat tombol tampil/sembunyi.
--}}
@props([
    'name',
    'label',
    'type'     => 'text',
    'hint'     => null,
    'error'    => null,
    'required' => false,
    'value'    => null,
    'id'       => null,
])

@php
    $id    = $id ?? 'field-'.str_replace(['[', ']', '.'], '-', $name);
    $error = $error ?? ($errors->first($name) ?: null);
    $describedBy = trim(($hint ? $id.'-hint ' : '').($error ? $id.'-error' : ''));
    $isPassword = $type === 'password';
@endphp

<div class="mb-3">
    <label for="{{ $id }}" class="form-label">
        {{ $label }}@if ($required)<span class="pit-required" aria-hidden="true"> *</span>@endif
    </label>

    @if ($isPassword)<div class="pit-pw">@endif
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if (! $isPassword) value="{{ old($name, $value) }}" @endif
        @required($required)
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($error) aria-invalid="true" @endif
        {{ $attributes->class(['form-control', 'is-invalid' => (bool) $error]) }}
    >
    @if ($isPassword)
        <button type="button" class="pit-pw__toggle" data-pit-toggle-password="#{{ $id }}" aria-label="Tampilkan kata sandi" aria-pressed="false">
            <i class="bi bi-eye" aria-hidden="true"></i>
        </button>
    </div>
    @endif

    @if ($hint)
        <div id="{{ $id }}-hint" class="form-text">{{ $hint }}</div>
    @endif

    <div id="{{ $id }}-error" class="invalid-feedback">{{ $error }}</div>
</div>
