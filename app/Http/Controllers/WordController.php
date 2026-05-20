<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedWord;
use App\Models\Module;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $request->validate([
            'word' => 'required|string',
            'module_id' => 'required',
            'context' => 'nullable|string'
        ]);

        // Clean up inputs to prevent formatting issues breaking the API payload
        $word = trim(preg_replace('/\s+/', ' ', $request->input('word')));
        $moduleId = $request->input('module_id');
        
        // Limit context string size and sanitize white spaces
        $context = $request->input('context', '');
        $context = trim(preg_replace('/\s+/', ' ', $context));
        if (strlen($context) > 500) {
            $context = substr($context, 0, 500) . '...';
        }

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Gemini API Key is missing from your env configuration file.'
            ], 500);
        }

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

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
                $definition = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;
                
                if ($definition) {
                    $cleanDefinition = trim(str_replace(['"', '*'], '', $definition));

                    $savedWord = SavedWord::create([
                        'word' => $word,
                        'definition' => $cleanDefinition,
                        'module_id' => $moduleId,
                        'context' => $context
                    ]);

                    return response()->json([
                        'success' => true,
                        'word' => $word,
                        'definition' => $cleanDefinition
                    ]);
                }
            }

            Log::error('Gemini API Error details: ' . $response->body());
            return response()->json(['success' => false, 'message' => 'Failed to retrieve details from API response.'], 500);

        } catch (\Exception $e) {
            Log::error('Dictionary exception trace: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An internal lookup error occurred.'], 500);
        }
    }

    public function destroy($id)
    {
        $word = SavedWord::findOrFail($id);
        $word->delete();

        return redirect()->back()->with('success', 'Vocabulary word successfully removed.');
    }
}