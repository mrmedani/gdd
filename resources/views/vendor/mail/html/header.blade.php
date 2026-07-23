@props(['url'])
@php
    $logoSrc = storage_url(\App\Domains\Settings\Models\Setting::get('app_logo'));
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($logoSrc)
<img src="{{ $logoSrc }}" class="logo" alt="{{ config('app.name') }}">
@elseif (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
