@props(['title', 'description' => null])
<div class="dash-header">
    <div>
        <h1 class="dash-header__title">{{ $title }}</h1>
        @if ($description)<p class="dash-header__desc">{{ $description }}</p>@endif
    </div>
    @isset($actions)<div class="dash-header__actions">{{ $actions }}</div>@endisset
</div>
