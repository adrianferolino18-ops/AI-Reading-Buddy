<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'AI Reading Buddy'); ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        serif: ['Georgia', 'serif'],
                    },
                    colors: {
                        navy: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            400: '#818cf8',
                            600: '#4f46e5',
                            700: '#1e1b4b',
                            800: '#151039',
                            900: '#0d0a2e',
                        },
                    },
                    keyframes: {
                        'fade-in': {
                            '0%':   { opacity: '0', transform: 'translateY(8px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                    animation: {
                        'fade-in':           'fade-in 0.3s ease-out',
                    },
                },
            },
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar       { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.04); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 3px; }
        .reading-body ::selection { background: rgba(99,102,241,0.35); color: #fff; }
        [x-cloak] { display: none !important; }
    </style>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="h-full bg-navy-900 text-white antialiased">

    <nav class="sticky top-0 z-40 bg-navy-800/90 backdrop-blur border-b border-white/10">
        <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="<?php echo e(route('modules.index')); ?>" class="flex items-center gap-2">
                <span class="text-lg font-semibold tracking-tight">
                    <span class="text-indigo-400">AI</span><span class="text-white"> Reading Buddy</span>
                </span>
            </a>

            <div class="flex items-center gap-1">
                <a href="<?php echo e(route('modules.index')); ?>"
                   class="px-3 py-1.5 rounded-md text-sm <?php echo e(request()->routeIs('modules.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:text-white hover:bg-white/5'); ?> transition">
                    Library
                </a>
                <a href="<?php echo e(route('dashboard')); ?>"
                   class="px-3 py-1.5 rounded-md text-sm <?php echo e(request()->routeIs('dashboard.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:text-white hover:bg-white/5'); ?> transition">
                    Word Bank
                </a>
                <a href="<?php echo e(url('/quiz')); ?>"
                   class="px-3 py-1.5 rounded-md text-sm <?php echo e(request()->routeIs('quiz.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:text-white hover:bg-white/5'); ?> transition">
                    Quiz
                </a>
            </div>
        </div>
    </nav>

    <?php if(session('success')): ?>
        <div id="flash-success" class="fixed top-16 left-1/2 -translate-x-1/2 z-50 animate-fade-in bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 px-5 py-2.5 rounded-xl text-sm shadow-lg">
            <?php echo e(session('success')); ?>

        </div>
        <script>setTimeout(() => document.getElementById('flash-success')?.remove(), 3000);</script>
    <?php endif; ?>

    <main class="animate-fade-in">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\DELL\Herd\ai-reading-buddy\resources\views/app.blade.php ENDPATH**/ ?>