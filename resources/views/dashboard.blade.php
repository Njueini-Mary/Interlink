<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @role('admin') {{ __('Admin Dashboard') }}
            @elserole('client') {{ __('Client Dashboard') }}
            @elserole('freelancer') {{ __('Freelancer Dashboard') }}
            @else {{ __('Dashboard') }} @endrole
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Role-specific Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                @role('freelancer')
                <!-- Freelancer Stats -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="p-6">

                        <div class="text-sm font-medium text-gray-500">Active Projects</div>
                        <div class="text-3xl font-bold text-indigo-600 mt-2">{{ $activeProjects ?? 5 }}</div>
                        <div class="text-sm text-gray-500 mt-2">{{ $projectsChange ?? '+2' }} from last month</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <div class="text-sm font-medium text-gray-500">Total Earnings</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">KSHS{{ $totalEarnings ?? '4,250' }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">My Rating</div>
                        <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $rating ?? '4.8' }}/5</div>
                        <div class="flex mt-2">
                            @for ($i = 0; $i < 5; $i++) @if ($i < floor($rating ?? 4.8)) <svg
                                class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                                </svg>
                                @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                @endif
                                @endfor
                        </div>
                    </div>
                </div>
                @endrole

                @role('client')
                <!-- Client Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Open Jobs</div>
                        <div class="text-3xl font-bold text-blue-600 mt-2">{{ $activeProjects }}</div>
                        <div class="text-sm text-gray-500 mt-2">{{ $hoursChange ?? '-4' }} from last month</div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="p-6">

                        <div class="text-sm font-medium text-gray-500">Total Spent</div>
                        <div class="text-3xl font-bold text-yellow-600 mt-2">KSHS{{ number_format($totalSpent) }}
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Client Rating</div>
                        <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $rating ?? '4.8' }}/5</div>
                        <div class="flex mt-2">
                            @for ($i = 0; $i < 5; $i++) @if ($i < floor($rating ?? 4.8)) <svg
                                class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                                </svg>
                                @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                @endif
                                @endfor
                        </div>
                    </div>
                </div>
                @endrole

                @role('admin')
                <!-- Admin Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <div class="text-sm font-medium text-gray-500">Total Users</div>
                        <div class="text-3xl font-bold text-blue-600 mt-2">{{ $totalUsers }}</div>
                        <div class="text-sm text-gray-500 mt-2">{{ $hoursChange ?? '-4' }} from last month</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="text-sm font-medium text-gray-500">Active Projects</div>
                            <div class="text-3xl font-bold text-blue-600 mt-2">{{ $activeProjects }}</div>
                        </div>
                    </div>
                </div>
                @endrole

                <!-- Common Stats -->
                @role('freelancer')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Hours Logged</div>
                        <div class="text-3xl font-bold text-blue-600">{{ $hoursLogged }}</div>
                    </div>
                </div>
                @endrole
            </div>

            <!-- Role-specific Content -->
            @role('freelancer')
            <!-- Freelancer Projects -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Active Projects</h3>
                        @foreach($projects as $project)
                        <div class="border rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium">{{ $project->title }}</h4>
                                    <p class="text-sm text-gray-500">
                                        Client: {{ $project->client->user->name }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $project->status_badge }}">
                                    {{ str_replace('_', ' ', $project->status) }}
                                </span>
                            </div>
                            <!-- Progress bar -->
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endrole

            @role('client')
            <!-- Client Jobs -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Recent Jobs</h3>
                        @foreach($jobs as $job)
                        <div class="border rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium">{{ $job->title }}</h4>
                                    <p class="text-sm text-gray-500">
                                        Category: {{ $job->category->name }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $job->status_badge }}">
                                    {{ $job->status }}
                                </span>
                            </div>
                            <!-- Job details -->
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endrole

            @role('admin')
            <!-- Admin Overview -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
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
            <!-- Main Content Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Current Projects -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Current Projects</h3>
                                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>
                            </div>
                            <div class="space-y-4">
                                @foreach ($projects ?? [] as $project)
                                <!-- Project Item -->
                                @endforeach
                                <!-- Example Project Items (if no projects provided) -->
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">E-commerce Website Redesign</h4>
                                            <p class="text-sm text-gray-500 mt-1">Client: Deos Corporation</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">In
                                            Progress</span>
                                    </div>
                                    <div class="mt-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Progress</span>
                                            <span class="text-gray-900">68%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: 68%"></div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-between text-sm">
                                        <span class="text-gray-500">Deadline: April 15, 2025</span>
                                        <span class="text-gray-500">Budget: Ksh2,800,970</span>
                                    </div>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">Mobile App Development</h4>
                                            <p class="text-sm text-gray-500 mt-1">Client: TenCent Inc.</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">On
                                            Hold</span>
                                    </div>
                                    <div class="mt-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Progress</span>
                                            <span class="text-gray-900">35%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 35%"></div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-between text-sm">
                                        <span class="text-gray-500">Deadline: May 30, 2025</span>
                                        <span class="text-gray-500">Budget: Ksh556,000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tasks Section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Upcoming Tasks</h3>
                                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>
                            </div>
                            <div class="space-y-3">
                                @foreach ($tasks ?? [] as $task)
                                <!-- Task Items -->
                                @endforeach
                                <!-- Example Task Items -->
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="mr-3 h-4 w-4 rounded border-gray-300 text-indigo-600">
                                        <div>
                                            <p class="font-medium text-gray-900">Finalize homepage design</p>
                                            <p class="text-sm text-gray-500">E-commerce Website Project</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500">Due Apr 5</span>
                                </div>
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="mr-3 h-4 w-4 rounded border-gray-300 text-indigo-600">
                                        <div>
                                            <p class="font-medium text-gray-900">Client meeting</p>
                                            <p class="text-sm text-gray-500">High Hill App Project</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500">Apr 6, 2pm</span>
                                </div>
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="mr-3 h-4 w-4 rounded border-gray-300 text-indigo-600" checked>
                                        <div>
                                            <p class="font-medium text-gray-900 line-through">Send invoice</p>
                                            <p class="text-sm text-gray-500">TechStar Logo Project</p>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500">Completed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @role('freelancer')
                <!-- Sidebar Content -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Earnings Chart -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Earnings</h3>
                            <div class="h-64 flex items-end space-x-2">
                                <div class="w-1/6 bg-indigo-200 rounded-t-md h-20" title="Jan: Ksh80000"></div>
                                <div class="w-1/6 bg-indigo-300 rounded-t-md h-32" title="Feb: Ksh1,20000"></div>
                                <div class="w-1/6 bg-indigo-400 rounded-t-md h-24" title="Mar:Ksh950"></div>
                                <div class="w-1/6 bg-indigo-500 rounded-t-md h-48" title="Apr: Ksh100,850"></div>
                                <div class="w-1/6 bg-indigo-600 rounded-t-md h-40" title="May: Ksh100,600"></div>
                                <div class="w-1/6 bg-indigo-700 rounded-t-md h-56" title="Jun: Ksh200,100"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <span>Jan</span>
                                <span>Feb</span>
                                <span>Mar</span>
                                <span>Apr</span>
                                <span>May</span>
                                <span>Jun</span>
                            </div>
                        </div>
                    </div>
                    <!-- Upcoming Payments -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Payments</h3>
                            <div class="space-y-3">
                                <div class="p-3 border border-gray-200 rounded-lg">
                                    <div class="flex justify-between">
                                        <span class="font-medium">Acme Corporation</span>
                                        <span class="text-green-600 font-medium">Ksh1,20000</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-500 mt-2">
                                        <span>Invoice #2023-04</span>
                                        <span>Due in 5 days</span>
                                    </div>
                                </div>
                                <div class="p-3 border border-gray-200 rounded-lg">
                                    <div class="flex justify-between">
                                        <span class="font-medium">TechStart Inc.</span>
                                        <span class="text-green-600 font-medium">Ksh85000</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-500 mt-2">
                                        <span>Invoice #2023-05</span>
                                        <span>Due in 12 days</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Recent Messages -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Messages</h3>
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex-shrink-0"></div>
                                    <div>
                                        <div class="flex items-baseline">
                                            <h4 class="font-medium">John Mwangi</h4>
                                            <span class="ml-2 text-xs text-gray-500">2 hours ago</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Just reviewed your latest designs. Looking
                                            good! Let's schedule a call tomorrow to discuss next steps.</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex-shrink-0"></div>
                                    <div>
                                        <div class="flex items-baseline">
                                            <h4 class="font-medium">Sarah Ogutu</h4>
                                            <span class="ml-2 text-xs text-gray-500">Yesterday</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Could you send me an update on the mobile
                                            app project? The client is asking for a progress report.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-900">View All Messages</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>
</x-app-layout>