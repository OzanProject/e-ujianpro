<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        return view('admin.guide.index');
    }
}
