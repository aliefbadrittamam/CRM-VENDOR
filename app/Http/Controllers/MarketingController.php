<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MarketingController extends Controller
{
    // public function index()
  
    public function whatsapp()
    {
        return view('marketing.index');
    }
    public function leads()
    {
        return view('marketing.lead');
    }
    public function analysis()
    {
        return view('marketing.analysis');
    }

    
}
