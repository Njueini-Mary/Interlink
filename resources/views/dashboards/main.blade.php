<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @php $role = Auth::user()->role ?? null; @endphp
            @if($role === 'admin')
                {{ __('Admin Dashboard') }}
            @elseif($role === 'organization')
                {{ __('Organization Dashboard') }}
            @elseif($role === 'student')
                {{ __('Student Dashboard') }}
            @else
                {{ __('Dashboard') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Role-specific Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                @php $role = Auth::user()->role ?? null; @endphp
                @if($role === 'student')
                    <!-- Student Stats -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Active Applications</div>
                            <div class="text-3xl font-bold text-indigo-600 mt-2">{{ $activeApplications ?? 2 }}</div>
                            <div class="text-sm text-gray-500 mt-2">{{ $applicationsChange ?? '+1' }} from last month</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Placements Secured</div>
                            <div class="text-3xl font-bold text-green-600 mt-2">{{ $placementsSecured ?? 1 }}</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Profile Completion</div>
                            <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $profileCompletion ?? '80' }}%</div>
                        </div>
                    </div>
                @elseif($role === 'organization')
                    <!-- Organization Stats -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Open Opportunities</div>
                            <div class="text-3xl font-bold text-blue-600 mt-2">{{ $openOpportunities ?? 3 }}</div>
                            <div class="text-sm text-gray-500 mt-2">{{ $opportunitiesChange ?? '+1' }} from last month</div>
                        </div>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Placements</div>
                            <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $totalPlacements ?? 5 }}</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Organization Rating</div>
                            <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $rating ?? '4.8' }}/5</div>
                        </div>
                    </div>
                @elseif($role === 'admin')
                    <!-- Admin Stats -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Users</div>
                            <div class="text-3xl font-bold text-blue-600 mt-2">{{ $totalUsers }}</div>
                            <div class="text-sm text-gray-500 mt-2">{{ $usersChange ?? '+10' }} from last month</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Active Opportunities</div>
                            <div class="text-3xl font-bold text-blue-600 mt-2">{{ $activeOpportunities }}</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Role-specific Content -->
            @if($role === 'student')
            <!-- Student Applications -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">My Applications</h3>
                @foreach($applications as $application)
                <div class="border rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium">{{ $application->opportunity->title }}</h4>
                            <p class="text-sm text-gray-500">
                                Organization: {{ $application->opportunity->organization->name }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $application->status == 'accepted' ? 'green' : ($application->status == 'pending' ? 'yellow' : 'red') }}-100 text-{{ $application->status == 'accepted' ? 'green' : ($application->status == 'pending' ? 'yellow' : 'red') }}-800">
                            {{ ucfirst($application->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @endrole

            @role('organization')
            <!-- Organization Opportunities -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Recent Opportunities</h3>
                @foreach($opportunities as $opportunity)
                <div class="border rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium">{{ $opportunity->title }}</h4>
                            <p class="text-sm text-gray-500">
                                Applicants: {{ $opportunity->applications_count ?? 0 }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $opportunity->status == 'open' ? 'green' : 'gray' }}-100 text-{{ $opportunity->status == 'open' ? 'green' : 'gray' }}-800">
                            {{ ucfirst($opportunity->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @endrole

            @role('admin')
            <!-- Admin Overview -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Recent Users</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recentUsers as $user)
                    <div class="border rounded-lg p-4">
                        <div class="font-medium">{{ $user->name }}</div>
                        <div class="text-sm font-medium text-gray-500">
                            {{ $user->roles->pluck('name')->join(', ') }}
                        </div>
                        <div class="text-sm font-medium text-gray-500">
                            Joined: {{ $user->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endrole
        </div>
    </div>
</x-app-layout>
