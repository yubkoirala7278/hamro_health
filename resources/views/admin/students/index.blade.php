@extends('admin.master')

@section('header-content')
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        table {
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Student Management</h4>
            @if (Auth::user()->hasRole('school_admin'))
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary">Add New Student</a>
            @endif
        </div>


        <div class="card-body table-responsive">
            <table id="students-table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone No.</th>
                        <th>DOB</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Parent Phone No.</th>
                        <th>Emergency Contact</th>
                        <th>Grade Level</th>
                        <th>School</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const table = $('#students-table').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                ajax: '{{ route('admin.students.dataTable') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'dob',
                        name: 'dob'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'parent_phone',
                        name: 'parent_phone'
                    },
                    {
                        data: 'emergency_contact',
                        name: 'emergency_contact'
                    },
                    {
                        data: 'grade_level',
                        name: 'grade_level'
                    },
                    {
                        data: 'school_name',
                        name: 'school_name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [10, 'desc']
                ],
                pageLength: 10,
            });

            $('#students-table').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                const slug = $(this).data('slug');
                const row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.students.index') }}/' + slug,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    table.row(row).remove().draw();
                                    Toastify({
                                        text: response.message,
                                        duration: 3000,
                                        gravity: 'top',
                                        position: 'right',
                                        backgroundColor: '#28a745'
                                    }).showToast();
                                } else {
                                    Toastify({
                                        text: response.message,
                                        duration: 3000,
                                        gravity: 'top',
                                        position: 'right',
                                        backgroundColor: '#dc3545'
                                    }).showToast();
                                }
                            },
                            error: function(xhr) {
                                const response = xhr.responseJSON || {
                                    message: 'An error occurred.'
                                };
                                Toastify({
                                    text: response.message,
                                    duration: 3000,
                                    gravity: 'top',
                                    position: 'right',
                                    backgroundColor: '#dc3545'
                                }).showToast();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
