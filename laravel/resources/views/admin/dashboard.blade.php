<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Total Users -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Total Users</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</div>
                        <div class="mt-1 text-sm text-gray-500">{{ number_format($stats['admin_users']) }} staff / {{ number_format($stats['customers']) }} customers</div>
                    </div>
                </div>

                <!-- Total Companies -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Companies</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_companies']) }}</div>
                    </div>
                </div>

                <!-- Sample Requests -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Sample Requests</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_requests']) }}</div>
                        <div class="mt-1 text-sm text-yellow-600">{{ number_format($stats['pending_requests']) }} pending</div>
                    </div>
                </div>

                <!-- Jobs/Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Jobs/Orders</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_jobs']) }}</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Sample Requests -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Sample Requests</h3>
                        @if($recentRequests->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($recentRequests as $request)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">#{{ $request->id }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                    {{ $request->user?->full_name ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-500">
                                                    {{ $request->request_date?->format('M d, Y') ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-2 text-sm">
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $request->isPending() ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ $request->status_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">No sample requests found.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Jobs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Jobs/Orders</h3>
                        @if($recentJobs->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Job ID</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($recentJobs as $job)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $job->job_id ?? '#' . $job->id }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                    {{ $job->company?->company ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                    ${{ number_format($job->order_total ?? 0, 2) }}
                                                </td>
                                                <td class="px-4 py-2 text-sm {{ $job->balance_due > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    ${{ number_format($job->balance_due ?? 0, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">No jobs found.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Current User Info -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Logged In As</h3>
                    <div class="text-sm text-gray-600">
                        <p><strong>Name:</strong> {{ auth()->user()->full_name }}</p>
                        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                        <p><strong>Role:</strong> {{ auth()->user()->isAdmin() ? 'Administrator' : 'Staff' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
