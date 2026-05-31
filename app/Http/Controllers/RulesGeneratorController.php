<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RulesGeneratorController extends Controller
{
    public function index()
    {
        return view('tools.rules-generator.index');
    }
}
