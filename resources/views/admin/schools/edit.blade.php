@extends('admin.master')

@section('content')
@php
        // if $schoolAdmin is not passed explicitly, get first schoolAdmin here
        $schoolAdmin = $schoolAdmin ?? $school->schoolAdmins->first();
    @endphp
    <div class="card">
        <div class="card-header">
            <h4>Edit School: {{ $school->name }}</h4>
        </div>
        <div class="card-body">
                       <form action="{{ route('admin.schools.update', $schoolAdmin->slug) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">School Name</label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $schoolAdmin->name ?? '') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" name="address" id="address"
                           class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $school->address) }}">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" name="phone" id="phone"
                           class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $school->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">School Admin Email</label>
                    <input type="email" name="email" id="email"
                           class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $schoolAdmin->email ?? '') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password (Leave blank to keep unchanged)</label>
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Update School</button>
                <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection