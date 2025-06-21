<?php
// File: resources/views/admin/students/medical_reports/index.blade.php
?>

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
            <h4>Medical Reports for {{ $student->name }}</h4>
            <div>
                <a href="{{ route('admin.students.show', $student->slug) }}" class="btn btn-secondary">Back to Student</a>
                <a href="{{ route('admin.students.medical_reports.create', $student->slug) }}" class="btn btn-primary">Add
                    New Medical Report</a>
            </div>
        </div>
        <div class="card-body table-responsive">
            @if (session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif
            <table id="medical-reports-table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Report Date</th>
                        <th>Medical Condition</th>
                        <th>Allergies</th>
                        <th>Medications</th>
                        <th>Vaccinations</th>
                        <th>Doctor Name</th>
                        <th>Specialist</th>
                        <th>Blood Pressure</th>
                        <th>Pulse Rate</th>
                        <th>Temperature</th>
                        <th>Status</th>
                        <th>Created By</th>
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
            const table = $('#medical-reports-table').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                ajax: '{{ route('admin.students.medical_reports.dataTable', $student->slug) }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'report_date',
                        name: 'report_date'
                    },
                    {
                        data: 'medical_condition',
                        name: 'medical_condition'
                    },
                    {
                        data: 'allergies',
                        name: 'allergies'
                    },
                    {
                        data: 'medications',
                        name: 'medications'
                    },
                    {
                        data: 'vaccinations',
                        name: 'vaccinations'
                    },
                    {
                        data: 'doctor_name',
                        name: 'doctor_name'
                    },
                    {
                        data: 'specialist',
                        name: 'specialist'
                    },
                    {
                        data: 'blood_pressure',
                        name: 'blood_pressure'
                    },
                    {
                        data: 'pulse_rate',
                        name: 'pulse_rate'
                    },
                    {
                        data: 'temperature',
                        name: 'temperature'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_by_name',
                        name: 'created_by_name'
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
                    [13, 'desc']
                ],
                pageLength: 10,
            });

            $('#medical-reports-table').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
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
                            url: '{{ route('admin.students.medical_reports.index', $student->slug) }}/' +
                                id,
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
