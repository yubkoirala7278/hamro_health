@extends('admin.master')

@section('header-content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <style>
        .dashboard-card {
            border: none;
            border-radius: 1rem;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .dashboard-icon {
            font-size: 2.5rem;
            margin-right: 1rem;
        }

        .dashboard-title {
            font-size: 1rem;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .dashboard-value {
            font-size: 1.75rem;
            font-weight: bold;
        }

        .chart-container {
            /* min-height: 300px; */
            max-height: 300px;
            /* or any height you prefer */
        }
    </style>
@endsection

@section('content')
    <!-- Flash Messages -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-4">
        <h1 class="display-6 fw-bold text-primary">Admin Dashboard</h1>
        <p class="text-muted">Overview of schools, students, and medical reports</p>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-building dashboard-icon text-primary"></i>
                        <div>
                            <div class="dashboard-title">Total Schools</div>
                            <div class="dashboard-value">{{ $totalSchools }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-person-fill dashboard-icon text-success"></i>
                        <div>
                            <div class="dashboard-title">Total Students</div>
                            <div class="dashboard-value">{{ $totalStudents }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-file-medical dashboard-icon text-danger"></i>
                        <div>
                            <div class="dashboard-title">Total Medical Reports</div>
                            <div class="dashboard-value">{{ $totalMedicalReports }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4">
        <div class="col-lg-6 d-flex">
            <div class="card shadow-sm w-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">Medical Reports by Status</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-flex">
            <div class="card shadow-sm w-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">Students by Grade Level</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container d-flex justify-content-center align-items-center"
                        style="min-height: 300px;">
                        <canvas id="gradeLevelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script>
        // Pie Chart for Students by Grade Level
        const gradeLevelCtx = document.getElementById('gradeLevelChart').getContext('2d');
        new Chart(gradeLevelCtx, {
            type: 'pie',
            data: {
                labels: @json(array_keys($gradeLevelCounts)),
                datasets: [{
                    data: @json(array_values($gradeLevelCounts)),
                    backgroundColor: [
                        '#007bff',
                        '#28a745',
                        '#dc3545',
                        '#ffc107',
                        '#17a2b8',
                        '#6610f2'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Student Distribution by Grade Level'
                    }
                }
            }
        });

        // Bar Chart for Medical Reports by Status
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($statusCounts)),
                datasets: [{
                    label: 'Reports',
                    data: @json(array_values($statusCounts)),
                    backgroundColor: '#007bff',
                    borderColor: '#0056b3',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Reports'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Status'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Medical Reports by Status'
                    }
                }
            }
        });
    </script>
@endsection
