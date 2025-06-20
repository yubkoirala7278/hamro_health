@extends('admin.master')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Student Details</h4>
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $student->name }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $student->email }}</dd>

                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">{{ $student->student->phone ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Date of Birth</dt>
                <dd class="col-sm-9">{{ $student->student->dob ? $student->student->dob->format('d M Y') : 'N/A' }}</dd>

                <dt class="col-sm-3">Gender</dt>
                <dd class="col-sm-9">{{ $student->student->gender ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Address</dt>
                <dd class="col-sm-9">{{ $student->student->address ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Parent Phone</dt>
                <dd class="col-sm-9">{{ $student->student->parent_phone ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Emergency Contact</dt>
                <dd class="col-sm-9">{{ $student->student->emergency_contact ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Grade Level</dt>
                <dd class="col-sm-9">{{ $student->student->grade_level ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $student->created_at->format('d M Y') }}</dd>
            </dl>
        </div>
    </div>
@endsection