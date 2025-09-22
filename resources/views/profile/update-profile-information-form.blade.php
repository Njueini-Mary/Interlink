<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Update Profile | Interlink</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Optional FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom styles -->
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <style>
    .toast-container {
      z-index: 2147483647;
      /* Set to the maximum possible value */
    }
  </style>
</head>

<body>
  @include('layouts.navbar')

  <div class="container py-5">
    <!-- Toast Notifications -->
    <div aria-live="polite" aria-atomic="true" class="position-relative">
      <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
        @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive"
          aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
              aria-label="Close"></button>
          </div>
        </div>
        @endif
        @if(session('error'))
        <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive"
          aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
              aria-label="Close"></button>
          </div>
        </div>
        @endif
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-9 col-xl-8">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <h1 class="display-6 fw-bold mb-2"><i class="fas fa-user-edit"></i> Update Your Profile</h1>
              <p class="lead text-muted">Manage your professional details, update your skills, and profile
                information.</p>
            </div>
            <!-- Display Validation Errors -->
            @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              @if($user->role === 'student')
              <h4 class="mb-4 text-primary"><i class="fas fa-user-graduate"></i> Student Details</h4>
              <div class="row g-4 align-items-center mb-3">
                <div class="col-md-3 text-center">
                  <label for="profile_photo" class="form-label">Profile Photo</label>
                  <input type="file" id="profile_photo" name="profile_photo"
                    class="form-control mb-2">
                  @if(isset($user->student->profile_photo))
                  <img src="{{ asset('storage/' . $user->student->profile_photo) }}"
                    alt="Profile Photo" class="rounded-circle border border-3 border-primary shadow"
                    width="100">
                  @endif
                </div>
                <div class="col-md-9">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="name" class="form-label">Name</label>
                      <input type="text" id="name" name="name" class="form-control"
                        value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                      <label for="email" class="form-label">Email Address</label>
                      <input type="email" id="email" name="email" class="form-control"
                        value="{{ old('email', $user->email) }}" required>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mb-4">
                <label for="university" class="form-label">University</label>
                <input type="text" id="university" name="university" class="form-control"
                  value="{{ old('university', $user->student->university ?? '') }}"
                  placeholder="E.g., University of Nairobi">
              </div>
              <div class="mb-4">
                <label for="course" class="form-label">Course</label>
                <input type="text" id="course" name="course" class="form-control"
                  value="{{ old('course', $user->student->course ?? '') }}"
                  placeholder="E.g., Computer Science">
              </div>
              <div class="mb-4">
                <label for="year" class="form-label">Year</label>
                <input type="text" id="year" name="year" class="form-control"
                  value="{{ old('year', $user->student->year ?? '') }}" placeholder="E.g., 3">
              </div>
              <div class="mb-4">
                <label for="resume" class="form-label">Resume (PDF)</label>
                <input type="file" id="resume" name="resume" class="form-control">
                @if(isset($user->student->resume))
                <div class="mt-2">
                  <a href="{{ asset('storage/' . $user->student->resume) }}" target="_blank">View
                    Current Resume</a>
                </div>
                @endif
              </div>
              <div class="mb-4">
                <label for="academic_field" class="form-label">Academic Field</label>
                <input type="text" id="academic_field" name="academic_field" class="form-control"
                  value="{{ old('academic_field', $user->student->academic_field ?? '') }}"
                  placeholder="E.g., Computer Science, Business Administration">
              </div>
              <div class="mb-4">
                <label for="bio" class="form-label">Bio</label>
                <textarea id="bio" name="bio" class="form-control" rows="4"
                  placeholder="Tell us about yourself...">{{ old('bio', $user->student->bio ?? '') }}</textarea>
              </div>
              <div class="mb-4 form-check">
                <input type="checkbox" id="is_available" name="is_available" class="form-check-input"
                  value="1"
                  {{ old('is_available', $user->student->is_available ?? false) ? 'checked' : '' }}>
                <label for="is_available" class="form-check-label">I am currently available for
                  opportunities</label>
              </div>

              <!-- STUDENT SKILLS -->
              <div class="mb-4">
                <label for="existing_skills" class="form-label">Select Your Existing Skills</label>
                <select id="existing_skills" name="existing_skills[]" multiple class="form-select">
                  @foreach($allSkills as $skill)
                  <option value="{{ $skill->id }}"
                    {{ (isset($user->student->skills) && $user->student->skills->pluck('id')->contains($skill->id)) ? 'selected' : '' }}>
                    {{ $skill->name }}
                  </option>
                  @endforeach
                </select>
                <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple
                  skills.</small>
              </div>
              <div class="mb-4">
                <label for="new_skills" class="form-label">Or add new skills (comma separated)</label>
                <input type="text" id="new_skills" name="new_skills" class="form-control"
                  value="{{ old('new_skills') }}" placeholder="E.g., Vue.js, Tailwind CSS">
              </div>

              <!-- STUDENT CATEGORIES -->
              @if(isset($categories) && $categories->count())
              <div class="mb-4">
                <label for="categories" class="form-label">Your Categories</label>
                <select id="categories" name="categories[]" multiple class="form-select">
                  @foreach($categories as $category)
                  <option value="{{ $category->id }}"
                    {{ (isset($user->student->categories) && $user->student->categories->pluck('id')->contains($category->id)) ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                  @endforeach
                </select>
                <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple
                  categories.</small>
              </div>
              @endif


              @elseif($user->role === 'organization')
              <h4 class="mb-3">Organization Details</h4>
              <div class="mb-4">
                <label for="company_logo" class="form-label">Organization Logo</label>
                <input type="file" id="company_logo" name="company_logo" class="form-control">
                @if(isset($user->organizationProfile->logo) && $user->organizationProfile->logo)
                <div class="mt-2">
                  <img src="{{ asset('storage/' . $user->organizationProfile->logo) }}"
                    alt="Organization Logo" class="img-thumbnail" width="100">
                </div>
                @endif
              </div>
              <div class="mb-4">
                <label for="organization_name" class="form-label">Organization Name</label>
                <input type="text" id="organization_name" name="organization_name" class="form-control"
                  value="{{ old('organization_name', $user->organizationProfile->name ?? '') }}"
                  placeholder="Your organization name">
              </div>
              <div class="mb-4">
                <label for="organization_profile" class="form-label">Organization Profile</label>
                <textarea id="organization_profile" name="organization_profile" class="form-control"
                  rows="4"
                  placeholder="Tell us about your organization...">{{ old('organization_profile', $user->organizationProfile->profile ?? '') }}</textarea>
              </div>
              <div class="mb-4">
                <label for="location" class="form-label">Location</label>
                <input type="text" id="location" name="location" class="form-control"
                  value="{{ old('location', $user->organizationProfile->location ?? '') }}"
                  placeholder="City, State or Country">
              </div>
              <div class="mb-4">
                <label for="industry" class="form-label">Industry</label>
                <input type="text" id="industry" name="industry" class="form-control"
                  value="{{ old('industry', $user->organizationProfile->industry ?? '') }}"
                  placeholder="E.g., Technology, Healthcare">
              </div>
              <div class="mb-4">
                <label for="website" class="form-label">Website</label>
                <input type="text" id="website" name="website" class="form-control"
                  value="{{ old('website', $user->organizationProfile->website ?? '') }}"
                  placeholder="https://example.com">
              </div>
              <div class="mb-4">
                <label for="contact_email" class="form-label">Contact Email</label>
                <input type="email" id="contact_email" name="contact_email" class="form-control"
                  value="{{ old('contact_email', $user->organizationProfile->contact_email ?? '') }}"
                  placeholder="contact@example.com">
              </div>
              <div class="mb-4">
                <label for="contact_phone" class="form-label">Contact Phone</label>
                <input type="text" id="contact_phone" name="contact_phone" class="form-control"
                  value="{{ old('contact_phone', $user->organizationProfile->contact_phone ?? '') }}"
                  placeholder="+254 700 000000">
              </div>

              @elseif($user->role === 'admin')
              <h4 class="mb-3">Admin Profile</h4>
              <div class="mb-4">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control"
                  value="{{ old('name', $user->name) }}" required>
              </div>
              <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control"
                  value="{{ old('email', $user->email) }}" required>
              </div>
              @endif

              <!-- Submit Button -->
              <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Update Profile</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>



  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
@include('layouts.footer')