<a href="{{ route('admin.students.show', $student->slug) }}" class="btn btn-sm btn-info" aria-label="View student details">View</a>
<a href="{{ route('admin.students.edit', $student->slug) }}" class="btn btn-sm btn-primary" aria-label="Edit student">Edit</a>
<button type="button" class="btn btn-sm btn-danger delete-btn" data-slug="{{ $student->slug }}" aria-label="Delete student">Delete</button>