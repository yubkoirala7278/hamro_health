@extends('admin.master')

@section('header-content')
    {{-- CKEditor CDN --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
    <style>
        .form-select{
            display: block !important;
        }
    </style>
@endsection

@section('content')
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Add Medical Report for {{ $student->name }}</h4>
            <a href="{{ route('admin.students.medical_reports.index', $student->slug) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.students.medical_reports.store', $student->slug) }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- Left Column --}}
                    <div class="col-md-6">
                        <h5 class="mb-3 border-bottom pb-2">Medical Details</h5>

                        <div class="mb-3">
                            <label for="report_date" class="form-label">Report Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="report_date" id="report_date" class="form-control"
                                value="{{ old('report_date') }}" required>
                            @error('report_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="medical_condition" class="form-label">Medical Condition</label>
                            <input type="text" name="medical_condition" id="medical_condition" class="form-control"
                                value="{{ old('medical_condition') }}">
                            @error('medical_condition')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="allergies" class="form-label">Allergies</label>
                            <textarea name="allergies" id="allergies" class="form-control">{{ old('allergies') }}</textarea>
                            @error('allergies')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="medications" class="form-label">Medications</label>
                            <textarea name="medications" id="medications" class="form-control">{{ old('medications') }}</textarea>
                            @error('medications')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="vaccinations" class="form-label">Vaccinations</label>
                            <textarea name="vaccinations" id="vaccinations" class="form-control">{{ old('vaccinations') }}</textarea>
                            @error('vaccinations')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="col-md-6">
                        <h5 class="mb-3 border-bottom pb-2">Vitals & Doctor Info</h5>

                        <div class="mb-3">
                            <label for="doctor_name" class="form-label">Doctor Name</label>
                            <input type="text" name="doctor_name" id="doctor_name" class="form-control"
                                value="{{ old('doctor_name') }}">
                            @error('doctor_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="doctor_contact" class="form-label">Doctor Contact</label>
                            <input type="text" name="doctor_contact" id="doctor_contact" class="form-control"
                                value="{{ old('doctor_contact') }}">
                            @error('doctor_contact')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="specialist" class="form-label">Specialist</label>
                            <input type="text" name="specialist" id="specialist" class="form-control"
                                value="{{ old('specialist') }}">
                            @error('specialist')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="mnc_number" class="form-label">MNC Number</label>
                            <input type="text" name="mnc_number" id="mnc_number" class="form-control"
                                value="{{ old('mnc_number') }}">
                            @error('mnc_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="blood_pressure" class="form-label">Blood Pressure</label>
                                <input type="text" name="blood_pressure" id="blood_pressure" class="form-control"
                                    value="{{ old('blood_pressure') }}" placeholder="e.g. 120/80">
                                @error('blood_pressure')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pulse_rate" class="form-label">Pulse Rate (bpm)</label>
                                <input type="text" name="pulse_rate" id="pulse_rate" class="form-control"
                                    value="{{ old('pulse_rate') }}">
                                @error('pulse_rate')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="temperature" class="form-label">Temperature</label>
                                <input type="text" name="temperature" id="temperature" class="form-control"
                                    value="{{ old('temperature') }}" placeholder="e.g. 36.6°C">
                                @error('temperature')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="respiratory_rate" class="form-label">Respiratory Rate (bpm)</label>
                                <input type="text" name="respiratory_rate" id="respiratory_rate" class="form-control"
                                    value="{{ old('respiratory_rate') }}">
                                @error('respiratory_rate')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="oxygen_saturation" class="form-label">Oxygen Saturation (%)</label>
                            <input type="text" name="oxygen_saturation" id="oxygen_saturation" class="form-control"
                                value="{{ old('oxygen_saturation') }}">
                            @error('oxygen_saturation')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="report_file" class="form-label">Upload Report File</label>
                            <input type="file" name="report_file" id="report_file" class="form-control"
                                accept=".pdf,.jpg,.png">
                            @error('report_file')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived
                                </option>
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4">Save Medical Report</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editors = ['allergies', 'medications', 'vaccinations', 'notes'];
            editors.forEach(id => {
                ClassicEditor
                    .create(document.querySelector(`#${id}`), {
                        removePlugins: ['Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar',
                            'ImageUpload', 'MediaEmbed'
                        ]
                    })
                    .catch(error => console.error(`Error initializing CKEditor for ${id}:`, error));
            });
        });
    </script>
@endsection
