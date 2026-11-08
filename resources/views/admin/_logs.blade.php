@if ($logs->isEmpty())
    <div class="empty"><i class="fa-solid fa-clock-rotate-left"></i><strong>No activity yet</strong></div>
@else
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>When</th><th>User</th><th>Action</th><th>Details</th><th>IP address</th></tr></thead>
            <tbody>
            @foreach ($logs as $log)
                @php $bad = in_array($log->action, ['login_failed', 'login_blocked']); @endphp
                <tr>
                    <td class="text-nowrap">{{ $log->created_at->format('d M, h:i A') }}</td>
                    <td>{{ $log->user->name ?? 'Guest' }}</td>
                    <td><span class="tag {{ $bad ? 'tag-bad' : 'tag-muted' }}">{{ str_replace('_', ' ', $log->action) }}</span></td>
                    <td>{{ $log->description }}</td>
                    <td><code>{{ $log->ip_address }}</code></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
