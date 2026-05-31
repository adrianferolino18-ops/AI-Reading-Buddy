

<?php $__env->startSection('title', 'Evaluation Results'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto px-4 py-8">
    
    <div class="bg-slate-900 border border-white/10 rounded-2xl p-8 text-center shadow-md mb-6">
        <span class="text-5xl block mb-2">🎉</span>
        <h1 class="text-2xl font-black text-white tracking-tight">Evaluation Complete</h1>
        <p class="text-xs text-slate-400 mt-1">Your answers have been processed against active repository keys.</p>
        
        <div class="grid gap-4 md:grid-cols-2 mb-6">
            <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 shadow-sm">
                <div class="text-[11px] uppercase tracking-[0.35em] text-slate-400 font-semibold mb-4">Core Mastery</div>
                <div class="flex items-center gap-3">
                    <span class="text-5xl font-black text-emerald-400"><?php echo e($score); ?></span>
                    <div>
                        <div class="text-lg font-bold text-white">/ <?php echo e($total); ?> Mastered</div>
                        <p class="text-xs text-slate-500 mt-1"><?php echo e($score === $total ? 'Perfect score — full module mastery' : 'Keep going — every mistake is a learning moment'); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center gap-2 text-xs uppercase tracking-[0.35em] text-indigo-300 font-semibold mb-4">
                    <span>🧠 Learning Resilience</span>
                </div>
                <div class="text-xl font-bold text-white">Total Incorrect Attempts Overcome</div>
                <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-indigo-500/10 border border-indigo-500/20 px-4 py-2 text-indigo-100 text-sm shadow-sm">
                    <span class="text-indigo-200">🧠</span>
                    <span><?php echo e($totalWrongAttempts); ?> <?php echo e($totalWrongAttempts === 1 ? 'attempt' : 'attempts'); ?></span>
                </div>
                <p class="text-xs text-slate-500 mt-3">This score reflects how many challenges you turned into progress while completing the quiz.</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-3">
            <a href="<?php echo e(route('modules.show', $moduleId)); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                Back to Workspace
            </a>
            <a href="<?php echo e(url('/quiz')); ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                🧠 Go to Quiz Hub
            </a>
            <a href="<?php echo e(url('/quiz?module_id=' . $moduleId . '&reset=1')); ?>" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-4 py-2 rounded-xl transition">
                🔄 Retake Quiz Arena
            </a>
            <a href="<?php echo e(route('dashboard')); ?>" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-4 py-2 rounded-xl transition">
                Open Word Bank
            </a>
        </div>
    </div>

    <div class="space-y-3">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider pl-1">Question Audit Breakdown</h3>
        
        <?php $__currentLoopData = $breakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-slate-900/60 border <?php echo e($item['is_correct'] ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-rose-500/20 bg-rose-500/5'); ?> rounded-xl p-4 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-white">“<?php echo e($item['word']); ?>”</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider <?php echo e($item['is_correct'] ? 'text-emerald-400' : 'text-rose-400'); ?>">
                        <?php echo e($item['is_correct'] ? '✓ Correct' : '✕ Incorrect'); ?>

                    </span>
                </div>
                
                <div class="text-xs space-y-1">
                    <p class="text-slate-400"><span class="font-semibold text-slate-300">Expected:</span> <?php echo e($item['correct_definition']); ?></p>
                    <?php if(!$item['is_correct']): ?>
                        <p class="text-rose-400/80"><span class="font-semibold text-rose-400">Submitted:</span> <?php echo e($item['submitted_definition'] ?? '[No Answer Provided]'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\Herd\ai-reading-buddy\resources\views/quiz_results.blade.php ENDPATH**/ ?>