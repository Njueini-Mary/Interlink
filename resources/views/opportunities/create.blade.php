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
  <!-- DEBUG: {{ json_encode($categories) }} -->
  @include('components.navbar')

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-9 col-xl-8">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <h1 class="display-6 fw-bold mb-2 text-primary"><i class="fas fa-briefcase"></i> Post a New Opportunity</h1>
              <p class="lead text-muted">Provide as much detail as possible to attract the right students.</p>
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

      <!-- Opportunity Title & Description -->
      <div class="row g-4 mb-3">
        <div class="col-md-6">
          <label for="title" class="form-label">Opportunity Title</label>
          <input type="text" class="form-control" id="title" name="title" placeholder="E.g., Software Development Internship" value="{{ old('title') }}" required>
        </div>
        <div class="col-md-6">
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
        <div class="col-md-6">
          <label for="new_category" class="form-label">Or Create New Category</label>
          <input type="text" class="form-control" id="new_category" name="new_category" placeholder="Enter new category if not available" value="{{ old('new_category') }}">
        </div>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Opportunity Description</label>
        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Describe the opportunity in detail" required>{{ old('description') }}</textarea>
      </div>

      <!-- Opportunity Type -->
      <div class="mb-3">
        <label class="form-label">Opportunity Type</label>
        <div class="d-flex gap-3">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="type_internship" value="WBL" {{ old('type') === 'WBL' ? 'checked' : '' }} required>
            <label class="form-check-label" for="type_internship"><i class="fas fa-user-graduate me-1"></i> Work Based Learning</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="type_placement" value="SBL" {{ old('type') === 'SBL' ? 'checked' : '' }}>
            <label class="form-check-label" for="type_placement"><i class="fas fa-briefcase me-1"></i> Service Based Learning</label>
          </div>
        </div>
      </div>

      <!-- Pricing Section -->
      <div id="pricingSection" class="mb-4">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label for="currency" class="form-label">Currency</label>
            <select class="form-select" id="currency" name="currency" required>
              <option value="KES" {{ old('currency', 'KES') == 'KES' ? 'selected' : '' }}>KES (Kenyan Shilling)</option>
              <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
              <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
              <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (British Pound)</option>
              <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>INR (Indian Rupee)</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Compensation Type</label>
            <div class="d-flex gap-2">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="compensation_type" id="comp_type_monthly" value="monthly" {{ old('compensation_type', 'monthly') === 'monthly' ? 'checked' : '' }} required>
                <label class="form-check-label" for="comp_type_monthly"><i class="fas fa-calendar-alt me-1"></i> Monthly</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="compensation_type" id="comp_type_hourly" value="hourly" {{ old('compensation_type') === 'hourly' ? 'checked' : '' }}>
                <label class="form-check-label" for="comp_type_hourly"><i class="fas fa-clock me-1"></i> Hourly</label>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div id="stipendField">
              <label for="monthly_stipend" class="form-label">Monthly Stipend</label>
              <input type="number" step="0.01" class="form-control" id="monthly_stipend" name="monthly_stipend" placeholder="E.g., 5000" value="{{ old('monthly_stipend') }}">
            </div>
            <div id="hourlyRateFields" class="row" style="{{ old('compensation_type') === 'hourly' ? '' : 'display:none;' }}">
              <div class="col-6">
                <label for="min_rate" class="form-label">Min Rate (hr)</label>
                <input type="number" step="0.01" class="form-control" id="min_rate" name="min_rate" placeholder="E.g., 100" value="{{ old('min_rate') }}">
              </div>
              <div class="col-6">
                <label for="max_rate" class="form-label">Max Rate (hr)</label>
                <input type="number" step="0.01" class="form-control" id="max_rate" name="max_rate" placeholder="E.g., 200" value="{{ old('max_rate') }}">
              </div>
            </div>
          </div>
        </div>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const monthlyRadio = document.getElementById('comp_type_monthly');
          const hourlyRadio = document.getElementById('comp_type_hourly');
          const stipendField = document.getElementById('stipendField');
          const hourlyFields = document.getElementById('hourlyRateFields');
          function toggleFields() {
            if (monthlyRadio.checked) {
              stipendField.style.display = '';
              hourlyFields.style.display = 'none';
            } else {
              stipendField.style.display = 'none';
              hourlyFields.style.display = '';
            }
          }
          monthlyRadio.addEventListener('change', toggleFields);
          hourlyRadio.addEventListener('change', toggleFields);
          toggleFields();
        });
      </script>

      <!-- Deadline & New Category -->
      <div class="row g-4 mb-3">
        <div class="col-md-6">
          <label for="deadline" class="form-label">Deadline</label>
          <input type="date" class="form-control" id="deadline" name="deadline" value="{{ old('deadline') }}" required>
        </div>
      </div>

      <!-- Required Skills -->
      <div class="mb-4">
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
      <div class="mb-4">
        <label for="new_skills" class="form-label">Or add new skills (comma separated)</label>
        <input type="text" class="form-control" id="new_skills" name="new_skills" placeholder="E.g., React, Node.js" value="{{ old('new_skills') }}">
      </div>

      <!-- Submit -->
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-5 shadow"><i class="fas fa-paper-plane me-2"></i>Post Job</button>
      </div>
    </form>
  </div>
        </div>
      </div>
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
