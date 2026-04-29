<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| IMPORTACIÓN DE CONTROLADORES
|--------------------------------------------------------------------------
*/

// ── Públicos ──────────────────────────────────────────────────────────────
use App\Http\Controllers\public\PublicController;

// ── Autenticación ─────────────────────────────────────────────────────────
use App\Http\Controllers\Auth\LoginController;

// ── Dashboards ────────────────────────────────────────────────────────────
use App\Http\Controllers\Modulos\Dashboard\AdminController;
use App\Http\Controllers\Modulos\Dashboard\DocenteController;
use App\Http\Controllers\Modulos\Dashboard\EstudianteController;

// ── Perfil ────────────────────────────────────────────────────────────────
use App\Http\Controllers\Modulos\Perfil\PerfilController;

// ── Solicitudes ───────────────────────────────────────────────────────────
use App\Http\Controllers\Modulos\Solicitudes\SolicitudController;
use App\Http\Controllers\Modulos\Solicitudes\GestionController;

// ── Usuarios (gestión admin) ───────────────────────────────────────────────
use App\Http\Controllers\Modulos\Admin\UsuarioController;

// ── Biblioteca Digital — Lectura (docente + estudiante) ───────────────────
use App\Http\Controllers\Modulos\Biblioteca\Lectura\MateriaController as LecturaMateriaController;
use App\Http\Controllers\Modulos\Biblioteca\Lectura\RecursoController as LecturaRecursoController;

// ── Biblioteca Digital — Gestión (admin) ──────────────────────────────────
use App\Http\Controllers\Modulos\Biblioteca\Gestion\MateriaController as AdminMateriaController;
use App\Http\Controllers\Modulos\Biblioteca\Gestion\RecursoController as AdminRecursoController;

// ── Portal Público (admin) ────────────────────────────────────────────────
use App\Http\Controllers\Modulos\Portal\CarruselController;
use App\Http\Controllers\Modulos\Portal\EventosController;

// ── Académico — Estructura (admin) ────────────────────────────────────────
use App\Http\Controllers\Modulos\Academico\Estructura\SedeController;
use App\Http\Controllers\Modulos\Academico\Estructura\GradoController;
use App\Http\Controllers\Modulos\Academico\Estructura\GrupoController;
use App\Http\Controllers\Modulos\Academico\Estructura\MateriaController;

// ── Académico — Gestión (admin) ───────────────────────────────────────────
use App\Http\Controllers\Modulos\Academico\AnioController;
use App\Http\Controllers\Modulos\Academico\PeriodoController;
use App\Http\Controllers\Modulos\Academico\AsignacionController;
use App\Http\Controllers\Modulos\Academico\InscripcionController;
use App\Http\Controllers\Modulos\Academico\InscripcionMateriaController;
use App\Http\Controllers\Modulos\Academico\NotaController;
use App\Http\Controllers\Modulos\Academico\BoletinController;

// ── Horarios ───────────────────────────────────────────────────────────────
use App\Http\Controllers\Modulos\Academico\HorarioController;
use App\Http\Controllers\Modulos\Docente\HorarioDocenteController;
use App\Http\Controllers\Modulos\Estudiante\HorarioEstudianteController;

// ── Docente ───────────────────────────────────────────────────────────────
use App\Http\Controllers\Modulos\Docente\MisGruposController;
use App\Http\Controllers\Modulos\Docente\NotaDocenteController;
use App\Http\Controllers\Modulos\Docente\ResumenGrupoController;

// ── Estudiante ────────────────────────────────────────────────────────────
use App\Http\Controllers\Modulos\Estudiante\NotaEstudianteController;
use App\Http\Controllers\Modulos\Estudiante\BoletinEstudianteController;
use App\Http\Controllers\Modulos\Docente\AsistenciaDocenteController;
use App\Http\Controllers\Modulos\Estudiante\AsistenciaEstudianteController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::controller(PublicController::class)->group(function () {
    Route::get('/', 'inicio')->name('inicio');
    Route::get('/sobre-nosotros', 'sobreNosotros')->name('sobrenosotros');
    Route::get('/contacto', 'contacto')->name('contacto');
    Route::get('/eventos', 'eventos')->name('public.eventos');
});

/*
|--------------------------------------------------------------------------
| SOLICITUD PÚBLICA DE REGISTRO
|--------------------------------------------------------------------------
*/
Route::get('/registro', [SolicitudController::class, 'create'])
    ->name('solicitud.create');

Route::post('/registro', [SolicitudController::class, 'store'])
    ->name('solicitud.store');

Route::post('/registro/validar', [SolicitudController::class, 'validarCampo'])
    ->name('solicitud.validar');

/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| PERFIL DE USUARIO
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/perfil', [PerfilController::class, 'perfil'])
        ->name('perfil');

    Route::put('/perfil', [PerfilController::class, 'update'])
        ->name('perfil.update');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD — ADMINISTRADOR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminController::class, 'index'])
            ->name('dashboard');

        /*
        |----------------------------------------------------------------------
        | SOLICITUDES
        |----------------------------------------------------------------------
        */
Route::controller(GestionController::class)
    ->prefix('solicitudes')
    ->name('solicitudes.')
    ->group(function () {

        Route::get('/',                       'index')   ->name('index');
        Route::get('/{solicitud}/edit',       'edit')    ->name('edit');
        Route::put('/{solicitud}',            'update')  ->name('update');
        Route::post('/{solicitud}/aprobar',   'aprobar') ->name('aprobar');
        Route::post('/{solicitud}/rechazar',  'rechazar')->name('rechazar');
    });
/*
|----------------------------------------------------------------------
| GESTIÓN DE USUARIOS
|----------------------------------------------------------------------
*/
 Route::controller(UsuarioController::class)
    ->prefix('usuarios')
    ->name('usuarios.')
    ->group(function () {

        Route::get('/',                        'index')       ->name('index');
        Route::get('/create',                  'create')      ->name('create');
        Route::post('/',                       'store')       ->name('store');
        Route::delete('/bulk',                 'destroyBulk') ->name('destroyBulk');

        Route::get('/{usuario}/edit',          'edit')        ->name('edit');
        Route::put('/{usuario}',               'update')      ->name('update');
        Route::patch('/{usuario}/activar',     'activar')     ->name('activar');
        Route::patch('/{usuario}/desactivar',  'desactivar')  ->name('desactivar');
        Route::patch('/{usuario}/password',    'password')    ->name('password');
        Route::delete('/{usuario}',            'destroy')     ->name('destroy');
    });

    });

/*
|--------------------------------------------------------------------------
| DASHBOARD — DOCENTE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:docente'])
    ->group(function () {

        Route::get('/docente', [DocenteController::class, 'index'])
            ->name('docente.dashboard');
    });

/*
|--------------------------------------------------------------------------
| DASHBOARD — ESTUDIANTE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:estudiante'])
    ->group(function () {

        Route::get('/estudiante', [EstudianteController::class, 'index'])
            ->name('estudiante.dashboard');
    });

/*
|--------------------------------------------------------------------------
| BIBLIOTECA DIGITAL — LECTURA (docente + estudiante)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:docente,estudiante'])
    ->prefix('biblioteca')
    ->name('biblioteca.')
    ->group(function () {

        Route::get('materias', [LecturaMateriaController::class, 'index'])
            ->name('materias.index');

        Route::get('materias/{materia}/recursos', [LecturaRecursoController::class, 'index'])
            ->name('materias.recursos.index');

    });

/*
|--------------------------------------------------------------------------
| BIBLIOTECA DIGITAL — GESTIÓN (admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin/biblioteca')
    ->name('admin.biblioteca.')
    ->group(function () {

        // ── Materias ──────────────────────────────────────────────
        Route::get('materias',                        [AdminMateriaController::class, 'index'])   ->name('materias.index');
        Route::get('materias/create',                 [AdminMateriaController::class, 'create'])  ->name('materias.create');
        Route::post('materias',                       [AdminMateriaController::class, 'store'])   ->name('materias.store');

        // activar/desactivar ANTES de {materia}
        Route::patch('materias/{materia}/activar',    [AdminMateriaController::class, 'activar'])    ->name('materias.activar');
        Route::patch('materias/{materia}/desactivar', [AdminMateriaController::class, 'desactivar']) ->name('materias.desactivar');

        Route::get('materias/{materia}/edit',         [AdminMateriaController::class, 'edit'])    ->name('materias.edit');
        Route::put('materias/{materia}',              [AdminMateriaController::class, 'update'])  ->name('materias.update');
        Route::delete('materias/{materia}',           [AdminMateriaController::class, 'destroy']) ->name('materias.destroy');

        // ── Recursos ──────────────────────────────────────────────
        Route::get('materias/{materia}/recursos',                         [AdminRecursoController::class, 'index'])   ->name('materias.recursos.index');
        Route::get('materias/{materia}/recursos/create',                  [AdminRecursoController::class, 'create'])  ->name('materias.recursos.create');
        Route::post('materias/{materia}/recursos',                        [AdminRecursoController::class, 'store'])   ->name('materias.recursos.store');
        Route::get('materias/{materia}/recursos/{recurso}/edit',          [AdminRecursoController::class, 'edit'])    ->name('materias.recursos.edit');
        Route::put('materias/{materia}/recursos/{recurso}',               [AdminRecursoController::class, 'update'])     ->name('materias.recursos.update');
        Route::patch('materias/{materia}/recursos/{recurso}/activar',    [AdminRecursoController::class, 'activar'])    ->name('materias.recursos.activar');
        Route::patch('materias/{materia}/recursos/{recurso}/desactivar', [AdminRecursoController::class, 'desactivar']) ->name('materias.recursos.desactivar');
        Route::delete('materias/{materia}/recursos/{recurso}',            [AdminRecursoController::class, 'destroy'])   ->name('materias.recursos.destroy');
    });


/*
|--------------------------------------------------------------------------
| CARRUSEL — ADMINISTRACIÓN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin/carrusel')
    ->name('admin.carrusel.')
    ->group(function () {

        Route::get('/',                          [CarruselController::class, 'index'])     ->name('index');
        Route::get('/create',                    [CarruselController::class, 'create'])    ->name('create');
        Route::post('/',                         [CarruselController::class, 'store'])     ->name('store');

        // activar/desactivar ANTES de {carrusel}
        Route::patch('/{carrusel}/activar',      [CarruselController::class, 'activar'])   ->name('activar');
        Route::patch('/{carrusel}/desactivar',   [CarruselController::class, 'desactivar'])->name('desactivar');

        Route::get('/{carrusel}/edit',           [CarruselController::class, 'edit'])      ->name('edit');
        Route::put('/{carrusel}',                [CarruselController::class, 'update'])    ->name('update');
        Route::delete('/{carrusel}',             [CarruselController::class, 'destroy'])   ->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| EVENTOS — ADMINISTRACIÓN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin/eventos')
    ->name('admin.eventos.')
    ->group(function () {

        Route::get('/',                        [EventosController::class, 'index'])     ->name('index');
        Route::get('/create',                  [EventosController::class, 'create'])    ->name('create');
        Route::post('/',                       [EventosController::class, 'store'])     ->name('store');

        // activar/desactivar ANTES de {evento}
        Route::patch('/{evento}/activar',      [EventosController::class, 'activar'])   ->name('activar');
        Route::patch('/{evento}/desactivar',   [EventosController::class, 'desactivar'])->name('desactivar');

        Route::get('/{evento}/edit',           [EventosController::class, 'edit'])      ->name('edit');
        Route::put('/{evento}',                [EventosController::class, 'update'])    ->name('update');
        Route::delete('/{evento}',             [EventosController::class, 'destroy'])   ->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| ACADÉMICO — ADMINISTRACIÓN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin/academico')
    ->name('admin.academico.')
    ->group(function () {

        /*
        |----------------------------------------------------------------------
        | SEDES
        |----------------------------------------------------------------------
        */
        Route::controller(SedeController::class)
            ->prefix('estructura/sedes')
            ->name('sedes.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{sede}/edit', 'edit')->name('edit');
                Route::put('/{sede}', 'update')->name('update');
                Route::patch('/{sede}/activar', 'activar')->name('activar');
                Route::patch('/{sede}/desactivar', 'desactivar')->name('desactivar');
                Route::delete('/{sede}', 'destroy')->name('destroy');
            });

        /*
        |----------------------------------------------------------------------
        | GRADOS
        |----------------------------------------------------------------------
        */
        Route::resource('grados', GradoController::class);
        Route::patch('grados/{grado}/activar', [GradoController::class, 'activar'])->name('grados.activar');
        Route::patch('grados/{grado}/desactivar', [GradoController::class, 'desactivar'])->name('grados.desactivar');

        /*
        |----------------------------------------------------------------------
        | GRUPOS
        |----------------------------------------------------------------------
        */
        Route::controller(GrupoController::class)
            ->prefix('estructura/grupos')
            ->name('estructura.grupos.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{grupo}/edit', 'edit')->name('edit');
                Route::put('/{grupo}', 'update')->name('update');
                Route::patch('/{grupo}/activar', 'activar')->name('activar');
                Route::patch('/{grupo}/desactivar', 'desactivar')->name('desactivar');
                Route::delete('/{grupo}', 'destroy')->name('destroy');
            });

        /*
        |----------------------------------------------------------------------
        | MATERIAS ACADÉMICAS (estructura)
        |----------------------------------------------------------------------
        */
        Route::prefix('estructura')
            ->name('estructura.')
            ->group(function () {

                Route::resource('materias', MateriaController::class);
                Route::patch('materias/{materia}/activar', [MateriaController::class, 'activar'])->name('materias.activar');
                Route::patch('materias/{materia}/desactivar', [MateriaController::class, 'desactivar'])->name('materias.desactivar');
            });

        /*
        |----------------------------------------------------------------------
        | AÑOS LECTIVOS + PERIODOS
        |----------------------------------------------------------------------
        */
        Route::controller(AnioController::class)
            ->prefix('anios')
            ->name('anios.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{anio}/edit', 'edit')->name('edit');
                Route::put('/{anio}', 'update')->name('update');
                Route::patch('/{anio}/activar', 'activar')->name('activar');
                Route::delete('/{anio}', 'destroy')->name('destroy');

                Route::prefix('{anioLectivo}/periodos')
                    ->name('periodos.')
                    ->controller(PeriodoController::class)
                    ->group(function () {

                        Route::get('/', 'index')->name('index');
                        Route::get('/create', 'create')->name('create');
                        Route::post('/', 'store')->name('store');
                        Route::get('/{periodo}/edit', 'edit')->name('edit');
                        Route::put('/{periodo}', 'update')->name('update');
                        Route::patch('/{periodo}/cerrar', 'cerrar')->name('cerrar');
                        Route::patch('/{periodo}/reabrir', 'reabrir')->name('reabrir');
                        Route::delete('/{periodo}', 'destroy')->name('destroy');
                    });
            });

        /*
        |----------------------------------------------------------------------
        | ASIGNACIONES
        |----------------------------------------------------------------------
        */
        Route::controller(AsignacionController::class)
            ->prefix('asignaciones')
            ->name('asignaciones.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{asignacion}/edit', 'edit')->name('edit');
                Route::put('/{asignacion}', 'update')->name('update');
                Route::patch('/{asignacion}/activar', 'activar')->name('activar');
                Route::patch('/{asignacion}/desactivar', 'desactivar')->name('desactivar');
                Route::delete('/{asignacion}', 'destroy')->name('destroy');
            });

        /*
        |----------------------------------------------------------------------
        | INSCRIPCIONES + MATERIAS DE INSCRIPCIÓN
        |----------------------------------------------------------------------
        */
        Route::controller(InscripcionController::class)
            ->prefix('inscripciones')
            ->name('inscripciones.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{inscripcion}/edit', 'edit')->name('edit');
                Route::put('/{inscripcion}', 'update')->name('update');
                Route::patch('/{inscripcion}/retirar', 'retirar')->name('retirar');
                Route::patch('/{inscripcion}/finalizar', 'finalizar')->name('finalizar');
                Route::delete('/{inscripcion}', 'destroy')->name('destroy');

                Route::prefix('{inscripcion}/materias')
                    ->name('materias.')
                    ->controller(InscripcionMateriaController::class)
                    ->group(function () {

                        Route::get('/', 'index')->name('index');
                        Route::get('/create', 'create')->name('create');
                        Route::post('/', 'store')->name('store');
                        Route::patch('/{inscripcionMateria}/retirar', 'retirar')->name('retirar');
                        Route::delete('/{inscripcionMateria}', 'destroy')->name('destroy');
                    });
            });

        /*
        |----------------------------------------------------------------------
        | NOTAS (anidadas bajo inscripcion_materia)
        |----------------------------------------------------------------------
        */
        Route::prefix('inscripcion-materias/{inscripcionMateria}/notas')
            ->name('notas.')
            ->controller(NotaController::class)
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{nota}/edit', 'edit')->name('edit');
                Route::put('/{nota}', 'update')->name('update');
                Route::delete('/{nota}', 'destroy')->name('destroy');
            });

        /*
        |----------------------------------------------------------------------
        | BOLETÍN (admin — cualquier inscripción)
        |----------------------------------------------------------------------
        */
        Route::prefix('boletin')
            ->name('boletin.')
            ->controller(BoletinController::class)
            ->group(function () {

                Route::get('/{inscripcion}', 'show')->name('show');
                Route::get('/{inscripcion}/pdf', 'exportarPdf')->name('pdf');
            });

        /*
        |----------------------------------------------------------------------
        | HORARIOS (admin — gestión por grupo)
        |----------------------------------------------------------------------
        */
        Route::controller(HorarioController::class)
            ->prefix('horarios')
            ->name('horarios.')
            ->group(function () {

                Route::get('/',                          'index')   ->name('index');
                Route::get('/grupo/{grupo}',             'grupo')   ->name('grupo');
                Route::get('/create/{grupo}',            'create')  ->name('create');
                Route::post('/',                         'store')   ->name('store');
                Route::delete('/{horario}',              'destroy') ->name('destroy');
            });

    });

/*
|--------------------------------------------------------------------------
| MÓDULO DOCENTE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:docente'])
    ->prefix('docente')
    ->name('docente.')
    ->group(function () {

        /*
        |----------------------------------------------------------------------
        | MIS GRUPOS
        | Lista las asignaciones activas del docente autenticado.
        |----------------------------------------------------------------------
        */
        Route::get('grupos', [MisGruposController::class, 'index'])
            ->name('grupos.index');

        /*
        |----------------------------------------------------------------------
        | NOTAS — DOCENTE
        | Flujo: mis asignaciones → estudiantes del grupo → registrar nota
        |----------------------------------------------------------------------
        */
        Route::controller(NotaDocenteController::class)
            ->prefix('notas')
            ->name('notas.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/{asignacion}/estudiantes', 'estudiantes')->name('estudiantes');
                Route::get('/{asignacion}/{inscripcionMateria}/create', 'create')->name('create');
                Route::post('/{asignacion}/{inscripcionMateria}', 'store')->name('store');
                Route::get('/{nota}/edit', 'edit')->name('edit');
                Route::put('/{nota}', 'update')->name('update');
                Route::delete('/{nota}', 'destroy')->name('destroy');
                Route::delete('/{asignacion}/{inscripcionMateria}/borrar-notas', 'eliminarNotasEstudiante')->name('eliminarNotasEstudiante');
            });

        /*
        |----------------------------------------------------------------------
        | BOLETÍN — DOCENTE (solo lectura, solo sus grupos)
        |----------------------------------------------------------------------
        */
        /*
        |----------------------------------------------------------------------
        | RESUMEN DE GRUPO — DOCENTE (todos los estudiantes del grupo)
        |----------------------------------------------------------------------
        */
        Route::get('grupos/{grupo}/resumen', [ResumenGrupoController::class, 'show'])
            ->name('grupos.resumen');

        /*
        |----------------------------------------------------------------------
        | ASISTENCIA — DOCENTE
        | Flujo: mis asignaciones → estudiantes → registrar faltas
        |----------------------------------------------------------------------
        */
        Route::controller(AsistenciaDocenteController::class)
            ->prefix('asistencia')
            ->name('asistencia.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/{asignacion}/estudiantes', 'estudiantes')->name('estudiantes');
                Route::get('/{asignacion}/{inscripcionMateria}/create', 'create')->name('create');
                Route::post('/{asignacion}/{inscripcionMateria}', 'store')->name('store');
                Route::get('/{asistencia}/edit', 'edit')->name('edit');
                Route::put('/{asistencia}', 'update')->name('update');
                Route::delete('/{asistencia}', 'destroy')->name('destroy');
            });

        /*
        |----------------------------------------------------------------------
        | HORARIO — DOCENTE (solo lectura)
        |----------------------------------------------------------------------
        */
        Route::get('horario', [HorarioDocenteController::class, 'index'])
            ->name('horario.index');

    });

/*
|--------------------------------------------------------------------------
| MÓDULO ESTUDIANTE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:estudiante'])
    ->prefix('estudiante')
    ->name('estudiante.')
    ->group(function () {

        /*
        |----------------------------------------------------------------------
        | NOTAS — ESTUDIANTE (solo lectura)
        |----------------------------------------------------------------------
        */
        Route::controller(NotaEstudianteController::class)
            ->prefix('notas')
            ->name('notas.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/{periodo}', 'porPeriodo')->name('periodo');
            });

        /*
        |----------------------------------------------------------------------
        | BOLETINES — ESTUDIANTE (año vigente + historial, solo lectura)
        |----------------------------------------------------------------------
        */
        Route::controller(BoletinEstudianteController::class)
            ->prefix('boletines')
            ->name('boletin.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::get('/{inscripcion}', 'show')->name('show');
                Route::get('/{inscripcion}/pdf', 'pdf')->name('pdf');
            });

        /*
        |-------------------------------------------------------------------    ---
        | ASISTENCIA — ESTUDIANTE (solo lectura)
        |----------------------------------------------------------------------
        */
        Route::controller(AsistenciaEstudianteController::class)
            ->prefix("asistencia")
            ->name("asistencia.")
            ->group(function () {

                Route::get("/", "index")->name("index");
                Route::get("/{periodo}", "porPeriodo")->name("periodo");
            });

        /*
        |----------------------------------------------------------------------
        | HORARIO — ESTUDIANTE (solo lectura)
        |----------------------------------------------------------------------
        */
        Route::get('horario', [HorarioEstudianteController::class, 'index'])
            ->name('horario.index');

    });