<?php

namespace App\Http\Controllers;

use App\Utils\ExtractFiltreArticle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainController extends Controller
{
    public function index()
    {

        return Inertia::render("Menu/Index", []);
    }
}
