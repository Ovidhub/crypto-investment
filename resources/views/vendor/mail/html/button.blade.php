@php
    $buttonClass = 'button';
    if (isset($color)) {
        $buttonClass .= ' button-' . $color;
    } else {
        $buttonClass .= ' button-primary';
    }
@endphp

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ $url }}" class="{{ $buttonClass }}" target="_blank" rel="noopener">
        {{ $slot }}
    </a>
</div>
