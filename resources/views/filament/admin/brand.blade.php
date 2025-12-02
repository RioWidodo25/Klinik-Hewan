@php
$footerSettings = \App\Models\FooterSetting::getSettings();
$logoUrl = $footerSettings->logo ? asset('storage/' . $footerSettings->logo) : null;
@endphp

<div class="flex items-center gap-3">
    @if($logoUrl)
    <img src="{{ $logoUrl }}" alt="Logo" class="h-10">
    @endif
    <span class="text-xl font-bold text-gray-800 dark:text-white">A2 VET</span>
</div>