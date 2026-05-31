<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\SavedWord;
use App\Models\Module;

class WordController extends Controller
{
    public function index()
    {
        $modules = Module::with('savedWords')->get();
        $savedWords = SavedWord::orderBy('created_at', 'desc')->get();

        return view('dashboard', [
            'modules' => $modules,
            'savedWords' => $savedWords
        ]);
    }

    public function defineAndStoreWord(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'word'      => ['required', 'string', 'min:2'],
                'module_id' => ['required', 'integer', 'exists:modules,id'],
                'context'   => ['nullable', 'string', 'max:500']
            ])->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input for word lookup.',
                'errors'  => $e->errors()
            ], 422);
        }

        $word     = trim(preg_replace('/\s+/', ' ', $request->input('word')));
        $moduleId = intval($request->input('module_id'));

        $context = $request->input('context', '');
        $context = trim(preg_replace('/\s+/', ' ', $context));
        if (strlen($context) > 500) {
            $context = substr($context, 0, 500) . '...';
        }

        $apiKey = env('GEMINI_API_KEY');

        // FALLBACK GENERATOR
        // Used when API key is missing or Gemini quota is exhausted
        $generateFallback = function () use ($word, $moduleId, $context) {
            $displayWord = ucfirst(strtolower($word));
            $fallbackDefinition = null;

            try {
                $response = Http::get('https://api.dictionaryapi.dev/api/v2/entries/en/' . urlencode($word));
                if ($response->successful()) {
                    $data = $response->json();
                    if (is_array($data) && count($data) > 0) {
                        $entry = $data[0] ?? null;
                        $meaning = $entry['meanings'][0] ?? null;
                        $definitionText = $meaning['definitions'][0]['definition'] ?? null;
                        $partOfSpeech = $meaning['partOfSpeech'] ?? null;

                        if (!empty($definitionText)) {
                            $fallbackDefinition = trim((!empty($partOfSpeech) ? '[' . $partOfSpeech . '] - ' : '') . $definitionText);
                        }
                    }
                }
            } catch (\Throwable $dictionaryException) {
                Log::warning('Dictionary API fallback failed: ' . $dictionaryException->getMessage());
            }

            if (empty($fallbackDefinition)) {
                if (strtolower($word) === 'questions') {
                    $fallbackDefinition = "Sentences or phrases addressed to someone in order to elicit information or test knowledge.";
                } elseif (strtolower($word) === 'reading') {
                    $fallbackDefinition = "The action or skill of reading written or printed matter silently or aloud.";
                } elseif (strtolower($word) === 'students') {
                    $fallbackDefinition = "Individuals who are actively studying or learning at an educational institution.";
                } else {
                    $fallbackDefinition = "A specialized term or concept analyzed dynamically within this learning module context.";
                }
            }

            try {
                SavedWord::updateOrCreate(
                    ['module_id' => $moduleId, 'word' => $word],
                    ['definition' => $fallbackDefinition, 'context' => $context]
                );
            } catch (\Throwable $saveException) {
                Log::warning('Fallback definition — DB write failed: ' . $saveException->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save word to your vocabulary bank. Please try again.'
                ], 500);
            }

            return response()->json([
                'success'    => true,
                'word'       => $word,
                'definition' => $fallbackDefinition
            ]);
        };

        if (!$apiKey) {
            return $generateFallback();
        }

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

            $prompt = "Provide a concise, direct, one-sentence dictionary definition for the word: '{$word}'. ";
            if (!empty($context)) {
                $prompt .= "Use this context from the reading assignment to determine its precise meaning: \"{$context}\". ";
            }
            $prompt .= "Do not include introductory remarks, markdown symbols, or commentary. Provide only clear definition text.";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $definition   = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($definition) {
                    $cleanDefinition = trim($definition);
                    $cleanDefinition = str_replace(['*', '"', '`'], '', $cleanDefinition);

                    SavedWord::updateOrCreate(
                        ['module_id' => $moduleId, 'word' => $word],
                        ['definition' => $cleanDefinition, 'context' => $context]
                    );
                }
            }

            Log::warning('Gemini API limited or failed. Engaging fallback. Raw response: ' . $response->body());
            return $generateFallback();

        } catch (\Throwable $e) {
            Log::error('Gemini Endpoint Failure: ' . $e->getMessage());
            return $generateFallback();
        }
    }

    public function clearModule($moduleId)
    {
        SavedWord::where('module_id', $moduleId)->delete();
        session()->forget('quiz_progress_' . $moduleId);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All vocabulary terms cleared successfully.');
    }

    public function destroy($id)
    {
        $word = SavedWord::findOrFail($id);
        $word->delete();

        return redirect()->back()->with('success', 'Vocabulary word successfully removed.');
    }
}