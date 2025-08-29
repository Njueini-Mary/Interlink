<div class="w-64 bg-gray-800 text-white min-h-screen">
    <div class="p-4">
        <h2 class="text-2xl font-bold">Client Dashboard</h2>
    </div>
    <ul class="space-y-4 mt-6">
        <li>
            <a href="{{ route('client.profile.update') }}" class="block py-2 px-4 text-gray-300 hover:bg-gray-700 rounded">
                Update Profile
            </a>
        </li>
        <li>
            {{-- <a href="{{ route('client.post.work') }}" class="block py-2 px-4 text-gray-300 hover:bg-gray-700 rounded">
                Post Work
            </a> --}}
        </li>
        <li>
            {{-- <a href="{{ route('client.find.freelancers') }}" class="block py-2 px-4 text-gray-300 hover:bg-gray-700 rounded">
                Find Freelancers
            </a> --}}
        </li>
    </ul>
</div>
