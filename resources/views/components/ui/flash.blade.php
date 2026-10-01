{{-- Renders session flash messages. Place once per layout. --}}
@if (session('status') && session('status') !== 'verification-link-sent')
    <x-ui.alert type="success" {{ $attributes }}>{{ session('status') }}</x-ui.alert>
@endif
@if (session('error'))
    <x-ui.alert type="error" {{ $attributes }}>{{ session('error') }}</x-ui.alert>
@endif
