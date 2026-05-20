@extends('app')

@section('title', 'Reading Room Workspace')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8" x-data="readingWorkspaceEngine()">
    
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center pb-4 border-b border-white/5 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white mt-1 tracking-tight">{{ $module->title }}</h1>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('quiz.index', ['module_id' => $module->id]) }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-md shadow-indigo-600/10 flex items-center gap-2">
                🧠 Take Module #{{ $module->id }} Quiz ➔
            </a>
            <a href="{{ route('modules.index') }}" class="text-xs text-slate-400 hover:text-white transition">
                ← Exit Room
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start relative">
        
        <div class="lg:col-span-2 bg-slate-900/40 border border-white/10 rounded-2xl p-8 shadow-sm relative">
            <div class="text-xs font-semibold text-slate-500 mb-4 tracking-wider uppercase flex items-center gap-2 select-none">
                💡 Tip: Highlight any keyword vocabulary text to query dictionary summaries instantly.
            </div>
            
            <div class="reading-body text-slate-300 leading-relaxed font-serif text-lg tracking-wide space-y-4 whitespace-pre-line"
                 @mouseup="handleTextHighlightSelection($event)">
                {{ $module->body_text }}
            </div>

            <button x-show="showLookUpButton" 
                    x-cloak
                    :style="`position: absolute; left: ${btnX}px; top: ${btnY}px;`"
                    @mousedown.prevent="executeDictionaryLookup()"
                    class="z-50 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl border border-indigo-400 flex items-center gap-1 animate-fade-in">
                🔍 Look Up
            </button>
        </div>

        <div class="space-y-6 lg:sticky lg:top-20">
            
            <div class="bg-slate-900 border border-white/10 rounded-2xl p-5 shadow-md">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 pb-2 border-b border-white/5 flex items-center gap-2 select-none">
                    💡 Interactive Desk Guide
                </h3>
                
                <div x-show="!selectedWord && !isLoading" class="text-center py-8 text-slate-500 text-xs">
                    Highlight a word and click "Look Up" to display definitions here.
                </div>

                <div x-show="isLoading" class="text-center py-8 space-y-3" x-cloak>
                    <div class="w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                    <p class="text-[11px] text-slate-400 tracking-wide font-medium">Querying Gemini Engine model dictionary configurations...</p>
                </div>

                <div x-show="selectedWord && !isLoading" class="space-y-4 animate-fade-in" x-cloak>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">Selected Target Word</span>
                        <h2 class="text-xl font-black text-indigo-400 mt-0.5 tracking-tight break-words" x-text="selectedWord"></h2>
                    </div>

                    <div class="p-3.5 bg-black/30 rounded-xl border border-white/5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Deduced Meaning</span>
                        <p class="text-xs text-slate-200 leading-relaxed font-medium" :class="isError ? 'text-rose-400 font-semibold' : ''" x-text="definition"></p>
                    </div>

                    <div class="p-3 bg-indigo-500/5 rounded-xl border border-indigo-500/10">
                        <span class="text-[9px] font-bold text-indigo-300 uppercase tracking-wider block mb-0.5">Auto-Save Audit</span>
                        <p class="text-[10px] text-slate-400 leading-normal">This translation pair pattern has been written directly to your Word Bank memory inventory cards.</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 border border-white/10 rounded-2xl p-5 shadow-md">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 pb-2 border-b border-white/5 select-none">
                    📚 Module Vocabulary Repository ({{ $module->savedWords->count() }})
                </h3>
                @if($module->savedWords->isEmpty())
                    <p class="text-xs text-slate-500 text-center py-4 italic">No definitions written yet.</p>
                @else
                    <div class="max-h-60 overflow-y-auto space-y-2 pr-1">
                        @foreach($module->savedWords as $wordItem)
                            <div class="p-2.5 bg-slate-950/40 rounded-xl border border-white/5 flex flex-col gap-1">
                                <span class="text-xs font-bold text-indigo-300">{{ $wordItem->word }}</span>
                                <p class="text-[11px] text-slate-400 line-clamp-2 leading-normal">{{ $wordItem->definition }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function readingWorkspaceEngine() {
        return {
            selectedWord: '',
            definition: '',
            isLoading: false,
            isError: false,
            showLookUpButton: false,
            btnX: 0,
            btnY: 0,
            activeContext: '',

            handleTextHighlightSelection(event) {
                const selection = window.getSelection();
                const activeSelectionText = selection.toString().trim();
                
                // Track clean word matches, skip spaces or multi-line paragraphs
                if (!activeSelectionText || activeSelectionText.length < 2 || /\s/.test(activeSelectionText)) {
                    this.showLookUpButton = false;
                    return;
                }

                this.selectedWord = activeSelectionText;
                
                // Pull a sanitized text boundary context block around the choice
                if (selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    const containerNode = range.startContainer.parentNode;
                    this.activeContext = (containerNode.textContent || '').substring(0, 250);
                }

                const parentRect = event.currentTarget.getBoundingClientRect();
                this.btnX = event.clientX - parentRect.left;
                this.btnY = event.clientY - parentRect.top - 40;
                this.showLookUpButton = true;
            },

            executeDictionaryLookup() {
                this.showLookUpButton = false;
                this.isLoading = true;
                this.isError = false;
                this.definition = '';

                fetch("{{ route('words.define') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        word: this.selectedWord,
                        module_id: "{{ $module->id }}",
                        context: this.activeContext
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network payload rejection');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.definition = data.definition;
                    } else {
                        this.isError = true;
                        this.definition = data.message || "Unable to link dictionary properties.";
                    }
                })
                .catch(error => {
                    console.error("Lookup Failure Trace:", error);
                    this.isError = true;
                    this.definition = "Unable to link dictionary properties.";
                })
                .finally(() => {
                    this.isLoading = false;
                });
            }
        };
    }
</script>
@endpush