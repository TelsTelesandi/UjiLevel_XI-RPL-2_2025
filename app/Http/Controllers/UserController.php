<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard()
    {
        $totalEvents = \App\Models\EventPengajuan::where('user_id', auth()->id())->count();
        return view('user.dashboard', compact('totalEvents'));
    }
}
