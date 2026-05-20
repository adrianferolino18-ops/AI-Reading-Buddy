<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedWord;
use App\Models\Module;

class DashboardController extends Controller
{
    public function index()
    {
        $words = SavedWord::orderBy('created_at', 'desc')->get();
        $modules = Module::with('savedWords')->get();
        
        return view('dashboard', [
            'words' => $words,
            'savedWords' => $words, 
            'modules' => $modules,
            'totalWordsCount' => $words->count()
        ]);
    }
}