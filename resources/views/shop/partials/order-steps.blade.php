@php
    $flow = ['pending', 'processing', 'shipped', 'delivered'];
    $icons = ['pending' => 'fa-receipt', 'processing' => 'fa-print', 'shipped' => 'fa-plane', 'delivered' => 'fa-box-open'];
    $idx = $order->status === 'cancelled' ? -1 : array_search($order->status, $flow, true);
@endphp
@if ($order->status === 'cancelled')
    <div class="alert alert-err"><i class="fa-solid fa-ban"></i> {{ t('order.status.cancelled') }}</div>
@else
    <div class="steps" role="list">
        @foreach ($flow as $i => $s)
            <div class="step {{ $i <= $idx ? 'done' : '' }} {{ $i === $idx ? 'now' : '' }}" role="listitem"><i class="fa-solid {{ $icons[$s] }}"></i><span>{{ t('order.status.'.$s) }}</span></div>
        @endforeach
    </div>
    <div class="progress" aria-hidden="true"><span style="width:{{ max(8, ($idx + 1) / 4 * 100) }}%"></span></div>
@endif
