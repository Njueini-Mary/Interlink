@extends('layouts.dashboard')
@section('content')

<!-- Users Metrics -->
<div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
        <span class="block text-lg font-bold text-blue-700">{{ $users->count() }}</span>
        <span class="text-xs text-gray-500">Total Users</span>
    </div>
    <div class="bg-green-50 rounded-lg px-4 py-2 text-center">
        <span class="block text-lg font-bold text-green-700">{{ $users->where('role', 'admin')->count() }}</span>
        <span class="text-xs text-gray-500">Admins</span>
    </div>
    <div class="bg-indigo-50 rounded-lg px-4 py-2 text-center">
        <span class="block text-lg font-bold text-indigo-700">{{ $users->where('role', 'student')->count() }}</span>
        <span class="text-xs text-gray-500">Students</span>
    </div>
    <div class="bg-yellow-50 rounded-lg px-4 py-2 text-center">
        <span class="block text-lg font-bold text-yellow-700">{{ $users->where('role', 'organization')->count() }}</span>
        <span class="text-xs text-gray-500">Organizations</span>
    </div>
</div>

<!-- Live Search & Filter -->
<div class="mb-6 flex flex-col md:flex-row md:items-center gap-4">
    <input id="user-search" type="text" placeholder="Search by name, email, or role..." class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500" />
    <select id="user-role" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">All Roles</option>
        <option value="admin">Admin</option>
        <option value="student">Student</option>
        <option value="organization">Organization</option>
    </select>
</div>

<script>
// Simple live filter for demo (client-side)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('user-search');
    const roleSelect = document.getElementById('user-role');
    const rows = document.querySelectorAll('tbody tr');
    function filterRows() {
        const search = searchInput.value.toLowerCase();
        const role = roleSelect.value;
        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const rowRole = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            let show = true;
            if (search && !(name.includes(search) || email.includes(search))) show = false;
            if (role && rowRole !== role) show = false;
            row.style.display = show ? '' : 'none';
        });
    }
    searchInput.addEventListener('input', filterRows);
    roleSelect.addEventListener('change', filterRows);
});
</script>

<!-- Users Table - Modern Card Layout -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4 text-indigo-700">All Users</h2>
    <table class="table-auto w-full">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left">#</th>
                <th class="px-4 py-2 text-left">Name</th>
                <th class="px-4 py-2 text-left">Email</th>
                <th class="px-4 py-2 text-left">Role</th>
                <th class="px-4 py-2 text-left">Joined</th>
                <th class="px-4 py-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $i => $user)
            <tr class="border-b">
                <td class="px-4 py-2">{{ ($users->currentPage() - 1) * $users->perPage() + $i + 1 }}</td>
                <td class="px-4 py-2">{{ $user->name }}</td>
                <td class="px-4 py-2">{{ $user->email }}</td>
                <td class="px-4 py-2">{{ ucfirst($user->role) }}</td>
                <td class="px-4 py-2">{{ $user->created_at->diffForHumans() }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-indigo-600 hover:underline">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Pagination -->
    <div class="mt-6 flex justify-center">
        {{ $users->links() }}
    </div>
</div>

<!-- Existing Toast Notification Code -->
<style>
.toast-container {
    z-index: 2147483647; /* Set to the maximum possible value */
    position: fixed; /* Ensure it is positioned relative to the viewport */
    top: 1rem; /* Place it at the top of the viewport */
    right: 1rem; /* Align it to the right */
}
</style>

<!-- Existing Toast Notification Code -->
{{-- End of Users Index View --}}
@endsection
