<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Update Profile | FreelanceHub</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Optional FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom styles -->
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
  @include('layouts.navbar')

  <div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
      <div class="col-lg-8 mx-auto text-center">
        <h1 class="display-5 fw-bold"><i class="fas fa-user-edit"></i> Update Your Profile</h1>
        <p class="lead text-muted">Manage your professional details, update your skills, and profile information.</p>
      </div>
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

    <!-- Update Form -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @if($user->hasRole('freelancer'))
        <h4 class="mb-3">Freelancer Details</h4>
        <!-- Freelancer Profile Photo -->
        <div class="mb-4">
          <label for="profile_photo" class="form-label">Freelancer Profile Photo</label>
          <input type="file" id="profile_photo" name="profile_photo" class="form-control">
          @if(isset($user->freelancer->profile_photo))
            <div class="mt-2">
              <img src="{{ asset('storage/' . $user->freelancer->profile_photo) }}" alt="Freelancer Photo"
                   class="rounded-circle" width="100">
            </div>
          @endif
        </div>
              <!-- Common User Fields -->
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
        <div class="mb-4">
          <label for="title" class="form-label">Professional Title</label>
          <input type="text" id="title" name="title" class="form-control"
                 value="{{ old('title', $user->freelancer->title ?? '') }}"
                 placeholder="E.g., Full Stack Developer">
        </div>
        <div class="mb-4">
          <label for="bio" class="form-label">Professional Bio</label>
          <textarea id="bio" name="bio" class="form-control" rows="4"
                    placeholder="Tell us about your experience...">{{ old('bio', $user->freelancer->bio ?? '') }}</textarea>
        </div>
        <div class="mb-4">
          <label for="hourly_rate" class="form-label">Hourly Rate (Ksh)</label>
          <input type="number" step="0.01" id="hourly_rate" name="hourly_rate" class="form-control"
                 value="{{ old('hourly_rate', $user->freelancer->hourly_rate ?? '') }}"
                 placeholder="E.g., 1500.00">
        </div>
        <div class="mb-4 form-check">
          <input type="checkbox" id="is_online" name="is_online" class="form-check-input" value="1"
                 {{ old('is_online', $user->freelancer->is_online ?? false) ? 'checked' : '' }}>
          <label for="is_online" class="form-check-label">I am currently available for work</label>
        </div>

        <!-- FREELANCER SKILLS -->
        <div class="mb-4">
          <label for="existing_skills" class="form-label">Select Your Existing Skills</label>
          <select id="existing_skills" name="existing_skills[]" multiple class="form-select">
            @foreach($allSkills as $skill)
              <option value="{{ $skill->id }}"
                {{ (isset($user->freelancer->skills) && $user->freelancer->skills->pluck('id')->contains($skill->id)) ? 'selected' : '' }}>
                {{ $skill->name }}
              </option>
            @endforeach
          </select>
          <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple skills.</small>
        </div>
        <div class="mb-4">
          <label for="new_skills" class="form-label">Or add new skills (comma separated)</label>
          <input type="text" id="new_skills" name="new_skills" class="form-control" 
                 value="{{ old('new_skills') }}" placeholder="E.g., Vue.js, Tailwind CSS">
        </div>

        <!-- FREELANCER CATEGORIES -->
        @if(isset($categories) && $categories->count())
          <div class="mb-4">
            <label for="categories" class="form-label">Your Categories</label>
            <select id="categories" name="categories[]" multiple class="form-select">
              @foreach($categories as $category)
                <option value="{{ $category->id }}"
                  {{ (isset($user->freelancer->categories) && $user->freelancer->categories->pluck('id')->contains($category->id)) ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
            <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple categories.</small>
          </div>
        @endif

      @elseif($user->hasRole('client'))
        <h4 class="mb-3">Client Details</h4>
        <div class="mb-4">
          <label for="company_logo" class="form-label">Company Logo</label>
          <input type="file" id="company_logo" name="company_logo" class="form-control">
          @if(isset($user->client->company_logo) && $user->client->company_logo)
            <div class="mt-2">
              <img src="{{ asset('storage/' . $user->client->company_logo) }}" alt="Company Logo" class="img-thumbnail" width="100">
            </div>
          @endif
        </div>
        <div class="mb-4">
          <label for="company_name" class="form-label">Company Name</label>
          <input type="text" id="company_name" name="company_name" class="form-control"
                 value="{{ old('company_name', $user->client->company_name ?? '') }}"
                 placeholder="Your company or business name">
        </div>
        <div class="mb-4">
          <label for="company_profile" class="form-label">Company Profile</label>
          <textarea id="company_profile" name="company_profile" class="form-control" rows="4"
                    placeholder="Tell us about your company...">{{ old('company_profile', $user->client->company_profile ?? '') }}</textarea>
        </div>
        <div class="mb-4">
          <label for="location" class="form-label">Location</label>
          <input type="text" id="location" name="location" class="form-control"
                 value="{{ old('location', $user->client->location ?? '') }}"
                 placeholder="City, State or Country">
        </div>
      @endif

      <!-- Submit Button -->
      <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">Update Profile</button>
      </div>
    </form>
  </div>

  @include('layouts.footer')

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
