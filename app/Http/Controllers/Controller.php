<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/*
|--------------------------------------------------------------------------
| Controlador Base
|--------------------------------------------------------------------------
| Todos los controladores de la aplicación extienden de esta clase.
| Aquí se pueden centralizar comportamientos comunes.
*/

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
