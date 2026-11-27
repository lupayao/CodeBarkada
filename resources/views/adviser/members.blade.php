@extends('layouts.app')

@section('title', 'Members')

@section('content')
    <div class="mb-4">
        <span class="kicker">Adviser panel</span>
        <h1 class="mt-2">Members</h1>
    </div>

    <div class="card p-4 mb-4">
        <h2 class="h4">Add members</h2>
        <p class="small text-body-secondary">
            One member per line: school email, then optionally first name, grade and section,
            separated by commas or tabs (you can paste straight from a spreadsheet).
        </p>

        <form method="POST" action="{{ route('adviser.members.store') }}" novalidate>
            @csrf

            <div class="mb-3">
                <label class="form-label" for="members">Member list</label>
                <textarea id="members" name="members" rows="6" spellcheck="false" required
                          placeholder="ana{{ $emailDomain }}, Ana, 9, Ruby"
                          @class(['form-control', 'font-monospace', 'is-invalid' => $errors->has('members')])>{{ old('members') }}</textarea>
                @error('members')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="default_password">Default password</label>
                <input id="default_password" name="default_password" type="text" autocomplete="off" spellcheck="false" required
                       aria-describedby="default-password-help"
                       @class(['form-control', 'is-invalid' => $errors->has('default_password')])>
                <div id="default-password-help" class="form-text">At least 8 characters; members must change it when they first sign in.</div>
                @error('default_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary" type="submit">Add members</button>
        </form>
    </div>

    <div class="card p-4">
        <h2 class="h4 mb-3">All members ({{ $members->count() }})</h2>

        @if ($members->isEmpty())
            <p class="text-body-secondary mb-0">No members yet. Add some above.</p>
        @else
            {{-- table-responsive: the table scrolls sideways on small screens --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Member</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Grade</th>
                            <th scope="col">Track</th>
                            <th scope="col">Status</th>
                            <th scope="col"><span class="visually-hidden">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr>
                                <td>
                                    <span class="d-flex align-items-center gap-2">
                                        <x-avatar :user="$member" size="28" />
                                        {{ $member->codename ?? '–' }}
                                    </span>
                                </td>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->email }}</td>
                                <td>{{ $member->grade ?? '–' }}{{ $member->section ? '-'.$member->section : '' }}</td>
                                <td>{{ $tracks[$member->track]['name'] ?? '–' }}</td>
                                <td>
                                    <span class="badge rounded-pill tone-{{ $statuses[$member->id]['tone'] }}">{{ $statuses[$member->id]['label'] }}</span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-outline-light btn-sm" href="{{ route('adviser.members.show', $member) }}"
                                       aria-label="Manage {{ $member->name }}">Manage</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
