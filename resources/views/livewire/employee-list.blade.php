<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 dark:from-blue-400 dark:to-indigo-300 tracking-tight font-heading">
                {{ __('employees.title') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5 font-medium">{{ __('employees.list_desc') }}</p>
        </div>
        <a href="{{ route('employees.create') }}" class="inline-flex items-center justify-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2.5 rounded-2xl font-bold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-1 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 border border-white/10 relative overflow-hidden group">
            <svg class="w-5 h-5 me-2 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="relative z-10">{{ __('employees.add') ?? 'Add Employee' }}</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl shadow-premium dark:shadow-premium-dark border border-slate-200/50 dark:border-slate-800/60 p-6 relative overflow-hidden group hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50/20 dark:bg-blue-500/5 rounded-full blur-3xl -mr-16 -mt-16 z-0 pointer-events-none"></div>
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('common.search') ?? 'Search' }}</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="searchName" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all dark:text-slate-100 shadow-inner dark:shadow-none font-medium" placeholder="{{ __('employees.search_name') ?? 'Search by name...' }}">
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">{{ __('employees.status') ?? 'Status' }}</label>
                <div class="relative">
                    <select wire:model.live="searchStatus" class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all dark:text-slate-100 appearance-none shadow-inner dark:shadow-none font-semibold">
                        <option value="">{{ __('common.all') ?? 'All' }}</option>
                        <option value="active">{{ __('employees.active') ?? 'Active' }}</option>
                        <option value="inactive">{{ __('employees.inactive') ?? 'Inactive' }}</option>
                    </select>
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg class="w-4 h-4 text-slate-400 absolute right-4 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Glassmorphic Employee Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative z-10">
        @forelse($employees as $employee)
            <div class="relative bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/60 shadow-premium dark:shadow-premium-dark rounded-3xl p-6 transition-all duration-300 hover:shadow-premium-hover dark:hover:shadow-premium-dark-hover hover:-translate-y-1 group">
                <div class="absolute top-4 {{ session('locale', 'ar') === 'ar' ? 'left-4' : 'right-4' }}">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $employee->status === 'active' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100/30 dark:border-emerald-800/30' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-100/30 dark:border-rose-800/30' }}">
                        {{ $employee->status === 'active' ? (__('employees.active') ?? 'Active') : (__('employees.inactive') ?? 'Inactive') }}
                    </span>
                </div>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-extrabold text-lg shadow-md shadow-blue-500/20">
                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">{{ $employee->name }}</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-400 mt-1 font-semibold">{{ $employee->role_title ?? '---' }}</p>
                    </div>
                </div>

                <div class="space-y-4 mb-6 border-t border-slate-100 dark:border-slate-800/50 pt-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 dark:text-slate-500 font-medium">{{ __('employees.base_salary') ?? 'Base Salary' }}</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ formatMoney($employee->base_salary) }} <span class="text-xs text-slate-400 dark:text-slate-500 font-semibold ms-1">{{ getCurrency() }}</span></span>
                    </div>
                    
                    @php 
                        $advancesTotal = $employee->active_advances_total; 
                        $salary = (float) $employee->base_salary;
                        $percentage = $salary > 0 ? min(100, ($advancesTotal / $salary) * 100) : 0;
                    @endphp
                    
                    <div class="pt-2">
                        <div class="flex justify-between items-center text-xs mb-1.5 font-semibold">
                            <span class="text-amber-600 dark:text-amber-400">{{ __('employees.active_advances') ?? 'Advances' }}</span>
                            <span class="text-amber-600 dark:text-amber-400 font-bold">{{ formatMoney($advancesTotal) }} {{ getCurrency() }}</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 shadow-inner">
                            <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-1.5 text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">
                            <span>{{ number_format($percentage, 0) }}% {{ __('employees.of_salary') ?? 'of base salary' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('employees.edit', $employee->id) }}" class="flex-1 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/50 dark:border-slate-700/50 text-center py-2.5 rounded-xl text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors shadow-sm">
                        {{ __('common.manage') ?? 'Manage' }}
                    </a>
                    <button wire:click="delete({{ $employee->id }})" wire:confirm="{{ __('common.confirm_delete') ?? 'Are you sure?' }}" class="p-2.5 bg-slate-50 dark:bg-slate-800 border border-rose-100/50 dark:border-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/20 hover:text-rose-700 transition shadow-sm cursor-pointer" title="{{ __('common.delete') ?? 'Delete' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl p-16 text-center border border-dashed border-slate-200 dark:border-slate-800 shadow-premium dark:shadow-premium-dark">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-950/50 rounded-full flex items-center justify-center mb-4 border border-slate-200/50 dark:border-slate-800/60 mx-auto">
                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-bold text-lg">{{ __('employees.no_employees') ?? 'No employees found.' }}</p>
            </div>
        @endforelse
    </div>

    @if($employees->hasPages())
        <div class="mt-6 p-4 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-slate-200/50 dark:border-slate-800/60 shadow-premium dark:shadow-premium-dark">
            {{ $employees->links() }}
        </div>
    @endif
</div>

