<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Post a New Opportunity | Interlink Placement System</title>
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
    <div class="row mb-4">
      <div class="col-lg-8 mx-auto text-center">
  <h1 class="display-5 fw-bold">Post a New Opportunity</h1>
  <p class="lead text-muted">Provide as much detail as possible to attract the right students.</p>
      </div>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger">
          <ul class="mb-0">
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif

  <form action="{{ route('opportunities.store') }}" method="POST">
      @csrf

      <!-- Opportunity Title -->
      <div class="mb-3">
        <label for="title" class="form-label">Opportunity Title</label>
        <input type="text" class="form-control" id="title" name="title" placeholder="E.g., Software Development Internship" value="{{ old('title') }}" required>
      </div>

      <!-- Opportunity Description -->
      <div class="mb-3">
        <label for="description" class="form-label">Opportunity Description</label>
        <textarea class="form-control" id="description" name="description" rows="6" placeholder="Describe the opportunity in detail" required>{{ old('description') }}</textarea>
      </div>

      <!-- Opportunity Type -->
      <div class="mb-3">
        <label class="form-label">Opportunity Type</label>
        <div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="type_internship" value="internship" {{ old('type') === 'internship' ? 'checked' : '' }} required>
            <label class="form-check-label" for="type_internship">Internship</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="type_placement" value="placement" {{ old('type') === 'placement' ? 'checked' : '' }}>
            <label class="form-check-label" for="type_placement">Placement</label>
          </div>
        </div>
      </div>

      <!-- Pricing Section -->
      <div id="pricingSection" class="mb-3">
        <!-- Fixed Price Field -->
        <div id="fixedPriceField">
          <label for="budget" class="form-label">Budget (Fixed Price)</label>
          <input type="number" step="0.01" class="form-control" id="budget" name="budget" placeholder="E.g., 50000" value="{{ old('budget') }}">
        </div>
        <!-- Hourly Rate Fields -->
        <div id="hourlyRateFields" class="row" style="display: none;">
          <div class="col-md-6">
            <label for="min_rate" class="form-label">Minimum Rate (per hour)</label>
            <input type="number" step="0.01" class="form-control" id="min_rate" name="min_rate" placeholder="E.g., 1000" value="{{ old('min_rate') }}">
          </div>
          <div class="col-md-6">
            <label for="max_rate" class="form-label">Maximum Rate (per hour)</label>
            <input type="number" step="0.01" class="form-control" id="max_rate" name="max_rate" placeholder="E.g., 2000" value="{{ old('max_rate') }}">
          </div>
        </div>
      </div>

      <!-- Deadline -->
      <div class="mb-3">
        <label for="deadline" class="form-label">Deadline</label>
        <input type="date" class="form-control" id="deadline" name="deadline" value="{{ old('deadline') }}" required>
      </div>

      <!-- Category -->
      <div class="mb-3">
        <label for="category_id" class="form-label">Job Category</label>
        <select class="form-select" id="category_id" name="category_id">
          <option value="">-- Select Category --</option>
          @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
              {{ $category->name }}
            </option>
          @endforeach
        </select>
      </div>
      <!-- Optionally create a new category -->
      <div class="mb-3">
        <label for="new_category" class="form-label">Or Create New Category</label>
        <input type="text" class="form-control" id="new_category" name="new_category" placeholder="Enter new category if not available" value="{{ old('new_category') }}">
      </div>

      <!-- Required Skills -->
      <div class="mb-3">
        <label for="existing_skills" class="form-label">Select Required Skills</label>
        <select id="existing_skills" name="existing_skills[]" multiple class="form-select">
          @foreach($allSkills as $skill)
            <option value="{{ $skill->id }}" 
              {{ (collect(old('existing_skills'))->contains($skill->id)) ? 'selected' : '' }}>
              {{ $skill->name }}
            </option>
          @endforeach
        </select>
        <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple skills.</small>
      </div>
      <div class="mb-3">
        <label for="new_skills" class="form-label">Or add new skills (comma separated)</label>
        <input type="text" class="form-control" id="new_skills" name="new_skills" placeholder="E.g., React, Node.js" value="{{ old('new_skills') }}">
      </div>

      <!-- Submit -->
      <div class="text-center">
        <button type="submit" class="btn btn-primary btn-lg">Post Job</button>
      </div>
    </form>
  </div>

  <!-- JavaScript to toggle pricing fields -->
  <script>
    const jobTypeRadios = document.querySelectorAll('input[name="job_type"]');
    const fixedPriceField = document.getElementById('fixedPriceField');
    const hourlyRateFields = document.getElementById('hourlyRateFields');

    function updatePricingFields() {
      const selectedType = document.querySelector('input[name="job_type"]:checked').value;
      if(selectedType === 'hourly'){
        fixedPriceField.style.display = 'none';
        hourlyRateFields.style.display = 'flex';
      } else {
        fixedPriceField.style.display = 'block';
        hourlyRateFields.style.display = 'none';
      }
    }

    jobTypeRadios.forEach(radio => {
      radio.addEventListener('change', updatePricingFields);
    });

    if(document.querySelector('input[name="job_type"]:checked')){
      updatePricingFields();
    }
  </script>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
