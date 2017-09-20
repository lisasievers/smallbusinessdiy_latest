<?php

namespace App\Http\Controllers;

use App\Commonsetting;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{

public function __construct()
  {
  
    // Sharing is caring
    //return view('data' => $data);
    //View::share('data', $data);
  }


    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
}
