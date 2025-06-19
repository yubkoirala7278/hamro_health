@extends('admin.master')

@section('content')
    @php
        $schoolAdmin = $school->schoolAdmins->first();
    @endphp

    <div class="card">
        <div class="card-header">
            <h4>School Details: {{ $school->name }}</h4>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $school->name }}</p>
            <p><strong>School Admin:</strong> {{ $schoolAdmin->name ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $school->address }}</p>
            <p><strong>Phone Number:</strong> {{ $school->phone }}</p>
            <p><strong>Created By:</strong> {{ $school->createdBy->name ?? 'N/A' }}</p>
            <p><strong>Created At:</strong> {{ $school->created_at->format('d M Y H:i') }}</p>
            <p><strong>Updated At:</strong> {{ $school->updated_at->format('d M Y H:i') }}</p>
            <a href="{{ route('admin.schools.edit', $schoolAdmin->slug ?? '') }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
@endsection
