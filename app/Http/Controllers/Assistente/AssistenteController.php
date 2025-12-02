<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssistenteController extends Controller
{
    public function index()
    {
        return view('assistente.dashboard');
    }
}
