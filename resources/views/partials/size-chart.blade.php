@php
    // [taille, poitrine cm, longueur cm, poitrine in, longueur in] — unisexe
    $rows = [
        ['XS', 86, 66, 34, 26], ['S', 92, 69, 36, 27], ['M', 99, 72, 39, 28.5], ['L', 106, 75, 42, 29.5],
        ['XL', 114, 78, 45, 30.5], ['2XL', 122, 81, 48, 32], ['3XL', 130, 84, 51, 33],
    ];
@endphp
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>{{ t('size_guide.size') }}</th><th>{{ t('size_guide.chest') }} (cm)</th><th>{{ t('size_guide.length') }} (cm)</th><th>{{ t('size_guide.chest') }} (in)</th><th>{{ t('size_guide.length') }} (in)</th></tr></thead>
        <tbody>@foreach ($rows as $r)<tr><td><b>{{ $r[0] }}</b></td><td>{{ $r[1] }}</td><td>{{ $r[2] }}</td><td>{{ $r[3] }}</td><td>{{ $r[4] }}</td></tr>@endforeach</tbody>
    </table>
</div>
<p class="muted" style="font-size:12.5px;margin-top:10px"><i class="fa-solid fa-ruler"></i> {{ t('size_guide.tip') }}</p>
