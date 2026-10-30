@props(['category', 'size' => 56])
@if ($category->thumb_url)
    <img class="cat-thumb" src="{{ $category->thumb_url }}" alt="{{ $category->localized_name }}" width="{{ $size }}" height="{{ $size }}" loading="lazy" decoding="async">
@else
    <i class="{{ $category->icon }}"></i>
@endif
