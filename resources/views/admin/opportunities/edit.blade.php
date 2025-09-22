@extends('layouts.dashboard')

@section('content')

<!-- Modernized Opportunity Edit View with Gradient Background -->
<div class="max-w-6xl mx-auto py-10 px-4 bg-gradient-to-r from-indigo-50 via-white to-indigo-100">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5A1 1 0 017.707 4.293L3.414 8.586H18a1 1 0 110 2H3.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                Back
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Edit Opportunity</h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.opportunities.show', $opportunity->id) }}" class="text-sm px-3 py-2 rounded-md border border-gray-200 hover:bg-gray-50">View</a>
            <form action="{{ route('admin.opportunities.destroy', $opportunity->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this opportunity?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm px-3 py-2 rounded-md bg-red-50 text-red-700 border border-red-100 hover:bg-red-100">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6">
            <div class="rounded-lg bg-green-50 border border-green-100 p-4 text-green-800">{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6">
            <div class="bg-red-50 border border-red-100 rounded-lg p-4">
                <strong class="block text-red-700 mb-2">Please fix the following errors:</strong>
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form column -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.opportunities.update', $opportunity->id) }}" enctype="multipart/form-data" class="space-y-6" x-data="opportunityForm()">
                @csrf
                @method('PUT')

                <!-- Title & Type -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input id="title" name="title" x-model="title" value="{{ old('title', $opportunity->title) }}" placeholder="e.g. Frontend Intern (React)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" />
                        @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select id="type" name="type" x-model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            <option value="WBL">WBL</option>
                            <option value="SBL">SBL</option>
                        </select>
                        @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Organization & Location -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="organization_id" class="block text-sm font-medium text-gray-700">Organization</label>
                        <select id="organization_id" name="organization_id" x-model="organization_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}" @if(old('organization_id', $opportunity->organization_id) == $org->id) selected @endif>{{ $org->name }}</option>
                            @endforeach
                        </select>
                        @error('organization_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input id="location" name="location" x-model="location" value="{{ old('location', $opportunity->location) }}" placeholder="City, country or Remote" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" />
                        @error('location')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Skills multi-select (native) + chip preview -->
                <div>
                    <label for="skills" class="block text-sm font-medium text-gray-700">Skills</label>
                    <select id="skills" name="skills[]" multiple x-ref="skillsSelect" @change="syncSkills()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 h-32">
                        @foreach($skills as $skill)
                            <option value="{{ $skill->id }}" @if($opportunity->skills->contains($skill->id)) selected @endif>{{ $skill->name }}</option>
                        @endforeach
                    </select>
                    <div class="mt-2 flex flex-wrap gap-2" aria-hidden="true">
                        <template x-for="(id, index) in selectedSkills" :key="id">
                            <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-sm font-medium" x-text="skillMap[id] ?? '—'">Skill</span>
                        </template>
                    </div>
                    @error('skills')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" x-model="description" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" placeholder="Describe responsibilities, deliverables, and what success looks like">{{ old('description', $opportunity->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Application deadline & stipend (optional) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700">Application Deadline</label>
                        <input id="deadline" name="deadline" type="date" value="{{ old('deadline', optional($opportunity->deadline)->format('Y-m-d')) }}" x-model="deadline" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" />
                        @error('deadline')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="stipend" class="block text-sm font-medium text-gray-700">Stipend / Salary</label>
                        <input id="stipend" name="stipend" x-model="stipend" value="{{ old('stipend', $opportunity->stipend) }}" placeholder="e.g. $300/month or Negotiable" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" />
                        @error('stipend')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- File upload (optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Attachment (optional)</label>
                    <input type="file" name="attachment" class="mt-1 block w-full text-sm text-gray-600" />
                    @error('attachment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.opportunities.index') }}" class="px-4 py-2 border rounded-md text-sm">Cancel</a>
                    <button type="submit" class="px-6 py-2 rounded-md bg-indigo-600 text-white shadow hover:bg-indigo-700">Update Opportunity</button>
                </div>
            </form>
        </div>

        <!-- Preview column -->
        <aside class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Live Preview</h3>

            <div class="border border-gray-100 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center text-gray-400">
                            {{-- organization logo placeholder --}}
                            <img src="{{ $opportunity->organization->logo ? asset('storage/' . $opportunity->organization->logo) : asset('images/default-organization.png') }}" alt="logo" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="text-md font-semibold text-gray-800" x-text="title || '{{ $opportunity->title }}'"></h4>
                            <p class="text-sm text-gray-500" x-text="organizationName()"></p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm bg-indigo-50 text-indigo-700" x-text="type"></span>
                </div>

                <p class="text-sm text-gray-700 mb-3" x-text="descriptionPreview()"></p>

                <div class="flex flex-wrap gap-2">
                    <template x-for="(id, idx) in selectedSkills" :key="id">
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700" x-text="skillMap[id]"></span>
                    </template>
                </div>

                <div class="mt-4 text-sm text-gray-500">
                    <strong>Location:</strong> <span x-text="location || '{{ $opportunity->location ?? 'Remote' }}'"></span>
                </div>

                <div class="mt-2 text-sm text-gray-500">
                    <strong>Deadline:</strong> <span x-text="deadline || '{{ optional($opportunity->deadline)->toDateString() ?? '—' }}'"></span>
                </div>
            </div>
        </aside>
    </div>
</div>

@push('scripts')
    <!-- Alpine for small reactive preview & syncing native select -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function opportunityForm() {
            return {
                title: @json(old('title', $opportunity->title)),
                type: @json(old('type', $opportunity->type)),
                organization_id: @json(old('organization_id', $opportunity->organization_id)),
                location: @json(old('location', $opportunity->location)),
                description: @json(old('description', $opportunity->description)),
                stipend: @json(old('stipend', $opportunity->stipend)),
                deadline: @json(old('deadline', optional($opportunity->deadline)->format('Y-m-d'))),

                // build a quick map of skillId => name for preview
                skillMap: @json($skills->mapWithKeys(function($s){ return [$s->id => $s->name]; })),

                // initialize selected skills from server (old or current)
                selectedSkills: @json(old('skills', $opportunity->skills->pluck('id'))),

                syncSkills() {
                    // sync native select to selectedSkills array
                    const opts = Array.from(this.$refs.skillsSelect.selectedOptions).map(o => o.value);
                    this.selectedSkills = opts;
                },

                organizationName() {
                    // find selected org name from blade-provided list
                    const map = @json($organizations->pluck('name','id'));
                    return map[this.organization_id] ?? '{{ $opportunity->organization->name }}';
                },

                descriptionPreview() {
                    if (!this.description) return 'No description yet.';
                    return this.description.length > 240 ? this.description.slice(0,240) + '…' : this.description;
                }
            }
        }
    </script>
@endpush

@endsection
