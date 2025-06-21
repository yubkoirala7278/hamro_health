@extends('admin.master')

@section('header-content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .card {
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }
        canvas{
            min-height: 350px;
            max-height: 350px;
        }
    </style>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users me-2"></i> Total Students</h5>
                    <p class="card-text fs-4">{{ $totalStudents }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-school me-2"></i> Grade Levels</h5>
                    <p class="card-text fs-4">{{ $gradeLevels }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-venus-mars me-2"></i> Gender Distribution</h5>
                    <p class="card-text">
                        @foreach ($genderDistribution as $gender => $count)
                            {{ $gender }}: {{ $count }}{{ $loop->last ? '' : ', ' }}
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-3">
         <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Grade Level Distribution</h5>
                    <div class="chart-container">
                        <canvas id="gradeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gender Distribution</h5>
                    <div class="chart-container">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
       
    </div>

    <!-- Student Table -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Recent Students</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Grade Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentData as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student['name'] }}</td>
                                <td>{{ $student['email'] }}</td>
                                <td>{{ $student['phone'] }}</td>
                                <td>{{ $student['grade_level'] }}</td>
                                <td>{!! $student['action'] !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Gender Distribution Chart
        new Chart(document.getElementById('genderChart'), {
            type: 'pie',
            data: {
                labels: @json(array_keys($genderDistribution)),
                datasets: [{
                    data: @json(array_values($genderDistribution)),
                    backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56'],
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Gender Distribution'
                    }
                }
            }
        });

        // Grade Level Distribution Chart
        const gradeData = @json($studentData->groupBy('grade_level')->map->count());
        new Chart(document.getElementById('gradeChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(gradeData),
                datasets: [{
                    label: 'Students per Grade',
                    data: Object.values(gradeData),
                    backgroundColor: '#36A2EB',
                    borderColor: '#36A2EB',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'Grade Level Distribution'
                    }
                }
            }
        });
    </script>
@endsection
