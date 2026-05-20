@extends('app')

@section('title', 'Evaluation Results')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    
    <div class="bg-slate-900 border border-white/10 rounded-2xl p-8 text-center shadow-md mb-6">
        <span class="text-5xl block mb-2">🎉</span>
        <h1 class="text-2xl font-black text-white tracking-tight">Evaluation Complete</h1>
        <p class="text-xs text-slate-400 mt-1">Your answers have been processed against active repository keys.</p>
        
        <div class="my-6 inline-block bg-black/30 border border-white/5 px-6 py-4 rounded-2xl">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest block">Final Accuracy Score</span>
            <span class="text-4xl font-black text-indigo-400 block mt-1">{{ $score }} / {{ $total }}</span>
            <span class="text-xs text-slate-500 block mt-1">({{ $total > 0 ? round(($score / $total) * 100) : 0 }}% Accuracy Matrix)</span>
        </div>

        <div class="flex justify-center gap-3">
            <a href="{{ route('modules.show', $moduleId) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                Back to Workspace
            </a>
            <a href="{{ route('dashboard.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-4 py-2 rounded-xl transition">
                Open Word Bank
            </a>
        </div>
    </div>

    <div class="space-y-3">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider pl-1">Question Audit Breakdown</h3>
        
        @foreach($breakdown as $item)
            <div class="bg-slate-900/60 border {{ $item['is_correct'] ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-rose-500/20 bg-rose-500/5' }} rounded-xl p-4 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-white">“{{ $item['word'] }}”</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $item['is_correct'] ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $item['is_correct'] ? '✓ Correct' : '✕ Incorrect' }}
                    </span>
                </div>
                
                <div class="text-xs space-y-1">
                    <p class="text-slate-400"><span class="font-semibold text-slate-300">Expected:</span> {{ $item['correct_definition'] }}</p>
                    @if(!$item['is_correct'])
                        <p class="text-rose-400/80"><span class="font-semibold text-rose-400">Submitted:</span> {{ $item['submitted_definition'] ?? '[No Answer Provided]' }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection