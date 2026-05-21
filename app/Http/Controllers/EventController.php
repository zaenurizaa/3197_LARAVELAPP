<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function show($id = null) {
    // Kalau filenya masih di folder layout:
    return view('layout.event-detail'); 
}

public function checkout() {
    return view('layout.checkout');
}

public function ticket() {
    return view('layout.ticket');
}
}
