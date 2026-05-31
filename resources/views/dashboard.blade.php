@extends('app')

@section('title', 'Vocabulary Word Bank')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 text-slate-100">
    
    {{-- Header Layout Section --}}
    <div class="mb-8 pb-6 border-b border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>🗂️</span> Personal Word Bank
            </h1>
            <p class="text-sm text-slate-400 mt-1">Review your saved vocabulary terms grouped neatly by assignment modules.</p>
        </div>
        <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-xl px-4 py-2 text-center sm:text-right">
            <span class="text-[10px] uppercase font-bold text-indigo-300 block tracking-wider">Total Saved Items</span>
            <span class="text-2xl font-black text-indigo-400">{{ $savedWords->count() }}</span>
        </div>
    </div>

    @if($modules->isEmpty() || $savedWords->count() === 0)
        <div class="bg-slate-900 border border-white/10 rounded-2xl p-12 text-center max-w-xl mx-auto">
            <span class="text-4xl">🍃</span>
            <h3 class="text-base font-bold text-slate-200 mt-4">Word Bank Is Empty</h3>
            <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                You haven't highlight-saved any vocabulary words yet. Visit your reading rooms to query definitions via AI!
            </p>
            <a href="{{ route('modules.index') }}" class="inline-block mt-5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                Go to Library
            </a>
        </div>
    @else
        {{-- Module Accordion/Dropdown Accordion Section --}}
        <div class="space-y-4" x-data="{ activeModule: null }">
            @foreach($modules as $module)
                {{-- Filter to hide empty structural rows --}}
                @if($module->savedWords->count() > 0)
                    <div x-data="{ isDeleteModalOpen: false }" class="bg-slate-900 border border-white/10 rounded-xl transition shadow-sm relative">
                        
                        {{-- Delete Confirmation Modal --}}
                        <div x-show="isDeleteModalOpen" x-cloak
                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
                            <div @click.away="isDeleteModalOpen = false"
                                 class="bg-slate-900 border border-slate-700/50 rounded-2xl shadow-2xl max-w-md w-full p-6">
                                <div class="text-center">
                                    <div class="text-3xl mb-3">🗑️</div>
                                    <h3 class="text-lg font-bold text-white mb-1">
                                        Delete module <span class="text-indigo-400">{{ $module->title }}</span>?
                                    </h3>
                                    <p class="text-sm text-slate-400 mb-6 leading-relaxed">
                                        All vocabulary words saved under this module will be permanently removed. This action cannot be undone.
                                    </p>
                                    <div class="flex gap-3 justify-center">
                                        <button @click="
                                            fetch('{{ route('words.clearModule', $module->id) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                                    'Accept': 'application/json'
                                                },
                                                body: new URLSearchParams({ _method: 'DELETE' })
                                            }).then(r => { if (r.ok) { isDeleteModalOpen = false; $root.remove(); } });
                                        " class="bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold px-5 py-2.5 rounded-lg transition shadow-lg shadow-rose-600/20">Yes, Delete Everything</button>
                                        <button @click="isDeleteModalOpen = false" class="bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs font-bold px-5 py-2.5 rounded-lg transition">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown Toggle Header --}}
                        <button @click="activeModule = (activeModule === {{ $module->id }} ? null : {{ $module->id }})"
                                class="w-full flex items-center justify-between p-5 bg-slate-950/40 hover:bg-slate-950/80 transition text-left focus:outline-none">
                            <div class="flex items-center gap-3">
                                <span class="bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">
                                    Module #{{ $module->id }}
                                </span>
                                <h3 class="text-base font-bold text-white tracking-tight truncate max-w-md">
                                    {{ $module->title }}
                                </h3>
                                {{-- Delete Module Button --}}
                                <span @click.stop="isDeleteModalOpen = true"
                                      class="cursor-pointer text-slate-500 hover:text-rose-400 transition text-xs p-1.5 rounded-md hover:bg-white/5"
                                      title="Delete this module and all its words">
                                    🗑️
                                </span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-xs font-semibold text-slate-400 bg-white/5 border border-white/5 px-2.5 py-0.5 rounded-full">
                                    {{ $module->savedWords->count() }} {{ Str::plural('word', $module->savedWords->count()) }}
                                </span>
                                {{-- Rotating Caret Vector --}}
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" 
                                     :class="activeModule === {{ $module->id }} ? 'rotate-180' : ''" 
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        {{-- Collapsible Panel Container --}}
                        <div x-show="activeModule === {{ $module->id }}" 
                             x-collapse
                             x-cloak
                             class="border-t border-white/5 bg-slate-900/50 p-5">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($module->savedWords as $item)
                                    <div class="bg-slate-950/50 border border-white/5 rounded-xl p-4 flex flex-col justify-between hover:border-white/10 transition relative group shadow-inner">
                                        
                                        {{-- Inline Delete Action --}}
                                        <form action="{{ route('words.destroy', $item->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this vocabulary item?');"
                                              class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition duration-150">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-500 hover:text-rose-400 text-xs p-1.5 rounded-md bg-black/20 hover:bg-black/40 transition">
                                                🗑️
                                            </button>
                                        </form>

                                        <div>
                                            <h4 class="text-sm font-bold text-indigo-400 tracking-tight mb-1.5 break-words">
                                                {{ $item->word }}
                                            </h4>
                                            <p class="text-xs text-slate-300 leading-relaxed font-medium pl-1">
                                                {{ $item->definition }}
                                            </p>
                                        </div>

                                        <div class="mt-3 pt-2 border-t border-white/5 text-[9px] text-slate-500 font-semibold select-none">
                                            Saved: {{ $item->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

</div>
@endsection