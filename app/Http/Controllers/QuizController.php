<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\SavedWord;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->has('module_id')) {
            $modules = Module::has('savedWords')->withCount('savedWords')->get();

            return view('quiz_hub', compact('modules'));
        }

        $moduleId = $request->query('module_id');
        
        if (!$moduleId) {
            return redirect()->route('modules.index')->withErrors([
                'error' => 'Please choose a reading room module first before starting a quiz evaluation.'
            ]);
        }
        
        $module = Module::findOrFail($moduleId);
        $progressKey = "quiz_progress_{$moduleId}";

        if ($request->query('reset') === '1') {
            session()->forget($progressKey);
        }

        $savedProgress = session($progressKey, [
            'answers' => [],
            'attempts' => [],
            'completed' => false,
            'score' => 0,
            'total' => 0,
            'breakdown' => [],
        ]);

        if (!empty($savedProgress['completed'])) {
            return view('quiz_results', [
                'moduleId' => $moduleId,
                'score' => $savedProgress['score'] ?? 0,
                'total' => $savedProgress['total'] ?? 0,
                'totalWrongAttempts' => array_sum(array_map('intval', $savedProgress['attempts'] ?? [])),
                'breakdown' => $savedProgress['breakdown'] ?? [],
            ]);
        }

        // Sanitize historic definitions: exclude records containing problematic tokens
        $badPatterns = ['error', 'quota', 'OpenAI'];

        // Load saved words for the module and filter out any with problematic historic definitions
        // Select only core word fields; hint strings will be computed dynamically.
        $savedWords = SavedWord::select('id','word','definition','context')
            ->where('module_id', $moduleId)
            ->get()
            ->filter(function ($item) use ($badPatterns) {
                $def = strtolower($item->definition ?? '');
                foreach ($badPatterns as $pat) {
                    if (strpos($def, strtolower($pat)) !== false) {
                        return false;
                    }
                }
                return true;
            })->values();


        $allDefinitions = SavedWord::select('id', 'definition')
            ->where('module_id', $moduleId)
            ->get()
            ->filter(function ($item) use ($badPatterns) {
                $def = strtolower($item->definition ?? '');
                foreach ($badPatterns as $pat) {
                    if (strpos($def, strtolower($pat)) !== false) {
                        return false;
                    }
                }
                return true;
            })->values();

        // Build a safe pool of unique definition texts to draw distractors from
        $definitionPool = $allDefinitions->pluck('definition')->map(function ($d) {
            return trim((string) $d);
        })->filter()->unique()->values();

        // For each saved word, prepare a distractor list explicitly excluding its own definition
        $distractorsMap = [];
        foreach ($savedWords as $w) {
            $currentDef = trim((string) ($w->definition ?? ''));
            $pool = $definitionPool->reject(function ($d) use ($currentDef) {
                return mb_strtolower(trim($d)) === mb_strtolower($currentDef);
            })->values();

            // Shuffle and take up to 3 distractors
            $shuffled = $pool->shuffle()->take(3)->values()->toArray();
            $distractorsMap[$w->id] = $shuffled;
        }

        $savedWords = $savedWords->map(function ($w) {
            $word = trim((string) ($w->word ?? ''));
            $definition = trim((string) ($w->definition ?? ''));
            $cleanDefinition = preg_replace('/\s+/', ' ', strip_tags($definition));

            $letter = strtoupper(mb_substr($word, 0, 1));
            $length = mb_strlen($word);
            $wordPattern = '';
            if ($length <= 2) {
                $wordPattern = $word;
            } else {
                $middle = implode(' ', array_fill(0, max(0, $length - 2), '_'));
                $wordPattern = mb_strtolower(mb_substr($word, 0, 1)) . ' ' . $middle . ' ' . mb_strtolower(mb_substr($word, -1));
            }

            $hint1 = '';
            $hint2 = '';
            $hint3 = '';

            $sanitizedDefinition = html_entity_decode(strip_tags($definition), ENT_QUOTES | ENT_HTML5);
            $sanitizedDefinition = trim(preg_replace('/\s+/', ' ', $sanitizedDefinition));
            $definitionWords = preg_split('/\s+/', $sanitizedDefinition, -1, PREG_SPLIT_NO_EMPTY) ?: [];

            $firstSegment = '';
            $remainingSegment = '';
            if (!empty($definitionWords)) {
                $firstSegment = implode(' ', array_slice($definitionWords, 0, min(6, count($definitionWords))));
                $remainingSegment = implode(' ', array_slice($definitionWords, min(6, count($definitionWords))));
            }

            if (empty($firstSegment) && !empty($sanitizedDefinition)) {
                $firstSegment = mb_substr($sanitizedDefinition, 0, 48);
            }
            if (empty($remainingSegment) && !empty($sanitizedDefinition) && mb_strlen($sanitizedDefinition) > mb_strlen($firstSegment)) {
                $remainingSegment = trim(mb_substr($sanitizedDefinition, mb_strlen($firstSegment)));
            }

            if (!empty($firstSegment)) {
                $hint1 = 'Core Concept: primarily dealing with ' . lcfirst($firstSegment) . '...';
            } else {
                $hint1 = 'Core Concept: primarily dealing with the central idea of this concept...';
            }

            if (!empty($remainingSegment)) {
                $hint2 = 'Deep Context: involving the process of ' . lcfirst($remainingSegment) . '.';
            } else {
                $hint2 = 'Deep Context: involving the broader meaning and function of this concept.';
            }

            $hintType = match (true) {
                mb_strlen($word) <= 6 => 'exact action or terminology',
                mb_strlen($word) <= 10 => 'distinct word or key phrase',
                default => 'technical term or concept',
            };
            $hint3 = 'Memory Trigger: Think of the ' . $hintType . ' used when you engage with this concept during active study sessions!';

            $w->hint_1 = $hint1;
            $w->hint_2 = $hint2;
            $w->hint_3 = $hint3;

            return $w;
        });

        return view('quiz', [
            'module' => $module,
            'savedWords' => $savedWords,
            'allDefinitions' => $allDefinitions,
            'distractorsMap' => $distractorsMap,
            'savedProgress' => $savedProgress,
        ]);
    }

    public function check(Request $request)
    {
        $moduleId = $request->input('module_id');
        $userAnswers = $request->input('answers', []);
        $attempts = $request->input('attempts', []);
        $progressKey = "quiz_progress_{$moduleId}";

        if ($request->input('action') === 'save_quit') {
            session([$progressKey => [
                'answers' => $userAnswers,
                'attempts' => $attempts,
                'completed' => false,
            ]]);

            return redirect('/quiz')->with('success', 'Progress saved successfully! You can resume this arena anytime.');
        }

        $savedWords = SavedWord::where('module_id', $moduleId)
            ->get();

        $score = 0;
        $totalQuestions = $savedWords->count();
        $breakdown = [];

        foreach ($savedWords as $wordItem) {
            $submitted = trim($userAnswers[$wordItem->id] ?? '');
            $correct = trim($wordItem->definition ?? '');
            $isCorrect = mb_strtolower($submitted) === mb_strtolower($correct);

            if ($isCorrect) {
                $score++;
            }

            $breakdown[] = [
                'word' => $wordItem->word,
                'correct_definition' => $wordItem->definition,
                'submitted_definition' => $submitted,
                'is_correct' => $isCorrect
            ];
        }

        $validAttempts = array_filter($attempts, function ($value) {
            return is_numeric($value) && intval($value) >= 0;
        });

        $totalWrongAttempts = array_sum(array_map('intval', $validAttempts));

        session([$progressKey => [
            'answers' => $userAnswers,
            'attempts' => $attempts,
            'completed' => true,
            'score' => $score,
            'total' => $totalQuestions,
            'breakdown' => $breakdown,
        ]]);

        return view('quiz_results', [
            'moduleId' => $moduleId,
            'score' => $score,
            'total' => $totalQuestions,
            'totalWrongAttempts' => $totalWrongAttempts,
            'breakdown' => $breakdown
        ]);
    }
}