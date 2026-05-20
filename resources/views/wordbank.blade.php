@extends('app')

@section('title', 'Your Saved Word Bank')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 text-slate-100">
    
    {{-- Top Heading Header Strip --}}
    <div class="mb-10 flex justify-between items-center pb-6 border-b border-white/5">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                <span>📚</span> Vocabulary Word Bank
            </h1>
            <p class="text-sm text-slate-400 mt-1">Your collected keywords organized neatly by reading material modules.</p>
        </div>
        <a href="{{ url('/library') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/10">
            ← Continue Reading
        </a>
    </div>

    {{-- Session Notifier Toast --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs rounded-xl flex items-center gap-2">
            <span>✨</span> {{ session('success') }}
        </div>
    @endif

    @if($savedWords->isEmpty())
        {{-- Clean Fallback Empty State --}}
        <div class="bg-slate-900 border border-white/10 rounded-2xl p-12 text-center max-w-xl mx-auto mt-12">
            <span class="text-4xl text-slate-500">🔍</span>
            <h3 class="text-lg font-bold text-slate-200 mt-4">Your Word Bank is Empty</h3>
            <p class="text-xs text-slate-400 mt-2 max-w-sm mx-auto leading-relaxed">
                Go to your library book collection, highlight any difficult terms inside the reading panes, and look them up to start tracking them here!
            </p>
        </div>
    @else
        {{-- Grouping Saved Words collection data cleanly by its module id reference point --}}
        @php 
            $groupedModules = $savedWords->groupBy('module_id')->sortKeys();
        @endphp

        <div class="space-y-4">
            @foreach($groupedModules as $moduleId => $words)
                {{-- Expandable Dropdown Container Component powered by Alpine.js --}}
                <div x-data="{ open: false }" class="bg-slate-900/60 border border-white/10 rounded-2xl overflow-hidden transition duration-150">
                    
                    {{-- Dropdown Trigger Header Banner --}}
                    <button type="button" @click="open = !open" class="w-full flex items-center justify-between p-5 text-left bg-slate-950/40 hover:bg-slate-900 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold text-sm">
                                #{{ $moduleId }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-200 group-hover:text-white transition-colors">
                                    Reading Module Materials
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Contains {{ $words->count() }} saved vocabulary {{ $words->count() === 1 ? 'term' : 'terms' }}
                                </p>
                            </div>
                        </div>

                        {{-- Dropdown Toggle Rotation Indicator Vector --}}
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] text-slate-500 group-hover:text-slate-400 transition-colors hidden sm:inline" x-text="open ? 'Click to collapse' : 'Click to expand'"></span>
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-200 transition-transform duration-200" 
                                 :class="{ 'rotate-180': open }" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    {{-- Dynamic Inner Tray Display containing the list items --}}
                    <div x-show="open" 
                         style="display: none;"
                         class="p-6 bg-slate-950/20 border-t border-white/5">
                        
                        {{-- Structured Inner Cards List Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($words as $item)
                                <div class="bg-slate-900 border border-white/5 hover:border-indigo-500/20 rounded-xl p-5 flex flex-col justify-between shadow-sm transition">
                                    <div>
                                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/5">
                                            <span class="text-sm font-bold text-indigo-400 capitalize">✨ {{ $item->word }}</span>
                                        </div>
                                        <p class="text-xs text-slate-300 leading-relaxed italic bg-black/10 p-2.5 rounded-lg border border-white/5">
                                            "{{ $item->definition }}"
                                        </p>
                                    </div>
                                    
                                    {{-- Footer Tray Details and Deletion Pipeline --}}
                                    <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between text-[10px] text-slate-500">
                                        <span>Saved: {{ $item->created_at->format('M d, Y') }}</span>
                                        <div class="flex items-center gap-3">
                                            <a href="{{ url('/quiz?module_id=' . $item->module_id) }}" class="text-indigo-400 hover:underline font-semibold">
                                                Practice Quiz
                                            </a>
                                            <form action="{{ route('words.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this word?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-500 font-medium transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection