{{--
    A coder-bot avatar in the user's color.
    Use it like: <x-avatar :user="$member" size="40" />
--}}
@props(['user', 'size' => 40])

@php($color = $user->avatarColor())

<svg {{ $attributes->merge(['class' => 'avatar']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 64 64"
     role="img" aria-label="{{ $user->displayName() }}'s avatar">
    {{-- background circle in the user's color ("bot-bg" lets the welcome page preview a new color) --}}
    <circle class="bot-bg" cx="32" cy="32" r="32" fill="hsl({{ $color }}, 80%, 58%)"/>
    {{-- antenna --}}
    <line x1="32" y1="10" x2="32" y2="17" stroke="#0b1f5e" stroke-width="3" stroke-linecap="round"/>
    <circle cx="32" cy="9" r="3.5" fill="#0b1f5e"/>
    {{-- head --}}
    <rect x="15" y="17" width="34" height="28" rx="8" fill="#fbf7ea"/>
    {{-- eyes --}}
    <circle cx="25" cy="29" r="4" fill="#0b1f5e"/>
    <circle cx="39" cy="29" r="4" fill="#0b1f5e"/>
    {{-- smile --}}
    <path d="M25 37 q7 5 14 0" fill="none" stroke="#0b1f5e" stroke-width="3" stroke-linecap="round"/>
    {{-- shoulders --}}
    <rect x="20" y="48" width="24" height="10" rx="4" fill="#fbf7ea"/>
</svg>
