@props(['status'])
@php
    $map = [
        'pending' => 'warn', 'approved' => 'ok', 'active' => 'ok', 'paid' => 'ok', 'verified' => 'ok',
        'available' => 'ok', 'completed' => 'info', 'rented' => 'info',
        'rejected' => 'bad', 'suspended' => 'bad', 'failed' => 'bad',
        'cancelled' => 'muted', 'unverified' => 'muted',
    ];
    $tone = $map[$status] ?? 'muted';
@endphp
<span class="tag tag-{{ $tone }}">{{ ucfirst($status) }}</span>
