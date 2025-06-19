@extends('admin.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Student Details: {{ $user->name }}</h4>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Roles:</strong> {{ $user->roles->pluck('name')->implode(', ') }}</p>
            <p><strong>School:</strong> {{ $user->studentProfile->school->name ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $user->studentProfile->phone ?? 'N/A' }}</p>
            <p><strong>Date of Birth:</strong> {{ $user->studentProfile->dob?->format('d M Y') ?? 'N/A' }}</p>
            <p><strong>Gender:</strong> {{ $user->studentProfile->gender ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $user->studentProfile->address ?? 'N/A' }}</p>
            <p><strong>Parent Phone:</strong> {{ $user->studentProfile->parent_phone ?? 'N/A' }}</p>
            <p><strong>Emergency Contact:</strong> {{ $user->studentProfile->emergency_contact ?? 'N/A' }}</p>
            <p><strong>Grade Level:</strong> {{ $user->studentProfile->grade_level ?? 'N/A' }}</p>
            <p><strong>Created At:</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
            <p><strong>Updated At:</strong> {{ $user->updated_at->format('d M Y H:i') }}</p>
            <a href="{{ route('admin.students.edit', $user->slug) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
@endsection