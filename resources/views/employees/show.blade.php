<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ $employee->employee_name }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">
                    Employee #{{ $employee->id }} | {{ $employee->position }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('employees.edit', $employee->id) }}" class="btn-sage">Edit Employee</a>
                <a href="{{ route('employees.index') }}" class="btn-sienna">Back to Employees</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <x-stat-card title="Access Level" :value="$employee->user ? ucfirst($employee->user->role) : 'No Access'" icon="Role" border="sienna" />
            <x-stat-card title="Status" :value="$employee->status ?? 'Active'" icon="State" :border="($employee->status ?? 'Active') === 'Active' ? 'sage' : 'cream'" />
            <x-stat-card title="Sales Handled" :value="$activitySummary['sales_count']" icon="Sale" border="terracotta" />
            <x-stat-card title="Approvals" :value="$activitySummary['approvals_count']" icon="OK" border="sage" />
        </div>

        <div class="card-rustic border-sienna">
            <h3 class="text-2xl font-lora font-bold text-sienna mb-6">Employee Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Full Name</p>
                    <p class="mt-1 font-bold text-sienna">{{ $employee->employee_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Email Address</p>
                    <p class="mt-1 text-sienna">{{ $employee->employee_email }}</p>
                </div>
                <div>
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Phone Number</p>
                    <p class="mt-1 text-sienna">{{ $employee->employee_phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Date Hired</p>
                    <p class="mt-1 text-sienna">{{ $employee->date_hired?->format('M d, Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Position/Role</p>
                    <p class="mt-1 text-sienna">{{ $employee->position }}</p>
                </div>
                <div>
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Emergency Contact</p>
                    <p class="mt-1 text-sienna">{{ $employee->emergency_contact ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="card-rustic border-terracotta">
                <h3 class="text-2xl font-lora font-bold text-sienna mb-6">Activity Summary</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-cream bg-opacity-40 rounded-lg p-4">
                        <p class="text-xs font-black text-sage uppercase tracking-widest">Sales Total</p>
                        <p class="mt-1 text-2xl font-bold text-terracotta">PHP {{ number_format($activitySummary['sales_total'], 2) }}</p>
                    </div>
                    <div class="bg-cream bg-opacity-40 rounded-lg p-4">
                        <p class="text-xs font-black text-sage uppercase tracking-widest">Orders Handled</p>
                        <p class="mt-1 text-2xl font-bold text-sienna">{{ $activitySummary['orders_count'] }}</p>
                    </div>
                    <div class="bg-cream bg-opacity-40 rounded-lg p-4">
                        <p class="text-xs font-black text-sage uppercase tracking-widest">Purchases</p>
                        <p class="mt-1 text-2xl font-bold text-sienna">{{ $activitySummary['purchases_count'] }}</p>
                    </div>
                    <div class="bg-cream bg-opacity-40 rounded-lg p-4">
                        <p class="text-xs font-black text-sage uppercase tracking-widest">Created Records</p>
                        <p class="mt-1 text-2xl font-bold text-sienna">{{ $activitySummary['created_records_count'] }}</p>
                    </div>
                    <div class="bg-cream bg-opacity-40 rounded-lg p-4 sm:col-span-2">
                        <p class="text-xs font-black text-sage uppercase tracking-widest">Inventory Movements</p>
                        <p class="mt-1 text-2xl font-bold text-sienna">{{ $activitySummary['inventory_movements_count'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card-rustic border-sage">
                <h3 class="text-2xl font-lora font-bold text-sienna mb-6">Login History</h3>
                <div class="space-y-3">
                    @forelse($loginHistory as $session)
                        <div class="border border-sienna border-opacity-10 rounded-lg p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-bold text-sienna">{{ $session->ip_address ?? 'Unknown IP' }}</p>
                                    <p class="text-xs text-sage">{{ $session->user_agent ? \Illuminate\Support\Str::limit($session->user_agent, 80) : 'Unknown device' }}</p>
                                </div>
                                <p class="text-xs font-bold text-sienna whitespace-nowrap">
                                    {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->format('M d, Y h:i A') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic">No active session history available.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card-rustic border-sienna">
            <h3 class="text-2xl font-lora font-bold text-sienna mb-6">Transactions Handled</h3>
            <div class="overflow-x-auto rounded-xl border border-sienna border-opacity-10">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest">
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Type</th>
                            <th class="px-6 py-4 text-left">Reference</th>
                            <th class="px-6 py-4 text-left">Details</th>
                            <th class="px-6 py-4 text-right">Amount</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($recentTransactions as $transaction)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-4 text-sienna">{{ $transaction['date']?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-6 py-4 font-bold text-sienna">{{ $transaction['type'] }}</td>
                                <td class="px-6 py-4 text-sage">{{ $transaction['reference'] }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $transaction['details'] }}</td>
                                <td class="px-6 py-4 text-right font-bold text-terracotta">
                                    {{ $transaction['amount'] !== null ? 'PHP ' . number_format($transaction['amount'], 2) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-cream text-sienna px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        {{ $transaction['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">No handled transactions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
