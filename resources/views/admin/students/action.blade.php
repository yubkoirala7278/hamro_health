<a href="{{ route('admin.students.show', $user->slug) }}" class="btn btn-sm btn-info">View</a>
<a href="{{ route('admin.students.edit', $user->slug) }}" class="btn btn-sm btn-primary">Edit</a>
<button type="button" class="btn btn-sm btn-danger delete-btn" data-slug="{{ $user->slug }}">Delete</button>