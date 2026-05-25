

<?php $__env->startSection('title', 'Vocabulary Word Bank'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 py-8 text-slate-100">
    
    
    <div class="mb-8 pb-6 border-b border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>🗂️</span> Personal Word Bank
            </h1>
            <p class="text-sm text-slate-400 mt-1">Review your saved vocabulary terms grouped neatly by assignment modules.</p>
        </div>
        <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-xl px-4 py-2 text-center sm:text-right">
            <span class="text-[10px] uppercase font-bold text-indigo-300 block tracking-wider">Total Saved Items</span>
            <span class="text-2xl font-black text-indigo-400"><?php echo e($savedWords->count()); ?></span>
        </div>
    </div>

    <?php if($modules->isEmpty() || $savedWords->count() === 0): ?>
        <div class="bg-slate-900 border border-white/10 rounded-2xl p-12 text-center max-w-xl mx-auto">
            <span class="text-4xl">🍃</span>
            <h3 class="text-base font-bold text-slate-200 mt-4">Word Bank Is Empty</h3>
            <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                You haven't highlight-saved any vocabulary words yet. Visit your reading rooms to query definitions via AI!
            </p>
            <a href="<?php echo e(route('modules.index')); ?>" class="inline-block mt-5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                Go to Library
            </a>
        </div>
    <?php else: ?>
        
        <div class="space-y-4" x-data="{ activeModule: null }">
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php if($module->savedWords->count() > 0): ?>
                    <div class="bg-slate-900 border border-white/10 rounded-xl overflow-hidden transition shadow-sm">
                        
                        
                        <button @click="activeModule = (activeModule === <?php echo e($module->id); ?> ? null : <?php echo e($module->id); ?>)"
                                class="w-full flex items-center justify-between p-5 bg-slate-950/40 hover:bg-slate-950/80 transition text-left focus:outline-none">
                            <div class="flex items-center gap-3">
                                <span class="bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">
                                    Module #<?php echo e($module->id); ?>

                                </span>
                                <h3 class="text-base font-bold text-white tracking-tight truncate max-w-md">
                                    <?php echo e($module->title); ?>

                                </h3>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-xs font-semibold text-slate-400 bg-white/5 border border-white/5 px-2.5 py-0.5 rounded-full">
                                    <?php echo e($module->savedWords->count()); ?> <?php echo e(Str::plural('word', $module->savedWords->count())); ?>

                                </span>
                                
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" 
                                     :class="activeModule === <?php echo e($module->id); ?> ? 'rotate-180' : ''" 
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        
                        <div x-show="activeModule === <?php echo e($module->id); ?>" 
                             x-collapse
                             x-cloak
                             class="border-t border-white/5 bg-slate-900/50 p-5">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php $__currentLoopData = $module->savedWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bg-slate-950/50 border border-white/5 rounded-xl p-4 flex flex-col justify-between hover:border-white/10 transition relative group shadow-inner">
                                        
                                        
                                        <form action="<?php echo e(route('words.destroy', $item->id)); ?>" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this vocabulary item?');"
                                              class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition duration-150">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-slate-500 hover:text-rose-400 text-xs p-1.5 rounded-md bg-black/20 hover:bg-black/40 transition">
                                                🗑️
                                            </button>
                                        </form>

                                        <div>
                                            <h4 class="text-sm font-bold text-indigo-400 tracking-tight mb-1.5 break-words">
                                                <?php echo e($item->word); ?>

                                            </h4>
                                            <p class="text-xs text-slate-300 leading-relaxed font-medium pl-1">
                                                <?php echo e($item->definition); ?>

                                            </p>
                                        </div>

                                        <div class="mt-3 pt-2 border-t border-white/5 text-[9px] text-slate-500 font-semibold select-none">
                                            Saved: <?php echo e($item->created_at->format('M d, Y')); ?>

                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\Herd\ai-reading-buddy\resources\views/dashboard.blade.php ENDPATH**/ ?>