<div class="py-12" @if($campaign->status === 'running') wire:poll.3s @endif>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-slate-500">
                        <li>
                            <a href="{{ route('campaigns') }}" class="hover:text-indigo-600 transition-colors"
                                wire:navigate>Campanii</a>
                        </li>
                        <li>
                            <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                aria-hidden="true">
                                <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                            </svg>
                        </li>
                        <li class="font-medium text-slate-900 truncate">Statistici: {{ $campaign->name }}</li>
                    </ol>
                </nav>
                <h2 class="text-3xl font-bold text-slate-900 sm:truncate">
                    {{ $campaign->name }}
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-slate-500">
                        <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Creat la {{ $campaign->created_at->format('d.m.Y H:i') }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-slate-500">
                        <span @class([
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize',
                            'bg-gray-100 text-gray-800' => $campaign->status === 'pending',
                            'bg-blue-100 text-blue-800' => $campaign->status === 'running',
                            'bg-green-100 text-green-800' => $campaign->status === 'completed',
                            'bg-red-100 text-red-800' => $campaign->status === 'failed',
                        ])>
                            {{ $campaign->status }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-5 flex lg:ml-4 lg:mt-0">
                <a href="{{ route('campaigns') }}"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-all duration-200"
                    wire:navigate>
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Înapoi
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5 mb-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-100">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-50 rounded-lg p-2">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-slate-500 truncate">Total Destinatari</dt>
                                <dd class="text-2xl font-bold text-slate-900">{{ number_format($stats['total']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-100">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-50 rounded-lg p-2">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-slate-500 truncate">Trimise</dt>
                                <dd class="text-2xl font-bold text-slate-900">{{ number_format($stats['sent']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-100">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-amber-50 rounded-lg p-2">
                            <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-slate-500 truncate">Deschise</dt>
                                <dd class="text-2xl font-bold text-slate-900">{{ number_format($stats['opened']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-100">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-50 rounded-lg p-2">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-slate-500 truncate">Eșuate</dt>
                                <dd class="text-2xl font-bold text-slate-900">{{ number_format($stats['failed']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-100">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-50 rounded-lg p-2">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-slate-500 truncate">În așteptare</dt>
                                <dd class="text-2xl font-bold text-slate-900">{{ number_format($stats['pending']) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Table -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-slate-200">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <h3 class="text-lg font-semibold text-slate-800">Listă Destinatari</h3>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="text"
                                placeholder="Caută email sau nume..."
                                class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg leading-5 bg-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all duration-200">
                        </div>
                        <select wire:model.live="statusFilter"
                            class="block w-full sm:w-48 pl-3 pr-10 py-2 text-base border-slate-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg transition-all duration-200">
                            <option value="">Toate statusurile</option>
                            <option value="pending">În așteptare</option>
                            <option value="sent">Trimis</option>
                            <option value="failed">Eșuat</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Destinatar</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Trimis la</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Deschis la</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Informații</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($recipients as $recipient)
                            <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-bold text-slate-900">{{ $recipient->name ?? 'Fără nume' }}
                                        </div>
                                        <div class="text-sm text-slate-500">{{ $recipient->email }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span @class([
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize',
                                        'bg-gray-100 text-gray-800' => $recipient->status === 'pending',
                                        'bg-green-100 text-green-800' => $recipient->status === 'sent',
                                        'bg-red-100 text-red-800' => $recipient->status === 'failed',
                                    ])>
                                        {{ $recipient->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $recipient->sent_at ? $recipient->sent_at->format('d.m.Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    @if($recipient->opened_at)
                                        <div class="flex items-center text-indigo-600 font-medium">
                                            <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            {{ $recipient->opened_at->format('d.m.Y H:i') }}
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    @if($recipient->ip_address)
                                        <div class="flex flex-col text-xs space-y-0.5">
                                            <span class="text-slate-400">IP: {{ $recipient->ip_address }}</span>
                                            <span class="text-slate-400 truncate max-w-[150px]"
                                                title="{{ $recipient->user_agent }}">
                                                {{ Str::limit($recipient->user_agent, 20) }}
                                            </span>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 whitespace-nowrap text-center text-slate-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        Nu s-au găsit destinatari.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($recipients->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                    {{ $recipients->links() }}
                </div>
            @endif
        </div>
    </div>
</div>