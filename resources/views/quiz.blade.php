@extends('app')

@section('title', 'Vocabulary Evaluation Review')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    
    <div class="mb-8 pb-4 border-b border-white/10 flex justify-between items-end">
        <div>
            <span class="text-[10px] font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-2.5 py-0.5 rounded-full uppercase tracking-widest">
                Active Assessment Arena
            </span>
            <h1 class="text-2xl font-black text-white mt-1 tracking-tight">Contextual Comprehension Evaluation</h1>
            <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                Testing your vocabulary understanding for: <span class="text-slate-200 font-semibold">{{ $module->title }}</span>
            </p>
        </div>
        <a href="{{ route('modules.show', $module->id) }}" class="text-xs text-slate-400 hover:text-white transition">
            ← Abort Quiz
        </a>
    </div>

    @if($savedWords->isEmpty())
        <div class="bg-slate-900 border border-white/10 rounded-2xl p-12 text-center">
            <span class="text-4xl">🏜️</span>
            <h3 class="text-base font-bold text-slate-200 mt-4">Workspace Repository Empty</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto leading-normal">
                You haven't captured or identified any unfamiliar vocabulary keywords inside this text module room space container workspace yet!
            </p>
            <a href="{{ route('modules.show', $module->id) }}" class="inline-block mt-5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                Return to Reading Room
            </a>
        </div>
    @else
        <form action="{{ route('quiz.check') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="module_id" value="{{ $module->id }}">

            @foreach($savedWords as $index => $wordItem)
                <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 shadow-sm space-y-4">
                    
                    <div class="flex items-center gap-2 pb-2 border-b border-white/5">
                        <span class="bg-white/5 text-slate-400 text-[10px] font-bold px-2 py-0.5 rounded-md">
                            Question {{ $index + 1 }} of {{ $savedWords->count() }}
                        </span>
                        <span class="text-xs text-slate-500">Identify correct contextual correlation definition:</span>
                    </div>

                    <div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold block">Vocabulary Keyword Target</span>
                        <h2 class="text-2xl font-extrabold text-white tracking-tight mt-0.5">“{{ $wordItem->word }}”</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5 pt-2">
                        @php
                            $correctChoice = $wordItem->definition;
                            
                            $distractors = $allDefinitions->where('id', '!=', $wordItem->id)
                                                          ->pluck('definition')
                                                          ->unique()
                                                          ->shuffle()
                                                          ->take(3)
                                                          ->toArray();

                            $optionsGroupArray = array_merge([$correctChoice], $distractors);
                            shuffle($optionsGroupArray);
                        @endphp

                        @foreach($optionsGroupArray as $optionTextString)
                            <label class="flex items-start gap-3 p-3 bg-slate-950/40 border border-white/5 hover:border-indigo-500/30 rounded-xl cursor-pointer transition select-none group">
                                <input type="radio" name="answers[{{ $wordItem->id }}]" value="{{ $optionTextString }}" required 
                                       class="mt-0.5 text-indigo-600 focus:ring-indigo-500 bg-slate-900 border-white/10">
                                <span class="text-xs text-slate-300 group-hover:text-slate-100 leading-relaxed font-medium">
                                    {{ $optionTextString }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                </div>
            @endforeach

            <div class="pt-2 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-6 py-3 rounded-xl transition shadow-md shadow-indigo-600/10 tracking-wide uppercase">
                    Submit Completed Answers Evaluation →
                </button>
            </div>
        </form>
    @endif

</div>
@endsection