@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Update your personal details and password')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Profile Details --}}
    <div class="card p-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Personal Details</h2>

        @if($errors->any())
        <div class="alert-error mb-5">
            <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Email Address</label>
                    <input type="email" value="{{ $user->email }}" class="form-input bg-gray-50" disabled>
                    <p class="text-xs text-gray-400 mt-1">Email cannot be changed. Contact an admin.</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="+233 ...">
                </div>
                <div>
                    <label class="form-label">Department</label>
                    <input type="text" name="department" value="{{ old('department', $user->department) }}" class="form-input" placeholder="e.g. IT Support">
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-3 text-xs text-gray-500 grid grid-cols-2 gap-3">
                <div><span class="font-medium text-gray-400">Role:</span> {{ ucfirst($user->role) }}</div>
                <div><span class="font-medium text-gray-400">Shift:</span> {{ $user->shift_label }}</div>
                <div><span class="font-medium text-gray-400">Account Status:</span>
                    <span class="{{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div><span class="font-medium text-gray-400">Member since:</span> {{ $user->created_at->format('d M Y') }}</div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="card p-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-1">Change Password</h2>
        <p class="text-xs text-gray-400 mb-4">Leave all fields blank if you don't want to change your password.</p>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PUT')
            {{-- Hidden fields to preserve profile values when submitting password only --}}
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="phone" value="{{ $user->phone }}">
            <input type="hidden" name="department" value="{{ $user->department }}">

            <div>
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-input" autocomplete="current-password">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-input" autocomplete="new-password">
                </div>
                <div>
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-input" autocomplete="new-password">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Update Password</button>
            </div>
        </form>
    </div>

</div>
@endsection
