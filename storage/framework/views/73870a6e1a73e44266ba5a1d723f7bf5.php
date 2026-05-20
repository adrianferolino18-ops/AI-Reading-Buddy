

<?php $__env->startSection('title', 'Vocabulary Evaluation Review'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto px-4 py-8">
    
    <div class="mb-8 pb-4 border-b border-white/10 flex justify-between items-end">
        <div>
            <span class="text-[10px] font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-2.5 py-0.5 rounded-full uppercase tracking-widest">
                Active Assessment Arena
            </span>
            <h1 class="text-2xl font-black text-white mt-1 tracking-tight">Contextual Comprehension Evaluation</h1>
            <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                Testing your vocabulary understanding for: <span class="text-slate-200 font-semibold"><?php echo e($module->title); ?></span>
            </p>
        </div>
        <a href="<?php echo e(route('modules.show', $module->id)); ?>" class="text-xs text-slate-400 hover:text-white transition">
            ← Abort Quiz
        </a>
    </div>

    <?php if($savedWords->isEmpty()): ?>
        <div class="bg-slate-900 border border-white/10 rounded-2xl p-12 text-center">
            <span class="text-4xl">🏜️</span>
            <h3 class="text-base font-bold text-slate-200 mt-4">Workspace Repository Empty</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto leading-normal">
                You haven't captured or identified any unfamiliar vocabulary keywords inside this text module room space container workspace yet!
            </p>
            <a href="<?php echo e(route('modules.show', $module->id)); ?>" class="inline-block mt-5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                Return to Reading Room
            </a>
        </div>
    <?php else: ?>
        <form action="<?php echo e(route('quiz.check')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="module_id" value="<?php echo e($module->id); ?>">

            <?php $__currentLoopData = $savedWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $wordItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 shadow-sm space-y-4">
                    
                    <div class="flex items-center gap-2 pb-2 border-b border-white/5">
                        <span class="bg-white/5 text-slate-400 text-[10px] font-bold px-2 py-0.5 rounded-md">
                            Question <?php echo e($index + 1); ?> of <?php echo e($savedWords->count()); ?>

                        </span>
                        <span class="text-xs text-slate-500">Identify correct contextual correlation definition:</span>
                    </div>

                    <div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold block">Vocabulary Keyword Target</span>
                        <h2 class="text-2xl font-extrabold text-white tracking-tight mt-0.5">“<?php echo e($wordItem->word); ?>”</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5 pt-2">
                        <?php
                            $correctChoice = $wordItem->definition;
                            
                            $distractors = $allDefinitions->where('id', '!=', $wordItem->id)
                                                          ->pluck('definition')
                                                          ->unique()
                                                          ->shuffle()
                                                          ->take(3)
                                                          ->toArray();

                            $optionsGroupArray = array_merge([$correctChoice], $distractors);
                            shuffle($optionsGroupArray);
                        ?>

                        <?php $__currentLoopData = $optionsGroupArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionTextString): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-start gap-3 p-3 bg-slate-950/40 border border-white/5 hover:border-indigo-500/30 rounded-xl cursor-pointer transition select-none group">
                                <input type="radio" name="answers[<?php echo e($wordItem->id); ?>]" value="<?php echo e($optionTextString); ?>" required 
                                       class="mt-0.5 text-indigo-600 focus:ring-indigo-500 bg-slate-900 border-white/10">
                                <span class="text-xs text-slate-300 group-hover:text-slate-100 leading-relaxed font-medium">
                                    <?php echo e($optionTextString); ?>

                                </span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-6 py-3 rounded-xl transition shadow-md shadow-indigo-600/10 tracking-wide uppercase">
                    Submit Completed Answers Evaluation →
                </button>
            </div>
        </form>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\Herd\ai-reading-buddy\resources\views/quiz.blade.php ENDPATH**/ ?>