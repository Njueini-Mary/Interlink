<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Opportunity Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card {
            border: none;
            border-radius: 10px;
        }
        .card-title {
            font-size: 1.75rem;
            font-weight: bold;
        }
        .badge {
            font-size: 0.9rem;
        }
        .modal-header {
            border-bottom: 2px solid #e9ecef;
        }
        .modal-footer {
            border-top: 2px solid #e9ecef;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')

    <main class="container py-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="row">
            <div class="col-lg-8 mx-auto">
                <article class="card shadow-sm">
                    <div class="card-body">
                        <header class="mb-4">
                            <h1 class="card-title text-center">{{ $opportunity->title }}</h1>
                            <p class="card-subtitle text-muted text-center">
                                <i class="fas fa-building me-1"></i> {{ $opportunity->organization->name }}
                                <img src="{{ $opportunity->organization->logo ? asset('storage/' . $opportunity->organization->logo) : asset('images/default-organization.jpg') }}" 
                                     alt="{{ $opportunity->organization->name }}" 
                                     class="rounded-circle mx-2" 
                                     width="40" 
                                     height="40">
                                <span class="mx-2">•</span>
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $opportunity->organization->location ?? 'Remote' }}
                            </p>
                        </header>

                        <div class="text-center mb-4">
                            <span class="badge bg-info">{{ ucfirst($opportunity->type) }}</span>
                        </div>

                        <p class="card-text">{{ $opportunity->description }}</p>

                        <section class="mt-4">
                            <h5>Skills Required:</h5>
                            <div class="d-flex flex-wrap">
                                @foreach($opportunity->skills as $skill)
                                    <span class="badge bg-secondary me-2 mb-2">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                        </section>
                        @if(auth()->user() && auth()->user()->role === 'student')
                            @php
                                $student = auth()->user()->student;
                                $hasApplied = $student->applications->contains('opportunity_id', $opportunity->id);
                                $matcher = app(\App\Services\SkillMatcher::class);
                                $requiredSkills = $opportunity->skills->pluck('name')->toArray();
                            @endphp
                            <div class="mt-4 text-center">
                                <form method="POST" action="{{ route('opportunities.calculate_matches') }}">
                                    @csrf
                                    <input type="hidden" name="opportunity_id" value="{{ $opportunity->id }}">
                                    <button type="submit" class="btn btn-primary">Recalculate ML Match</button>
                                </form>
                            </div>
                            <section class="mt-4">
                                <h5>ML Match Scores</h5>
                                @if($hasApplied)
                                    @php
                                        $application = $student->applications->where('opportunity_id', $opportunity->id)->first();
                                        $applicantSkills = $application ? ($application->student->skills->pluck('name')->toArray() ?? []) : [];
                                        // If persisted matched/missing skills are missing or empty, recalculate
                                        $matchedSkills = null;
                                        $missingSkills = null;
                                        if (isset($application->matched_skills) && is_string($application->matched_skills) && strlen($application->matched_skills) > 2) {
                                            $matchedSkills = json_decode($application->matched_skills, true);
                                        }
                                        if (isset($application->missing_skills) && is_string($application->missing_skills) && strlen($application->missing_skills) > 2) {
                                            $missingSkills = json_decode($application->missing_skills, true);
                                        }
                                        if (!is_array($matchedSkills) || !count($matchedSkills)) {
                                            $matchedSkills = $matcher->getMatchedSkills($requiredSkills, $applicantSkills);
                                        }
                                        if (!is_array($missingSkills) || !count($missingSkills)) {
                                            $missingSkills = $matcher->getMissingSkills($requiredSkills, $applicantSkills);
                                        }
                                        $matchScore = $application->rf_score ?? $application->match_score ?? 0;
                                        $tfidfScore = $application->tfidf_score ?? 0;
                                        $logregScore = $application->logreg_score ?? 0;
                                        $matchMessage = $application->match_message ?? $matcher->getMatchMessage($matchScore, $matchedSkills, $missingSkills, $requiredSkills);
                                    @endphp
                                    <div class="text-xs text-gray-700 mb-2">
                                        <strong>TF-IDF:</strong> {{ round($tfidfScore) }}%
                                        <strong>LogReg:</strong> {{ round($logregScore) }}%
                                        <strong>RF:</strong> {{ round($matchScore) }}%
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <strong>Required Skills:</strong>
                                                <ul class="mb-0">
                                                    @foreach($opportunity->skills as $skill)
                                                        <li>{{ $skill->name }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <strong>Applicant Skills:</strong>
                                                <ul class="mb-0">
                                                    @foreach($applicantSkills as $skill)
                                                        <li>{{ $skill }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 p-3 bg-white border rounded shadow-sm">
                                        <h6 class="mb-2 text-primary">Explainable AI (XAI) - Why this match?</h6>
                                        <div class="mb-2">
                                            <strong>Matched Skills:</strong>
                                            @if(is_array($matchedSkills) && count($matchedSkills))
                                                <span class="text-success">{{ implode(', ', $matchedSkills) }}</span>
                                            @else
                                                <span class="text-danger">None</span>
                                            @endif
                                        </div>
                                        <div class="mb-2">
                                            <strong>Missing Skills:</strong>
                                            @if(is_array($missingSkills) && count($missingSkills))
                                                <span class="text-warning">{{ implode(', ', $missingSkills) }}</span>
                                            @else
                                                <span class="text-success">None</span>
                                            @endif
                                        </div>
                                        <div class="mb-2">
                                            <strong>Explanation:</strong> {{ $matchMessage }}
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-xs text-blue-700 mr-2">Course: <span class="font-semibold">{{ $student->course ?? ($application->student->course ?? 'N/A') }}</span></span>
                                            <span class="text-xs text-blue-700 mr-2">Academic Field: <span class="font-semibold">{{ $student->academic_field ?? ($application->student->academic_field ?? 'N/A') }}</span></span>
                                            <span class="text-xs text-purple-700 mr-2">GPA: <span class="font-semibold">{{ $student->gpa ?? ($application->student->gpa ?? 'N/A') }}</span></span>
                                            <span class="text-xs text-orange-700">Interests: <span class="font-semibold">{{ $student->interests ?? ($application->student->interests ?? 'N/A') }}</span></span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-xs text-blue-700 mr-2">Course: <span class="font-semibold">{{ $student->course ?? 'N/A' }}</span></span>
                                            <span class="text-xs text-blue-700 mr-2">Academic Field: <span class="font-semibold">{{ $student->academic_field ?? 'N/A' }}</span></span>
                                            <span class="text-xs text-purple-700 mr-2">GPA: <span class="font-semibold">{{ $student->gpa ?? 'N/A' }}</span></span>
                                            <span class="text-xs text-orange-700">Interests: <span class="font-semibold">{{ $student->interests ?? 'N/A' }}</span></span>
                                        </div>
                                    </div>
                                @else
                                    @php
                                        $applicantSkills = $student->skills->pluck('name')->toArray();
                                        $matchedSkills = $matcher->getMatchedSkills($requiredSkills, $applicantSkills);
                                        $missingSkills = $matcher->getMissingSkills($requiredSkills, $applicantSkills);
                                        $mlScores = $matcher->getMLScores(implode(' ', $applicantSkills), implode(' ', $requiredSkills));
                                        $matchScore = $mlScores['rf'] ?? 0;
                                        $tfidfScore = $mlScores['tfidf'] ?? 0;
                                        $logregScore = $mlScores['logreg'] ?? 0;
                                        $matchMessage = $matcher->getMatchMessage($matchScore, $matchedSkills, $missingSkills, $requiredSkills);
                                    @endphp
                                    <div class="text-xs text-gray-700 mb-2">
                                        <strong>TF-IDF:</strong> {{ round($tfidfScore) }}%
                                        <strong>LogReg:</strong> {{ round($logregScore) }}%
                                        <strong>RF:</strong> {{ round($matchScore) }}%
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <strong>Required Skills:</strong>
                                                <ul class="mb-0">
                                                    @foreach($opportunity->skills as $skill)
                                                        <li>{{ $skill->name }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <strong>Applicant Skills:</strong>
                                                <ul class="mb-0">
                                                    @foreach($applicantSkills as $skill)
                                                        <li>{{ $skill }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 p-3 bg-white border rounded shadow-sm">
                                        <h6 class="mb-2 text-primary">Explainable AI (XAI) - Why this match?</h6>
                                        <div class="mb-2">
                                            <strong>Matched Skills:</strong>
                                            @if(is_array($matchedSkills) && count($matchedSkills))
                                                <span class="text-success">{{ implode(', ', $matchedSkills) }}</span>
                                            @else
                                                <span class="text-danger">None</span>
                                            @endif
                                        </div>
                                        <div class="mb-2">
                                            <strong>Missing Skills:</strong>
                                            @if(is_array($missingSkills) && count($missingSkills))
                                                <span class="text-warning">{{ implode(', ', $missingSkills) }}</span>
                                            @else
                                                <span class="text-success">None</span>
                                            @endif
                                        </div>
                                        <div class="mb-2">
                                            <strong>Explanation:</strong> {{ $matchMessage }}
                                        </div>
                                    </div>
                                @endif
                            </section>
                        @endif

                        <footer class="mt-4 text-muted">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-user me-1"></i> {{ $opportunity->applications_count ?? 0 }} applications received</span>
                                <span>Posted {{ $opportunity->created_at->diffForHumans() }}</span>
                            </div>
                        </footer>

                        @if (auth()->check() && auth()->user()->id === $opportunity->creator->id)
                            <div class="mt-4 text-center">
                                <a href="{{ route('admin.opportunities.edit', $opportunity->id) }}" class="btn btn-warning">Edit</a>
                            </div>
                        @endif

                        @if (auth()->check() && auth()->user()->role === 'student')
                            @php
                                $hasApplied = auth()->user()->student->applications->contains('opportunity_id', $opportunity->id);
                            @endphp
                            <div class="mt-4 text-center">
                                @if ($hasApplied)
                                    <button class="btn btn-secondary" disabled>Already Applied</button>
                                @else
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyModal">Apply</button>
                                @endif
                            </div>

                            <!-- Application Modal -->
                            <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="applyModalLabel">Apply for Opportunity</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('student.applications.store') }}" method="POST" id="applyForm">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="opportunity_id" value="{{ $opportunity->id }}">
                                                <div class="mb-3">
                                                    <label for="coverLetter" class="form-label">Cover Letter (optional)</label>
                                                    <textarea class="form-control" id="coverLetter" name="cover_letter" rows="4" placeholder="Briefly explain why you are interested in this opportunity and how your skills match." >{{ auth()->user()->student->cover_letter ?? '' }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="interest" class="form-label">Interests (comma separated)</label>
                                                    <input type="text" class="form-control" id="interest" name="interest" value="{{ auth()->user()->student->interest ?? '' }}" placeholder="e.g. Software Development, Data Science, AI" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="gpa" class="form-label">Current GPA</label>
                                                    <input type="text" class="form-control" id="gpa" name="gpa" value="{{ auth()->user()->student->gpa ?? '' }}" placeholder="e.g. 3.8" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="resume" class="form-label">Resume</label>
                                                    <input type="file" class="form-control" id="resume" name="resume" placeholder="Upload your resume in PDF format">
                                                    @if(auth()->user()->student && auth()->user()->student->resume)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="useExistingResume" name="use_existing_resume" value="1" checked>
                                                            <label class="form-check-label" for="useExistingResume">
                                                                Use Existing Resume: <a href="{{ asset('storage/' . auth()->user()->student->resume) }}" target="_blank">View Resume</a>
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Submit Application</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </article>
            </div>
        </section>
    </main>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
        document.querySelector('#applyForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            try {
                const response = await fetch(this.action, {
                    method: this.method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                if (data.success) {
                    alert('Application submitted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error during form submission:', error);
                alert('An error occurred. Please try again.');
            }
        });
    </script>
</body>
</html>
