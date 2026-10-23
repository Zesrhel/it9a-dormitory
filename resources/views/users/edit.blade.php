@extends('layouts.app')
@section('title', auth()->id() === $user->id ? 'My Profile' : 'Edit User')
@section('content')
<div class="page-header" style="margin-bottom:28px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="display:flex;align-items:center;gap:10px;">
                <h1 class="page-title">{{ auth()->id() === $user->id ? 'My Profile' : 'Edit User' }}</h1>
                <span class="badge badge-info capitalize">{{ str_replace('_', ' ', $user->role) }}</span>
                <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($user->status) }}</span>
            </div>
            <p class="page-subtitle">{{ auth()->id() === $user->id ? 'Update your personal profile, photo, and account security' : 'Manage account credentials and dormitory access' }}</p>
        </div>
        <div>
            @if(auth()->user()->isAdmin() && auth()->id() !== $user->id)
            <a href="{{ route('users.index') }}" class="btn-secondary" style="display:inline-flex;align-items:center;gap:6px;">
                <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Users
            </a>
            @else
            <a href="{{ route('dashboard') }}" class="btn-secondary" style="display:inline-flex;align-items:center;gap:6px;">
                <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>
            @endif
        </div>
    </div>
</div>

@if($errors->any())
<div style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.35);color:#fca5a5;padding:14px 18px;border-radius:12px;margin-bottom:24px;font-size:14px;">
    <div style="font-weight:700;margin-bottom:6px;display:flex;align-items:center;gap:6px;">
        <svg style="width:18px;height:18px;color:#f87171;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        Please fix the following issues:
    </div>
    <ul style="list-style:none;margin:0;padding-left:4px;">
        @foreach($errors->all() as $e)
        <li style="margin-top:2px;">• {{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:300px 1fr;gap:28px;align-items:start;">
        {{-- Profile Photo & Overview Card --}}
        <div class="glass-card p-6" style="text-align:center;">
            <div style="position:relative;width:120px;height:120px;margin:0 auto 16px;">
                <img id="avatarImagePreview" 
                     src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : '' }}" 
                     alt="{{ $user->name }}" 
                     style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid rgba(99,102,241,0.5);box-shadow:0 8px 24px rgba(99,102,241,0.25);display:{{ $user->profile_picture ? 'block' : 'none' }};">
                <div id="avatarFallbackPreview" 
                     style="width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#9333ea);display:{{ $user->profile_picture ? 'none' : 'flex' }};align-items:center;justify-content:center;color:white;font-size:32px;font-weight:800;border:3px solid rgba(99,102,241,0.5);box-shadow:0 8px 24px rgba(99,102,241,0.25);margin:0 auto;">
                    {{ $user->initials }}
                </div>
            </div>

            <h3 style="font-size:17px;font-weight:700;color:#f1f5f9;margin:0 0 4px 0;">{{ $user->name }}</h3>
            <p style="font-size:12px;color:#94a3b8;margin:0 0 16px 0;word-break:break-all;">{{ $user->email }}</p>

            <div style="background:rgba(15,23,42,0.6);border:1px solid rgba(99,102,241,0.15);border-radius:12px;padding:14px;text-align:left;margin-bottom:16px;">
                <label class="form-label" for="profile_picture_input" style="margin-bottom:6px;display:flex;align-items:center;justify-content:space-between;">
                    <span>Upload Photo</span>
                    <span style="font-size:11px;color:#818cf8;">Max 2 MB</span>
                </label>
                <input type="file" 
                       name="profile_picture" 
                       id="profile_picture_input" 
                       class="form-input" 
                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                       style="padding:8px 10px;font-size:12px;background:rgba(30,41,59,0.7);">
                <p style="font-size:11px;color:#64748b;margin:6px 0 0 0;">Supported: JPG, PNG, or WebP.</p>

                @if($user->profile_picture)
                <div style="margin-top:12px;padding-top:10px;border-top:1px solid rgba(51,65,85,0.6);">
                    <label style="display:flex;align-items:center;gap:8px;font-size:12px;color:#f87171;cursor:pointer;">
                        <input type="checkbox" name="remove_profile_picture" id="remove_profile_picture_cb" value="1" style="accent-color:#ef4444;">
                        Remove current photo
                    </label>
                </div>
                @endif
            </div>

            <div style="text-align:left;font-size:12px;color:#64748b;padding:8px 4px;border-top:1px solid rgba(51,65,85,0.4);">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <span>Member Since</span>
                    <span style="color:#cbd5e1;font-weight:600;">{{ $user->created_at->format('M Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span>Account Status</span>
                    <span style="color:{{ $user->status === 'active' ? '#34d399' : '#f87171' }};font-weight:600;">{{ ucfirst($user->status) }}</span>
                </div>
            </div>
        </div>

        {{-- Form Details Column --}}
        <div style="display:flex;flex-direction:column;gap:20px;">
            <div class="glass-card p-6">
                <h3 style="font-size:16px;font-weight:700;color:#e0e7ff;margin:0 0 16px 0;display:flex;align-items:center;gap:8px;">
                    <svg style="width:18px;height:18px;color:#818cf8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Basic Information
                </h3>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="mb-4">
                    <div>
                        <label class="form-label" for="name">Full Name <span style="color:#f87171;">*</span></label>
                        <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="email">Email Address <span style="color:#f87171;">*</span></label>
                        <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div>
                    <label class="form-label" for="password">Change Password</label>
                    <input type="password" name="password" id="password" class="form-input" placeholder="Leave blank to keep current password">
                    <p style="font-size:12px;color:#64748b;margin-top:4px;">Enter at least 8 characters if you wish to set a new password.</p>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="glass-card p-6">
                <h3 style="font-size:16px;font-weight:700;color:#e0e7ff;margin:0 0 16px 0;display:flex;align-items:center;gap:8px;">
                    <svg style="width:18px;height:18px;color:#818cf8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Role & Account Status
                </h3>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" {{ auth()->id() === $user->id ? 'disabled' : 'required' }}>
                            <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="dorm_manager" {{ $user->role === 'dorm_manager' ? 'selected' : '' }}>Dorm Manager</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @if(auth()->id() === $user->id)
                        <p style="font-size:11px;color:#f59e0b;margin-top:4px;">Administrators cannot modify their own role.</p>
                        @endif
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" {{ auth()->id() === $user->id ? 'disabled' : 'required' }}>
                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @if(auth()->id() === $user->id)
                        <p style="font-size:11px;color:#f59e0b;margin-top:4px;">Administrators cannot deactivate their own account.</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if($user->isStudent() && $user->student)
            <div class="glass-card p-6">
                <h3 style="font-size:16px;font-weight:700;color:#c7d2fe;margin:0 0 16px 0;display:flex;align-items:center;gap:8px;">
                    <svg style="width:18px;height:18px;color:#a5b4fc;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    Student Information
                </h3>

                <div class="mb-4">
                    <label class="form-label" for="student_number">Student Number</label>
                    <input type="text" name="student_number" id="student_number" class="form-input" value="{{ old('student_number', $user->student->student_number) }}">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="mb-4">
                    <div>
                        <label class="form-label" for="course">Course</label>
                        <input type="text" name="course" id="course" class="form-input" value="{{ old('course', $user->student->course) }}">
                    </div>
                    <div>
                        <label class="form-label" for="year_level">Year Level</label>
                        <select name="year_level" id="year_level" class="form-select">
                            @foreach(['1st Year','2nd Year','3rd Year','4th Year'] as $yr)
                            <option value="{{ $yr }}" {{ $user->student->year_level === $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label" for="contact_number">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" class="form-input" value="{{ old('contact_number', $user->student->contact_number) }}">
                </div>
            </div>
            @endif

            <div style="display:flex;gap:12px;margin-top:4px;">
                <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:8px;">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
                @if(auth()->user()->isAdmin() && auth()->id() !== $user->id)
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
                @else
                <a href="{{ route('dashboard') }}" class="btn-secondary">Cancel</a>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- Admin-Only: Recent Activities on Profile Page --}}
@if(auth()->user()->isAdmin() && auth()->id() === $user->id && isset($activityLogs))
<div class="glass-card p-8" style="margin-top:32px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(139,92,246,0.15));display:flex;align-items:center;justify-content:center;">
                <svg style="width:20px;height:20px;color:#a5b4fc;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div>
                <h3 style="font-size:18px;font-weight:700;color:#e0e7ff;margin:0;">Recent System Activities</h3>
                <p style="font-size:12px;color:#64748b;margin:2px 0 0 0;">Last 20 administrative actions</p>
            </div>
        </div>
        <span class="badge badge-info">Admin Only</span>
    </div>

    @if($activityLogs->count())
    <div style="display:flex;flex-direction:column;gap:2px;">
        @foreach($activityLogs as $log)
        @php
            $iconBg = match($log->action_type) {
                'student_allocated' => 'rgba(16,185,129,0.15)',
                'application_accepted' => 'rgba(99,102,241,0.15)',
                'allocation_deactivated' => 'rgba(239,68,68,0.15)',
                default => 'rgba(148,163,184,0.15)',
            };
            $iconColor = match($log->action_type) {
                'student_allocated' => '#34d399',
                'application_accepted' => '#818cf8',
                'allocation_deactivated' => '#f87171',
                default => '#94a3b8',
            };
            $icon = match($log->action_type) {
                'student_allocated' => '→',
                'application_accepted' => '✓',
                'allocation_deactivated' => '✕',
                default => '•',
            };
        @endphp
        <div style="display:flex;align-items:flex-start;gap:14px;padding:14px 0;border-bottom:1px solid rgba(51,65,85,0.4);transition:all 0.2s ease;">
            <span style="flex-shrink:0;width:36px;height:36px;border-radius:10px;background:{{ $iconBg }};color:{{ $iconColor }};display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;">{{ $icon }}</span>
            <div style="flex:1;min-width:0;">
                <p style="font-size:13.5px;color:#cbd5e1;line-height:1.6;margin:0;">{{ $log->description }}</p>
                <p style="font-size:11px;color:#64748b;margin:4px 0 0 0;">{{ $log->created_at->format('M d, Y – g:i A') }}</p>
            </div>
            <span style="flex-shrink:0;font-size:11px;padding:3px 10px;border-radius:8px;background:{{ $iconBg }};color:{{ $iconColor }};font-weight:600;white-space:nowrap;">{{ ucfirst(str_replace('_', ' ', $log->action_type)) }}</span>
        </div>
        @endforeach
    </div>
    @else
    <div style="text-align:center;padding:40px 0;">
        <p style="font-size:14px;color:#64748b;">No activities recorded yet.</p>
    </div>
    @endif
</div>
@endif

@push('scripts')
<script>
document.getElementById('profile_picture_input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const imgPreview = document.getElementById('avatarImagePreview');
    const fallbackPreview = document.getElementById('avatarFallbackPreview');
    const removeCb = document.getElementById('remove_profile_picture_cb');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            imgPreview.src = evt.target.result;
            imgPreview.style.display = 'block';
            fallbackPreview.style.display = 'none';
            if (removeCb) removeCb.checked = false;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('remove_profile_picture_cb')?.addEventListener('change', function(e) {
    const imgPreview = document.getElementById('avatarImagePreview');
    const fallbackPreview = document.getElementById('avatarFallbackPreview');
    const fileInput = document.getElementById('profile_picture_input');

    if (e.target.checked) {
        imgPreview.style.display = 'none';
        fallbackPreview.style.display = 'flex';
        if (fileInput) fileInput.value = '';
    } else {
        const originalUrl = "{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : '' }}";
        if (originalUrl) {
            imgPreview.src = originalUrl;
            imgPreview.style.display = 'block';
            fallbackPreview.style.display = 'none';
        }
    }
});
</script>
@endpush
@endsection
