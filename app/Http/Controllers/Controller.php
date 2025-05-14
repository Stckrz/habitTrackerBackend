<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @method $this middleware(...$middleware)
 * @method void authorizeResource(string $model, string $parameter = null, array $options = [])
 */
abstract class Controller
{
        use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

}
