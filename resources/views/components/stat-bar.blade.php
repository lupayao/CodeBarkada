{{--
    One labelled horizontal bar, e.g. "Python  12  ████████░░░░".
    The numbers come from CodeBarkadaService::dashboard(); this only draws them.
    Use it like: <x-stat-bar label="Python" :count="12" :percent="60" />
--}}
@props(['label', 'count', 'percent'])

<div class="stat-bar">
    <div class="d-flex justify-content-between small mb-1">
        <span>{{ $label }}</span>
        <span class="fw-bold">{{ $count }}</span>
    </div>
    <div class="stat-bar-track" role="img" aria-label="{{ $label }}: {{ $count }} ({{ $percent }}%)"
         title="{{ $label }}: {{ $count }} ({{ $percent }}%)">
        {{-- inline width, the same way Bootstrap progress bars do it --}}
        <div class="stat-bar-fill" style="width: {{ $percent }}%"></div>
    </div>
</div>
