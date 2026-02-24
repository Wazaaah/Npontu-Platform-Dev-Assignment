@extends('layouts.app')
@section('title', 'Add User')
@section('page-title', 'Add New User')

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5" id="user-form">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="form-label">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="+233 XX XXX XXXX">
                </div>
                <div>
                    <label class="form-label">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="form-input" required id="role-select" onchange="toggleShift()">
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div id="shift-field">
                    <label class="form-label">Shift Assignment <span class="text-red-500" id="shift-required">*</span></label>
                    <select name="shift" class="form-input" id="shift-select">
                        <option value="">Not assigned</option>
                        <option value="morning" {{ old('shift') === 'morning' ? 'selected' : '' }}>☀️ Morning (6AM – 6PM)</option>
                        <option value="night" {{ old('shift') === 'night' ? 'selected' : '' }}>🌙 Night (6PM – 6AM)</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1" id="shift-note-staff">Required for staff members.</p>
                    <p class="text-xs text-gray-400 mt-1 hidden" id="shift-note-admin">Admins are not assigned to shifts.</p>
                </div>
                <div class="col-span-2">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" value="{{ old('department') }}" class="form-input" placeholder="e.g. Applications Support">
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>
<script>
function toggleShift() {
    const role = document.getElementById('role-select').value;
    const shiftSelect = document.getElementById('shift-select');
    const shiftRequired = document.getElementById('shift-required');
    const noteStaff = document.getElementById('shift-note-staff');
    const noteAdmin = document.getElementById('shift-note-admin');
    if (role === 'admin') {
        shiftSelect.value = '';
        shiftSelect.disabled = true;
        shiftSelect.classList.add('opacity-50');
        shiftRequired.classList.add('hidden');
        noteStaff.classList.add('hidden');
        noteAdmin.classList.remove('hidden');
    } else {
        shiftSelect.disabled = false;
        shiftSelect.classList.remove('opacity-50');
        shiftRequired.classList.remove('hidden');
        noteStaff.classList.remove('hidden');
        noteAdmin.classList.add('hidden');
    }
}
// Run on page load in case old value was admin
toggleShift();
</script>
@endsection
