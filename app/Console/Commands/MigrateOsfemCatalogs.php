<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralSector;
use App\Models\AuxiliarySector;
use App\Models\BudgetProgram;
use App\Models\BudgetProject;

class MigrateOsfemCatalogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cat:migrate-legacy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate OSFEM Catalogs from Legacy MySQL DB to Landlord PostgreSQL DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración de catálogos Landlord (Legacy MySQL -> PostgreSQL)...');

        $year = '2026';

        DB::beginTransaction();

        try {
            $this->warn('Truncando tablas destino (Cascade)...');
            // Usamos TRUNCATE con CASCADE debido a que PostgreSQL cuida la integridad relacional fuertemente
            DB::statement('TRUNCATE TABLE budget_projects CASCADE;');
            DB::statement('TRUNCATE TABLE budget_programs CASCADE;');
            DB::statement('TRUNCATE TABLE auxiliary_sectors CASCADE;');
            DB::statement('TRUNCATE TABLE general_sectors CASCADE;');

            $legacy = DB::connection('legacy_mysql');

            $this->info('Migrando Sector General (dependencias_generales)...');
            $dependenciasGenerales = $legacy->table('dependencias_generales')
                ->where('anio', $year)
                ->where('tipo', 'Municipios')
                ->get();

            foreach ($dependenciasGenerales as $dg) {
                GeneralSector::create([
                    'code' => $dg->clave_dependencia,
                    'name' => $dg->nombre_dependencia_general,
                ]);
            }

            $this->info('Migrando Sector Auxiliar (dependencias_auxiliares)...');
            $dependenciasAuxiliares = $legacy->table('dependencias_auxiliares')
                ->where('anio', $year)
                ->where('tipo', 'Municipios')
                ->get();

            foreach ($dependenciasAuxiliares as $da) {
                AuxiliarySector::create([
                    'code' => $da->clave_dependencia_auxiliar,
                    'name' => $da->nombre_dependencia_auxiliar,
                ]);
            }

            $this->info('Migrando Presupuesto Programas (programas_presupuestarios)...');
            $programas = $legacy->table('programas_presupuestarios')
                ->where('anio', $year)
                ->get();

            $programIdMap = [];
            foreach ($programas as $p) {
                $newProgram = BudgetProgram::create([
                    'code' => $p->codigo_programa,
                    'name' => $p->nombre_programa,
                ]);
                $programIdMap[$p->id_programa] = $newProgram->id; // Memory map logic
            }

            $this->info('Migrando Presupuesto Proyectos (proyectos)...');
            $proyectos = $legacy->table('proyectos')
                ->where('anio', $year)
                ->get();

            foreach ($proyectos as $pr) {
                if (isset($programIdMap[$pr->id_programa])) {
                    BudgetProject::create([
                        'budget_program_id' => $programIdMap[$pr->id_programa],
                        'code' => $pr->codigo_proyecto,
                        'name' => $pr->nombre_proyecto,
                    ]);
                }
            }

            DB::commit();
            $this->info('¡Migración Landlord terminada exitosamente con Strict Types!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Falló la migración: ' . $e->getMessage());
        }
    }
}
