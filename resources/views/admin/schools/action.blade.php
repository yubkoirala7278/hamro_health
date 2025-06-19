@php
    $schoolAdmin = $school->schoolAdmins->first();
@endphp

<a href="{{ route('admin.schools.show', $schoolAdmin?->slug) }}" class="btn btn-sm btn-info">View</a>
<a href="{{ route('admin.schools.edit', $schoolAdmin?->slug) }}" class="btn btn-sm btn-primary">Edit</a>
<button type="button" class="btn btn-sm btn-danger delete-btn" data-slug="{{ $schoolAdmin?->slug }}">
    Delete
</button>
