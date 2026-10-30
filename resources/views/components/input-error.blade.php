@props(['messages'])
@if ($messages)
    <div {{ $attributes->merge(['class' => 'err', 'role' => 'alert']) }}>
        @foreach ((array) $messages as $message)<div>{{ $message }}</div>@endforeach
    </div>
@endif
