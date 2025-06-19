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
            <h4>School Management</h4>
            <a href="{{ route('admin.schools.create') }}" class="btn btn-primary">Add New School</a>
        </div>
        <div class="card-body table-responsive">
            <table id="schools-table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>School</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Phone No.</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody style="vertical-align: middle"></tbody>
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
            const table = $('#schools-table').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                ajax: '{{ route('admin.schools.dataTable') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'school_admin', name: 'school_admin' },
                    { data: 'address', name: 'address' },
                    { data: 'school_email', name: 'school_email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[5, 'desc']],
                pageLength: 10,
            });

            // Handle delete button click with SweetAlert2
            $('#schools-table').on('click', '.delete-btn', function(e) {
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
                            url: '{{ route('admin.schools.index') }}/' + slug,
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