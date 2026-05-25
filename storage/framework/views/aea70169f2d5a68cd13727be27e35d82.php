

<?php $__env->startSection('title', 'Assessment Selection Hub'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 py-10">
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">🧠 Assessment Selection Hub</h1>
        <p class="mt-3 text-sm text-slate-400 max-w-2xl mx-auto">Select a completed learning module repository below to enter the evaluation arena.</p>
    </div>

    <?php if($modules->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center text-center py-12 max-w-md mx-auto bg-slate-900 border border-slate-800 rounded-2xl p-8 mt-10 shadow-xl">
            <div class="text-4xl">🧠</div>
            <h3 class="text-base font-bold text-slate-200 mt-4">Quiz Repositories Empty</h3>
            <p class="text-xs text-slate-400 mt-2 max-w-xs leading-relaxed">
                You haven't captured or saved any vocabulary words from your reading assignments yet. Visit the Library to begin active reading and unlock evaluations!
            </p>
            <a href="<?php echo e(route('library')); ?>" class="mt-6 inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs px-5 py-2.5 rounded-lg transition-all shadow-[0_0_15px_rgba(99,102,241,0.3)]">
                Go to Library Workspace
            </a>
        </div>
    <?php else: ?>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="group rounded-3xl border border-white/10 bg-slate-900 p-6 shadow-xl shadow-black/10 transition hover:-translate-y-1 hover:border-indigo-500/30">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <span class="inline-flex rounded-full bg-indigo-600/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-200">Module</span>
                        <span class="text-xs text-slate-500">ID <?php echo e($module->id); ?></span>
                    </div>
                    <h2 class="text-xl font-bold text-white"><?php echo e($module->title); ?></h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400"><?php echo e(\Illuminate\Support\Str::limit($module->body_text ?? 'No description available.', 110)); ?></p>
                    <div class="mt-8">
                        <a href="<?php echo e(route('quiz.index', ['module_id' => $module->id])); ?>" class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-purple-600 to-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-purple-500/20 hover:from-purple-500 hover:to-blue-500">Enter Quiz Arena</a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\Herd\ai-reading-buddy\resources\views/quiz_hub.blade.php ENDPATH**/ ?>