<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function changeLanguage($locale)
    {
        
        App::setLocale($locale);
        return redirect()->back();
    }
}