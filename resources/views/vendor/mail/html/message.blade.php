@php
    // Default level to 'info' if not provided
    $level = $level ?? 'default';
    
    if ($level === 'success') {
        $bgColor = '#ECFDF5';
        $borderColor = '#10B981';
        $textColor = '#065F46';
    } elseif ($level === 'error') {
        $bgColor = '#FEF2F2';
        $borderColor = '#EF4444';
        $textColor = '#991B1B';
    } else {
        $bgColor = '#EFF6FF';
        $borderColor = '#3B82F6';
        $textColor = '#1E40AF';
    }
@endphp

@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ $header ?? config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Alert Box (if level is provided) --}}
@if(isset($message) && $level != 'default')
<div class="info-box">
    <p>{{ $message }}</p>
</div>
@endif

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
