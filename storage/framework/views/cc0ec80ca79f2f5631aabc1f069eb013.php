<?php $__env->startSection('title', 'Reading Room Workspace'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 py-8" x-data="readingWorkspaceEngine()">

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center pb-4 border-b border-white/5 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white mt-1 tracking-tight"><?php echo e($module->title); ?></h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('quiz.index', ['module_id' => $module->id])); ?>"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-md shadow-indigo-600/10 flex items-center gap-2">
                🧠 Take Module #<?php echo e($module->id); ?> Quiz ➔
            </a>
            <a href="<?php echo e(route('modules.index')); ?>" class="text-xs text-slate-400 hover:text-white transition">
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
                <?php echo e($module->body_text); ?>

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

                    <div x-show="!isError" class="p-3 bg-indigo-500/5 rounded-xl border border-indigo-500/10" x-cloak>
                        <span class="text-[9px] font-bold text-indigo-300 uppercase tracking-wider block mb-0.5">Auto-Save Audit</span>
                        <p class="text-[10px] text-slate-400 leading-normal">This translation pair pattern has been written directly to your Word Bank memory inventory cards.</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 border border-white/10 rounded-2xl p-5 shadow-md">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 pb-2 border-b border-white/5 select-none">
                    📚 Module Vocabulary Repository (<span x-text="wordCount"></span>)
                </h3>

                <div x-show="wordCount === 0 && recentWords.length === 0" class="text-center">
                    <p class="text-xs text-slate-500 text-center py-4 italic">No definitions written yet.</p>
                </div>

                
                <?php if($module->savedWords->isNotEmpty()): ?>
                    <div class="max-h-60 overflow-y-auto space-y-2 pr-1" id="server-word-list">
                        <?php $__currentLoopData = $module->savedWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wordItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-2.5 bg-slate-950/40 rounded-xl border border-white/5 flex flex-col gap-1">
                                <span class="text-xs font-bold text-indigo-300"><?php echo e($wordItem->word); ?></span>
                                <p class="text-[11px] text-slate-400 line-clamp-2 leading-normal"><?php echo e($wordItem->definition); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                
                <div class="mt-2 space-y-2 pr-1" x-show="recentWords.length > 0">
                    <template x-for="(entry, index) in recentWords" :key="index">
                        <div class="p-2.5 bg-slate-950/40 rounded-xl border border-indigo-500/10 flex flex-col gap-1">
                            <span class="text-xs font-bold text-indigo-300" x-text="entry.word"></span>
                            <p class="text-[11px] text-slate-400 line-clamp-2 leading-normal" x-text="entry.definition"></p>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
            wordCount: <?php echo e($module->savedWords->count()); ?>,
            recentWords: [],

            handleTextHighlightSelection(event) {
                const selection = window.getSelection();
                const activeSelectionText = selection.toString().trim();

                if (!activeSelectionText || activeSelectionText.length < 2 || /\s/.test(activeSelectionText)) {
                    this.showLookUpButton = false;
                    return;
                }

                this.selectedWord = activeSelectionText;

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

                fetch("<?php echo e(route('words.define')); ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        word: this.selectedWord,
                        module_id: Number(<?php echo e($module->id); ?>),
                        context: this.activeContext
                    })
                })
                .then(async response => {
                    const contentType = response.headers.get('Content-Type') || '';
                    let data;

                    if (contentType.includes('application/json')) {
                        try {
                            data = await response.json();
                        } catch (parseError) {
                            throw new Error('Backend returned invalid JSON.');
                        }
                    } else {
                        const text = await response.text();
                        throw new Error(text || 'Server returned an invalid response.');
                    }

                    if (response.status === 401) {
                        throw new Error('You must be logged in to look up words.');
                    }

                    if (!response.ok) {
                        throw new Error(data.message || 'Server request failed.');
                    }

                    return data;
                })
                .then(data => {
                    if (data && data.success && typeof data.definition === 'string' && data.definition.length > 0) {
                        this.definition = data.definition;
                        // Increment the live counter and append to the session word list
                        if (!this.recentWords.some(e => e.word === this.selectedWord)) {
                            this.recentWords.push({ word: this.selectedWord, definition: data.definition });
                        }
                        this.wordCount = <?php echo e($module->savedWords->count()); ?> + this.recentWords.length;
                    } else {
                        this.isError = true;
                        this.definition = data.message || data.definition || "Gemini engine returned an empty result.";
                    }
                })
                .catch(error => {
                    console.error("Lookup Failure Trace:", error);
                    this.isError = true;
                    this.definition = error.message || "An unexpected error occurred during lookup.";
                })
                .finally(() => {
                    this.isLoading = false;
                });
            }
        };
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\Herd\ai-reading-buddy\resources\views/read.blade.php ENDPATH**/ ?>