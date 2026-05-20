<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\SavedWord;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $moduleId = $request->query('module_id');
        
        if (!$moduleId) {
            return redirect()->route('modules.index')->withErrors([
                'error' => 'Please choose a reading room module first before starting a quiz evaluation.'
            ]);
        }
        
        $module = Module::findOrFail($moduleId);
        $savedWords = SavedWord::where('module_id', $moduleId)->get();
        $allDefinitions = SavedWord::select('id', 'definition')->get();

        return view('quiz', [
            'module' => $module,
            'savedWords' => $savedWords,
            'allDefinitions' => $allDefinitions
        ]);
    }

    public function checkAnswer(Request $request)
    {
        $moduleId = $request->input('module_id');
        $userAnswers = $request->input('answers', []);

        $savedWords = SavedWord::where('module_id', $moduleId)->get();
        
        $score = 0;
        $totalQuestions = $savedWords->count();
        $breakdown = [];

        foreach ($savedWords as $wordItem) {
            $submitted = $userAnswers[$wordItem->id] ?? null;
            $isCorrect = (trim($submitted) === trim($wordItem->definition));
            
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

        return view('quiz_results', [
            'moduleId' => $moduleId,
            'score' => $score,
            'total' => $totalQuestions,
            'breakdown' => $breakdown
        ]);
    }
}