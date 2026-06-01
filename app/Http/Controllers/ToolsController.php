<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ToolsController extends Controller
{
    public function nameGenerator()
    {
        return view('tools.name-generator.index');
    }

    public function welcomeMessage()
    {
        return view('tools.welcome-message.index');
    }

    public function linkValidator()
    {
        return view('tools.link-validator.index');
    }

    public function pollGenerator()
    {
        return view('tools.poll-generator.index');
    }

    public function raffleGenerator()
    {
        return view('tools.raffle-generator.index');
    }

    public function spamDetector()
    {
        return view('tools.spam-detector.index');
    }

    public function analyzeSpam(\Illuminate\Http\Request $request, \App\Services\SpamDetectorService $service)
    {
        $request->validate([
            'message' => 'required|string|max:10000',
        ]);

        $result = $service->analyze($request->input('message'));

        return response()->json($result);
    }

    public function fontsGenerator()
    {
        return view('tools.fonts-generator.index');
    }
}
