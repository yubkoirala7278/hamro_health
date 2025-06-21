<a href="{{ route('admin.students.medical_reports.show', [$student->slug, $report->id]) }}" class="btn btn-sm btn-info"
    aria-label="View medical report">View</a>
@if (Auth::user()->hasRole('school_admin'))
    <a href="{{ route('admin.students.medical_reports.edit', [$student->slug, $report->id]) }}"
        class="btn btn-sm btn-primary" aria-label="Edit medical report">Edit</a>
    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $report->id }}"
        aria-label="Delete medical report">Delete</button>
@endif
