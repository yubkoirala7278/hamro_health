@extends('admin.master')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top">
                <h5 class="mb-0 fw-semibold text-white">Medical Report: {{ $student->name }}</h5>
                <a href="{{ route('admin.students.medical_reports.index', $student->slug) }}" class="btn btn-light btn-sm rounded-pill">← Back to Reports</a>
            </div>

            <div class="card-body bg-light rounded-bottom p-4">
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <h6 class="text-muted border-bottom pb-2 mb-4 fw-semibold">Report Details</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-5 fw-medium text-dark">Report Date</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->report_date->format('d M Y') }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Medical Condition</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->medical_condition ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Allergies</dt>
                            <dd class="col-sm-7 text-dark">{!! $report->allergies ?? 'N/A' !!}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Medications</dt>
                            <dd class="col-sm-7 text-dark">{!! $report->medications ?? 'N/A' !!}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Vaccinations</dt>
                            <dd class="col-sm-7 text-dark">{!! $report->vaccinations ?? 'N/A' !!}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Notes</dt>
                            <dd class="col-sm-7 text-dark">{!! $report->notes ?? 'N/A' !!}</dd>
                        </dl>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <h6 class="text-muted border-bottom pb-2 mb-4 fw-semibold">Doctor & Vitals</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-5 fw-medium text-dark">Doctor Name</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->doctor_name ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Doctor Contact</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->doctor_contact ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Specialist</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->specialist ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">MNC Number</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->mnc_number ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Blood Pressure</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->blood_pressure ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Pulse Rate</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->pulse_rate ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Temperature</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->temperature ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Respiratory Rate</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->respiratory_rate ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Oxygen Saturation</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->oxygen_saturation ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Report File</dt>
                            <dd class="col-sm-7 text-dark">
                                @if ($report->report_file_url)
                                    <a href="{{ $report->report_file_url }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">Download Report</a>
                                @else
                                    N/A
                                @endif
                            </dd>

                            <dt class="col-sm-5 fw-medium text-dark">Status</dt>
                            <dd class="col-sm-7 text-dark">
                                <span class="badge bg-{{ $report->status == 'active' ? 'success' : 'secondary' }} rounded-pill px-3 py-2">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </dd>

                            <dt class="col-sm-5 fw-medium text-dark">Created By</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->creator->name ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 fw-medium text-dark">Created At</dt>
                            <dd class="col-sm-7 text-dark">{{ $report->created_at->format('d M Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection