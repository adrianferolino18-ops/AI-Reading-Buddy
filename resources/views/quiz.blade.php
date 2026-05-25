@extends('app')

@section('title', 'Vocabulary Evaluation Review')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Contextual Comprehension Evaluation</h1>
            <p class="text-sm text-slate-400">Testing your vocabulary understanding for: <span class="font-semibold text-slate-200">{{ $module->title }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('modules.show', $module->id) }}" class="text-xs text-slate-400 hover:text-white">← Abort Quiz</a>
            <button type="submit" form="quizSessionForm" name="action" value="save_quit" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold px-3 py-1.5 rounded-lg border border-slate-700 transition-all">💾 Save & Quit</button>
        </div>
    </div>

    @if($savedWords->isEmpty())
        <div class="text-center py-8">
            <h3 class="text-base font-bold text-slate-200 mt-4">Workspace Repository Empty</h3>
            <p class="text-xs text-slate-400 mt-1">You haven't captured any vocabulary for this reading room yet.</p>
            <a href="{{ route('modules.show', $module->id) }}" class="mt-5 inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold">Return to Reading Room</a>
        </div>
    @else

        <form id="quizSessionForm" action="{{ route('quiz.check') }}" method="POST">
            @csrf
            <input type="hidden" name="module_id" value="{{ $module->id }}">

            <div class="grid grid-cols-1 gap-6">
                @foreach($savedWords as $index => $wordItem)
                    @php
                        $badPatternsLocal = ['error','quota','openai'];
                        $defLower = strtolower($wordItem->definition ?? '');
                        if (collect($badPatternsLocal)->contains(function($p) use ($defLower) { return strpos($defLower, $p) !== false; })) {
                            continue;
                        }
                    @endphp

                    <div x-data="{ selected: '{{ addslashes($savedProgress['answers'][$wordItem->id] ?? '') }}', wrongCount: {{ intval($savedProgress['attempts'][$wordItem->id] ?? 0) }}, locked: false, hint1: false, hint2: false, hint3: false, correct: '{{ addslashes($wordItem->definition) }}', choose(choice) { if (!this.locked) { this.selected = choice; if (choice === this.correct) { this.locked = true; } else { this.wrongCount = Math.min(this.wrongCount + 1, 3); } } } }" x-init="locked = selected === correct" class="bg-slate-900 border border-white/10 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3 gap-3">
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Question {{ $index + 1 }} of {{ $savedWords->count() }}</div>
                                <div class="text-xs text-slate-500">Identify the correct contextual definition</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-xs text-slate-400">Incorrect attempts: <span x-text="wrongCount">0</span></div>
                                <span x-show="locked" x-cloak class="inline-flex items-center gap-1.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-[0_0_12px_rgba(16,185,129,0.1)]">✓ Completed</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Vocabulary Keyword</div>
                            <h2 class="text-2xl font-extrabold text-white">“{{ $wordItem->word }}”</h2>
                        </div>

                        @php
                            $correctChoice = $wordItem->definition;
                            $distractors = $distractorsMap[$wordItem->id] ?? [];

                            $optionsGroupArray = array_merge([$correctChoice], $distractors);
                            shuffle($optionsGroupArray);
                        @endphp

                        <div class="grid grid-cols-1 gap-3">
                            @foreach($optionsGroupArray as $optionTextString)
                                <label class="flex items-start gap-3 p-3 rounded-xl cursor-pointer transition transform hover:scale-[1.01]"
                                       :class="selected === '{{ addslashes($optionTextString) }}' ? (selected === correct ? 'bg-emerald-900/40 ring-2 ring-emerald-400' : 'bg-rose-900/30 ring-2 ring-rose-400') : 'bg-slate-950/40'"
                                       @click.stop="choose('{{ addslashes($optionTextString) }}')">
                                    <input type="radio" class="hidden" :checked="selected === '{{ addslashes($optionTextString) }}'" @click.stop />
                                    <div class="flex-1 text-sm text-slate-200">{{ $optionTextString }}</div>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-4 flex items-center gap-3">
                            <button type="button" @click="hint1 = !hint1" :class="hint1 || wrongCount >= 1 ? 'ring-2 ring-purple-400 bg-gradient-to-r from-purple-600 to-blue-600' : 'bg-slate-800'" class="px-3 py-1 rounded-md text-xs text-white shadow-sm transition">Hint 1</button>
                            <button type="button" @click="hint2 = !hint2" :class="hint2 || wrongCount >= 2 ? 'ring-2 ring-indigo-400 bg-gradient-to-r from-indigo-600 to-fuchsia-600' : 'bg-slate-800'" class="px-3 py-1 rounded-md text-xs text-white shadow-sm transition">Hint 2</button>
                            <button type="button" @click="hint3 = !hint3" :class="hint3 || wrongCount >= 3 ? 'ring-2 ring-amber-400 bg-gradient-to-r from-amber-400 to-yellow-500' : 'bg-slate-800'" class="px-3 py-1 rounded-md text-xs text-white shadow-sm transition">Hint 3</button>

                            <div class="ml-auto text-xs text-slate-400">&nbsp;</div>
                        </div>

                        <div class="mt-3 space-y-2 text-sm">
                            <div x-show="hint1 || wrongCount >= 1" x-transition class="text-slate-200 bg-gradient-to-r from-purple-800/25 to-blue-800/15 p-3 rounded">Level 1 — Broad hint: The word starts with "{{ strtoupper(substr($wordItem->word,0,1)) }}" and is {{ strlen($wordItem->word) }} characters.</div>
                            <div x-show="hint2 || wrongCount >= 2" x-transition class="text-slate-200 bg-gradient-to-r from-indigo-800/25 to-fuchsia-800/15 p-3 rounded">Level 2 — Context hint: "{{ e(\Illuminate\Support\Str::limit($wordItem->context ?? '', 100)) }}"</div>
                            <div x-show="hint3 || wrongCount >= 3" x-transition class="text-slate-200 bg-gradient-to-r from-amber-800/25 to-yellow-800/15 p-3 rounded">Level 3 — Final hint: "{{ e(\Illuminate\Support\Str::words($wordItem->definition, 6, '')) }}"</div>
                        </div>

                        <input type="hidden" name="answers[{{ $wordItem->id }}]" :value="selected">
                        <input type="hidden" :name="'attempts[{{ $wordItem->id }}]'" :value="wrongCount">
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-bold tracking-wide uppercase">Submit Completed Answers Evaluation →</button>
            </div>
        </form>
    @endif

</div>
@endsection