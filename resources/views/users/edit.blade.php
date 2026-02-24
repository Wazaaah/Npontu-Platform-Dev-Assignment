@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">New Password <span class="text-gray-400 text-xs">(leave blank to keep current)</span></label>
                    <input type="password" name="password" class="form-input">
                </div>
                <div>
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-input">
                </div>
                <div>
                    <label class="form-label">Role</label>
                    <select name="role" class="form-input" required>
                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Shift</label>
                    <select name="shift" class="form-input">
                        <option value="">Not assigned</option>
                        <option value="morning" {{ old('shift', $user->shift) === 'morning' ? 'selected' : '' }}>Morning</option>
                        <option value="night" {{ old('shift', $user->shift) === 'night' ? 'selected' : '' }}>Night</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" value="{{ old('department', $user->department) }}" class="form-input">
                </div>
                <div class="col-span-2">
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600">
                        <label for="is_active" class="text-sm text-gray-700">Account Active</label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
