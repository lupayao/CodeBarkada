@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <span class="kicker">Adviser panel</span>
            <h1 class="mt-2 mb-0">Hello, {{ $adviser->displayName() }}</h1>
        </div>
        {{-- Quick actions --}}
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary" href="{{ route('adviser.members.index') }}">Add members</a>
            <a class="btn btn-outline-light" href="{{ route('adviser.members.index') }}">All members</a>
        </div>
    </div>

    @if ($stats['total'] === 0)
        {{-- Empty club: no charts, just the first step --}}
        <div class="card p-5 text-center">
            <h2 class="h4">No members yet</h2>
            <p class="text-body-secondary">Add your club members to see the dashboard fill up.</p>
            <div><a class="btn btn-primary" href="{{ route('adviser.members.index') }}">Add members</a></div>
        </div>
    @else
        {{-- 1. Stat tiles --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card stat-tile tone-sky p-3 h-100">
                    <span class="stat-label">Total members</span>
                    <span class="stat-value">{{ $stats['total'] }}</span>
                </div>
            </div>
            @foreach ($stats['statuses'] as $status)
                <div class="col-6 col-lg-3">
                    <div class="card stat-tile tone-{{ $status['tone'] }} p-3 h-100">
                        <span class="stat-label">{{ $status['label'] }}</span>
                        <span class="stat-value">{{ $status['count'] }}</span>
                        <span class="small text-body-secondary">{{ $status['percent'] }}% of members</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4 mb-4">
            {{-- 2. Sign-up progress: one bar split by status --}}
            <div class="col-lg-6">
                <div class="card p-4 h-100">
                    <h2 class="h5 mb-1">Sign-up progress</h2>
                    <p class="small text-body-secondary">Where each member is in getting started.</p>

                    <div class="status-bar my-3" role="img"
                         aria-label="@foreach ($stats['statuses'] as $s){{ $s['label'] }} {{ $s['count'] }}@if (! $loop->last), @endif @endforeach">
                        @foreach ($stats['statuses'] as $status)
                            @if ($status['count'] > 0)
                                <div class="status-bar-part tone-{{ $status['tone'] }}" style="flex-grow: {{ $status['count'] }}"
                                     title="{{ $status['label'] }}: {{ $status['count'] }} ({{ $status['percent'] }}%)"></div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Legend: every color also has its name and numbers --}}
                    <ul class="list-unstyled mb-0 small">
                        @foreach ($stats['statuses'] as $status)
                            <li class="d-flex align-items-center gap-2 py-1">
                                <span class="legend-dot tone-{{ $status['tone'] }}" aria-hidden="true"></span>
                                <span class="flex-grow-1">{{ $status['label'] }}</span>
                                <span class="fw-bold">{{ $status['count'] }}</span>
                                <span class="text-body-secondary legend-pct">{{ $status['percent'] }}%</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- 3. Tracks --}}
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100">
                    <h2 class="h5 mb-1">Tracks</h2>
                    <p class="small text-body-secondary">{{ $stats['tracksTotal'] }} members have picked one.</p>
                    <div class="d-flex flex-column gap-3">
                        @foreach ($stats['tracks'] as $track)
                            <x-stat-bar :label="$track['label']" :count="$track['count']" :percent="$track['percent']" />
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 4. Grades --}}
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100">
                    <h2 class="h5 mb-3">By grade</h2>
                    <div class="d-flex flex-column gap-3">
                        @foreach ($stats['grades'] as $grade)
                            <x-stat-bar :label="$grade['label']" :count="$grade['count']" :percent="$grade['percent']" />
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- 5. Needs attention --}}
            <div class="col-lg-7">
                <div class="card p-4 h-100">
                    <h2 class="h5 mb-3">Needs attention ({{ $stats['needsAttentionCount'] }})</h2>

                    @if ($stats['needsAttention']->isEmpty())
                        <p class="text-body-secondary mb-0">Everyone has finished signing up.</p>
                    @else
                        <ul class="list-unstyled mb-0">
                            @foreach ($stats['needsAttention'] as $row)
                                <li class="d-flex align-items-center gap-3 py-2 border-bottom">
                                    <x-avatar :user="$row['member']" size="32" />
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="text-truncate">{{ $row['member']->name }}</div>
                                        <div class="small text-body-secondary text-truncate">{{ $row['member']->email }}</div>
                                    </div>
                                    <span class="badge rounded-pill tone-{{ $row['status']['tone'] }} d-none d-sm-inline">{{ $row['status']['label'] }}</span>
                                    <a class="btn btn-outline-light btn-sm" href="{{ route('adviser.members.show', $row['member']) }}"
                                       aria-label="Manage {{ $row['member']->name }}">Manage</a>
                                </li>
                            @endforeach
                        </ul>
                        @if ($stats['needsAttentionCount'] > $stats['needsAttention']->count())
                            <a class="d-inline-block mt-3 small" href="{{ route('adviser.members.index') }}">See all members</a>
                        @endif
                    @endif
                </div>
            </div>

            {{-- 6. Recently added --}}
            <div class="col-lg-5">
                <div class="card p-4 h-100">
                    <h2 class="h5 mb-3">Recently added</h2>
                    <ul class="list-unstyled mb-0">
                        @foreach ($stats['recent'] as $member)
                            <li class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <x-avatar :user="$member" size="32" />
                                <span class="flex-grow-1 text-truncate">{{ $member->name }}</span>
                                <span class="small text-body-secondary">{{ $member->created_at->format('M j') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
@endsection
