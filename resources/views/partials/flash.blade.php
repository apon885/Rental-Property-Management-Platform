@if (session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2" role="status">
        <i class="fa-solid fa-circle-check mt-1"></i><div>{{ session('success') }}</div>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
        <i class="fa-solid fa-circle-exclamation mt-1"></i><div>{{ session('error') }}</div>
    </div>
@endif
