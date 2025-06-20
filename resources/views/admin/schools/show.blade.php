@extends('admin.master')

@section('content')
   <div class="card">
    <div class="card-header">
        <h4>School Details: {{ $school->name }}</h4>
    </div>
    <div class="card-body">
        <p><strong>School Admin:</strong> {{ $school->name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $school->email ?? 'N/A' }}</p>
        <p><strong>Address:</strong> {{ $school->school->address }}</p>
        <p><strong>Phone Number:</strong> {{ $school->school->phone }}</p>
        <p><strong>Created At:</strong> {{ $school->created_at->format('d M Y H:i') }}</p>
        <p><strong>Updated At:</strong> {{ $school->updated_at->format('d M Y H:i') }}</p>

        <a href="{{ route('admin.schools.edit', $school->slug ?? '') }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection
