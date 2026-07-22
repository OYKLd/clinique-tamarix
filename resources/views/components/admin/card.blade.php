@props(['title' => null, 'icon' => null, 'action' => null])

<div {{ $attributes->merge(['class' => 'card border-0 shadow-sm']) }}>
    @if ($title)
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="h6 mb-0">
                @if ($icon)<i class="bi {{ $icon }} me-2 text-secondary"></i>@endif
                {{ $title }}
            </h2>
            @if ($action)
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
