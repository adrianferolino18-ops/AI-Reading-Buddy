<?php $__env->startSection('title', 'Vocabulary Evaluation Review'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $preFilledAnswers = [];
    foreach ($savedWords as $wordItem) {
        $savedAnswer = $savedProgress['answers'][$wordItem->id] ?? '';
        if ($savedAnswer !== '') {
            $preFilledAnswers['q_' . $wordItem->id] = true;
        }
    }
?>
<div x-data="{ answers: <?php echo e(json_encode($preFilledAnswers)); ?> }" class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Contextual Comprehension Evaluation</h1>
            <p class="text-sm text-slate-400">Testing your vocabulary understanding for: <span class="font-semibold text-slate-200"><?php echo e($module->title); ?></span></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('modules.show', $module->id)); ?>" class="text-xs text-slate-400 hover:text-white">← Abort Quiz</a>
            <button type="submit" form="quizSessionForm" name="action" value="save_quit" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold px-3 py-1.5 rounded-lg border border-slate-700 transition-all">💾 Save & Quit</button>
        </div>
    </div>

    <?php if($savedWords->isEmpty()): ?>
        <div class="text-center py-8">
            <h3 class="text-base font-bold text-slate-200 mt-4">Workspace Repository Empty</h3>
            <p class="text-xs text-slate-400 mt-1">You haven't captured any vocabulary for this reading room yet.</p>
            <a href="<?php echo e(route('modules.show', $module->id)); ?>" class="mt-5 inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold">Return to Reading Room</a>
        </div>
    <?php else: ?>

        <form id="quizSessionForm" action="<?php echo e(route('quiz.check')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="module_id" value="<?php echo e($module->id); ?>">

            <div class="grid grid-cols-1 gap-6">
                <?php $__currentLoopData = $savedWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $wordItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $badPatternsLocal = ['error','quota','openai'];
                        $defLower = strtolower($wordItem->definition ?? '');
                        if (collect($badPatternsLocal)->contains(function($p) use ($defLower) { return strpos($defLower, $p) !== false; })) {
                            continue;
                        }

                        $savedAnswer = $savedProgress['answers'][$wordItem->id] ?? '';
                        $savedAttempts = intval($savedProgress['attempts'][$wordItem->id] ?? 0);
                        $savedIsCorrect = $savedAnswer !== '' && $savedAnswer === $wordItem->definition;
                    ?>

                    <div x-data="{ selected: '<?php echo e(addslashes($savedAnswer)); ?>', wrongCount: <?php echo e($savedAttempts); ?>, locked: <?php echo e($savedIsCorrect ? 'true' : 'false'); ?>, hint1: false, hint2: false, hint3: false, correct: '<?php echo e(addslashes($wordItem->definition)); ?>', choose(choice) { if (!this.locked) { this.selected = choice; if (choice === this.correct) { this.locked = true; } else { this.wrongCount = Math.min(this.wrongCount + 1, 3); } } } }" x-init="if (selected) { answers['q_<?php echo e($wordItem->id); ?>'] = true; } locked = selected === correct" class="bg-slate-900 border border-white/10 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3 gap-3">
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Question <?php echo e($index + 1); ?> of <?php echo e($savedWords->count()); ?></div>
                                <div class="text-xs text-slate-500">Identify the correct contextual definition</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-xs text-slate-400">Incorrect attempts: <span x-text="wrongCount">0</span></div>
                                <span x-show="locked" x-cloak class="inline-flex items-center gap-1.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-[0_0_12px_rgba(16,185,129,0.1)]">✓ Completed</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Vocabulary Keyword</div>
                            <h2 class="text-2xl font-extrabold text-white">“<?php echo e($wordItem->word); ?>”</h2>
                        </div>

                        <?php
                            $correctChoice = $wordItem->definition;
                            $distractors = $distractorsMap[$wordItem->id] ?? [];

                            $optionsGroupArray = array_merge([$correctChoice], $distractors);
                            shuffle($optionsGroupArray);
                        ?>

                        <div class="grid grid-cols-1 gap-3">
                            <?php $__currentLoopData = $optionsGroupArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionTextString): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-start gap-3 p-3 rounded-xl cursor-pointer transition transform hover:scale-[1.01]"
                                       :class="selected === '<?php echo e(addslashes($optionTextString)); ?>' ? (selected === correct ? 'bg-emerald-900/40 ring-2 ring-emerald-400' : 'bg-rose-900/30 ring-2 ring-rose-400') : 'bg-slate-950/40'"
                                       @click.stop="choose('<?php echo e(addslashes($optionTextString)); ?>')">
                                    <input type="radio" class="hidden" :checked="selected === '<?php echo e(addslashes($optionTextString)); ?>'" @click.stop />
                                    <div class="flex-1 text-sm text-slate-200"><?php echo e($optionTextString); ?></div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <div class="mt-4 flex items-center gap-3">
                            <button type="button" @click="hint1 = !hint1" :class="hint1 || wrongCount >= 1 ? 'ring-2 ring-purple-400 bg-gradient-to-r from-purple-600 to-blue-600' : 'bg-slate-800'" class="px-3 py-1 rounded-md text-xs text-white shadow-sm transition">Hint 1</button>
                            <button type="button" @click="hint2 = !hint2" :class="hint2 || wrongCount >= 2 ? 'ring-2 ring-indigo-400 bg-gradient-to-r from-indigo-600 to-fuchsia-600' : 'bg-slate-800'" class="px-3 py-1 rounded-md text-xs text-white shadow-sm transition">Hint 2</button>
                            <button type="button" @click="hint3 = !hint3" :class="hint3 || wrongCount >= 3 ? 'ring-2 ring-amber-400 bg-gradient-to-r from-amber-400 to-yellow-500' : 'bg-slate-800'" class="px-3 py-1 rounded-md text-xs text-white shadow-sm transition">Hint 3</button>

                            <div class="ml-auto text-xs text-slate-400">&nbsp;</div>
                        </div>

                        <div class="mt-3 space-y-2 text-sm">
                            <div x-show="hint1 || wrongCount >= 1" x-transition class="text-slate-200 bg-gradient-to-r from-purple-800/25 to-blue-800/15 p-3 rounded">Level 1 — Broad hint: The word starts with "<?php echo e(strtoupper(substr($wordItem->word,0,1))); ?>" and is <?php echo e(strlen($wordItem->word)); ?> characters.</div>
                            <div x-show="hint2 || wrongCount >= 2" x-transition class="text-slate-200 bg-gradient-to-r from-indigo-800/25 to-fuchsia-800/15 p-3 rounded">Level 2 — Context hint: "<?php echo e(e(\Illuminate\Support\Str::limit($wordItem->context ?? '', 100))); ?>"</div>
                            <div x-show="hint3 || wrongCount >= 3" x-transition class="text-slate-200 bg-gradient-to-r from-amber-800/25 to-yellow-800/15 p-3 rounded">Level 3 — Final hint: "<?php echo e(e(\Illuminate\Support\Str::words($wordItem->definition, 6, ''))); ?>"</div>
                        </div>

                        <input type="hidden" name="answers[<?php echo e($wordItem->id); ?>]" :value="selected">
                        <input type="hidden" :name="'attempts[<?php echo e($wordItem->id); ?>]'" :value="wrongCount">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" :disabled="Object.keys(answers).length < <?php echo e($savedWords->count()); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-bold tracking-wide uppercase disabled:opacity-50 disabled:cursor-not-allowed">Submit Completed Answers Evaluation →</button>
            </div>
        </form>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\Herd\ai-reading-buddy\resources\views/quiz.blade.php ENDPATH**/ ?>