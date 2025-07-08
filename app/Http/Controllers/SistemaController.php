<?php

namespace App\Http\Controllers;

class SistemaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        return view('Contenido.content');
    }
}
