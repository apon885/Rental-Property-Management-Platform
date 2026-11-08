<div class="stat-strip mb-4">
    @foreach ($stats as $s)
        <div class="stat">
            <div class="stat-label">{{ $s['label'] }}</div>
            <div class="stat-value">{{ $s['value'] }}</div>
            @if (! empty($s['hint']))<div class="stat-hint">{{ $s['hint'] }}</div>@endif
        </div>
    @endforeach
</div>
