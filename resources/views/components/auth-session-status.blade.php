@props(['status'])
@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-ok', 'role' => 'status']) }}><i class="fa-solid fa-circle-check"></i> {{ $status }}</div>
@endif
