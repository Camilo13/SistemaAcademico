<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BoletinSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // LIMPIEZA PREVIA — evita errores si el seeder se corre
        // más de una vez. Orden inverso por foreign keys.
        // ============================================================
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('notas')->truncate();
        DB::table('asistencias')->truncate();
        DB::table('inscripcion_materias')->truncate();
        DB::table('inscripciones')->truncate();
        DB::table('asignaciones')->truncate();
        DB::table('horarios')->truncate();
        DB::table('materias')->truncate();
        DB::table('grupos')->truncate();
        DB::table('grados')->truncate();
        DB::table('periodos')->truncate();
        DB::table('anios_lectivos')->truncate();
        DB::table('sedes')->truncate();

        // Eliminar solo los usuarios del seeder (no el admin)
        DB::table('users')
            ->whereIn('identificacion', [
                '1061234567','1061234568','1061234569','1061234570',
                '1061234571','1061234572','1061234573','1061890001',
            ])
            ->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ============================================================
        // 1. SEDE
        // ============================================================
        $sedeId = DB::table('sedes')->insertGetId([
            'codigo'     => 'AUY-001',
            'nombre'     => 'Sede Principal — Vereda El Lago',
            'direccion'  => 'Vereda El Lago, Resguardo Indígena La Gaitana, Inzá, Cauca',
            'telefono'   => '3142567890',
            'activa'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ============================================================
        // 2. AÑO LECTIVO
        // ============================================================
        $anioId = DB::table('anios_lectivos')->insertGetId([
            'nombre'       => '2025',
            'fecha_inicio' => '2025-02-03',
            'fecha_fin'    => '2025-11-28',
            'activo'       => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // ============================================================
        // 3. PERIODOS
        // ============================================================
        $periodo1Id = DB::table('periodos')->insertGetId([
            'anio_lectivo_id' => $anioId,
            'numero'          => 1,
            'nombre'          => 'Primer Periodo',
            'fecha_inicio'    => '2025-02-03',
            'fecha_fin'       => '2025-04-25',
            'abierto'         => false,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $periodo2Id = DB::table('periodos')->insertGetId([
            'anio_lectivo_id' => $anioId,
            'numero'          => 2,
            'nombre'          => 'Segundo Periodo',
            'fecha_inicio'    => '2025-04-28',
            'fecha_fin'       => '2025-07-18',
            'abierto'         => false,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $periodo3Id = DB::table('periodos')->insertGetId([
            'anio_lectivo_id' => $anioId,
            'numero'          => 3,
            'nombre'          => 'Tercer Periodo',
            'fecha_inicio'    => '2025-07-21',
            'fecha_fin'       => '2025-11-28',
            'abierto'         => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // ============================================================
        // 4. DOCENTES — campo 'firma' incluido (nullable en BD)
        // ============================================================
        $docentesData = [
            ['nombre' => 'Carlos Alberto', 'apellidos' => 'Muñoz Fernández',  'identificacion' => '1061234567', 'correo' => 'carlos.munoz@akweuusyat.edu.co'],
            ['nombre' => 'Luz Marina',     'apellidos' => 'Tombé Ipia',       'identificacion' => '1061234568', 'correo' => 'luz.tombe@akweuusyat.edu.co'],
            ['nombre' => 'Hernando',       'apellidos' => 'Anacona Pilimué',  'identificacion' => '1061234569', 'correo' => 'hernando.anacona@akweuusyat.edu.co'],
            ['nombre' => 'Rosa Elena',     'apellidos' => 'Güetio Chilito',   'identificacion' => '1061234570', 'correo' => 'rosa.guetio@akweuusyat.edu.co'],
            ['nombre' => 'Alirio',         'apellidos' => 'Secué Trochez',    'identificacion' => '1061234571', 'correo' => 'alirio.secue@akweuusyat.edu.co'],
            ['nombre' => 'Marleny',        'apellidos' => 'Avirama Achipiz',  'identificacion' => '1061234572', 'correo' => 'marleny.avirama@akweuusyat.edu.co'],
            ['nombre' => 'Wilson',         'apellidos' => 'Lame Quiñonez',    'identificacion' => '1061234573', 'correo' => 'wilson.lame@akweuusyat.edu.co'],
        ];

        $docenteIds = [];
        foreach ($docentesData as $index => $doc) {
            $docenteIds[] = DB::table('users')->insertGetId(array_merge($doc, [
                'ubicacion'  => 'Inzá, Cauca',
                'contacto'   => '315678901' . $index,
                'password'   => Hash::make('Docente2025*'),
                'rol'        => 'docente',
                'activo'     => true,
                'firma'      => null,          // ← campo que faltaba
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ============================================================
        // 5. ESTUDIANTE
        // ============================================================
        $estudianteId = DB::table('users')->insertGetId([
            'nombre'         => 'Yuliana Paola',
            'apellidos'      => 'Tróchez Avirama',
            'identificacion' => '1061890001',
            'correo'         => 'yuliana.trochez@estudiante.akweuusyat.edu.co',
            'ubicacion'      => 'Resguardo La Gaitana, Inzá, Cauca',
            'contacto'       => '3001234561',
            'password'       => Hash::make('Estudiante2025*'),
            'rol'            => 'estudiante',
            'activo'         => true,
            'firma'          => null,          // ← campo que faltaba
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // ============================================================
        // 6. GRADO
        // ============================================================
        $gradoId = DB::table('grados')->insertGetId([
            'sede_id'     => $sedeId,
            'nombre'      => 'Séptimo',
            'nivel'       => 7,
            'tipo'        => 'Básica Secundaria',
            'activo'      => true,
            'director_id' => $docenteIds[0],
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // ============================================================
        // 7. GRUPO
        // ============================================================
        $grupoId = DB::table('grupos')->insertGetId([
            'grado_id'        => $gradoId,
            'anio_lectivo_id' => $anioId,
            'nombre'          => 'A',
            'cupo_maximo'     => 30,
            'activo'          => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // ============================================================
        // 8. MATERIAS — códigos únicos por grado, no null
        // ============================================================
        $materiasData = [
            ['codigo' => 'MAT-701', 'nombre' => 'Matemáticas',        'intensidad_horaria' => 5],
            ['codigo' => 'LEN-701', 'nombre' => 'Lengua Castellana',   'intensidad_horaria' => 5],
            ['codigo' => 'NAT-701', 'nombre' => 'Ciencias Naturales',  'intensidad_horaria' => 4],
            ['codigo' => 'SOC-701', 'nombre' => 'Ciencias Sociales',   'intensidad_horaria' => 4],
            ['codigo' => 'EDF-701', 'nombre' => 'Educación Física',    'intensidad_horaria' => 2],
            ['codigo' => 'ART-701', 'nombre' => 'Educación Artística', 'intensidad_horaria' => 2],
            ['codigo' => 'ETI-701', 'nombre' => 'Ética y Valores',     'intensidad_horaria' => 2],
        ];

        $materiaIds = [];
        foreach ($materiasData as $mat) {
            $materiaIds[] = DB::table('materias')->insertGetId(array_merge($mat, [
                'grado_id'    => $gradoId,
                'descripcion' => null,
                'tipo'        => 'normal',
                'activa'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]));
        }

        // ============================================================
        // 9. ASIGNACIONES
        // ============================================================
        $asignacionIds = [];
        foreach ($materiaIds as $index => $matId) {
            $asignacionIds[$matId] = DB::table('asignaciones')->insertGetId([
                'docente_id'  => $docenteIds[$index],
                'materia_id'  => $matId,
                'grupo_id'    => $grupoId,
                'activa'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ============================================================
        // 10. INSCRIPCIÓN
        // ============================================================
        $inscripcionId = DB::table('inscripciones')->insertGetId([
            'estudiante_id'     => $estudianteId,
            'grupo_id'          => $grupoId,
            'estado'            => 'activo',
            'fecha_inscripcion' => '2025-02-03',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // ============================================================
        // 11. INSCRIPCION_MATERIAS
        // ============================================================
        $inscripcionMateriaIds = [];
        foreach ($materiaIds as $matId) {
            $inscripcionMateriaIds[$matId] = DB::table('inscripcion_materias')->insertGetId([
                'inscripcion_id' => $inscripcionId,
                'asignacion_id'  => $asignacionIds[$matId],
                'grupo_id'       => $grupoId,
                'estado'         => 'activa',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // ============================================================
        // 12. NOTAS — 7 materias × 3 periodos = 21 registros
        // ============================================================
        $notasPorMateria = [
            0 => [4.5, 4.8, 4.6], // Matemáticas
            1 => [4.7, 4.9, 5.0], // Lengua Castellana
            2 => [4.3, 4.6, 4.7], // Ciencias Naturales
            3 => [4.8, 4.5, 4.9], // Ciencias Sociales
            4 => [5.0, 5.0, 4.8], // Educación Física
            5 => [4.6, 4.7, 4.8], // Educación Artística
            6 => [4.9, 5.0, 4.7], // Ética y Valores
        ];

        $periodos = [$periodo1Id, $periodo2Id]; // Periodo 3 queda pendiente (sin notas)

        foreach ($materiaIds as $matIndex => $matId) {
            $inscMatId = $inscripcionMateriaIds[$matId];
            foreach ($periodos as $perIndex => $perId) {
                DB::table('notas')->insert([
                    'inscripcion_materia_id' => $inscMatId,
                    'periodo_id'             => $perId,
                    'nota'                   => $notasPorMateria[$matIndex][$perIndex],
                    'observacion'            => null,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);
            }
        }
    }
}