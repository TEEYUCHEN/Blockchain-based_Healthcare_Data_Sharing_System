<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\View;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $abi = json_decode(
            file_get_contents(storage_path('blockchain/AccessControlABI.json')),
            true
        );

        View::share('contractAddress', env('CONTRACT_ADDRESS'));
        View::share('contractABI', $abi);
    }
}
