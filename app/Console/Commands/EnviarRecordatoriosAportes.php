<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioAporteMailable;
use App\Models\Socio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatoriosAportes extends Command
{
    /**
     * El nombre para ejecutar el comando.
     */
    protected $signature = 'aportes:enviar-recordatorios';

    /**
     * Descripción del comando.
     */
    protected $description = 'Busca socios cuyo último pago vence a fin de mes y envía correo';

    /**
     * Lógica principal.
     */
    public function handle()
    {
        // ==========================================
        // PASO 1: EL GUARDIA (Validación de Fecha)
        // ==========================================

        $hoy = now();
        $finDeMes = $hoy->copy()->endOfMonth();

        // Avisamos 3 días antes del fin de mes.
        $fechaObjetivo = $finDeMes->copy()->subDays(3);

        // Si HOY no es la fecha objetivo, terminamos para no gastar recursos.
        if (! $hoy->isSameDay($fechaObjetivo)) {
            // return 0; // <--- DESCOMENTAR ESTO EN PRODUCCIÓN
            // Para pruebas hoy (28 dic), lo dejamos pasar.
        }

        $this->info('Hoy es fecha de corte. Iniciando búsqueda para vencimientos al: '.$finDeMes->toDateString());

        // ==========================================
        // PASO 2: LA CONSULTA (El Filtro Inteligente)
        // ==========================================

        $query = Socio::where('estado', 'Activo') // <--- 1. CORRECCIÓN: Mayúscula 'Activo'

            // CONDICIÓN A: Debe tener un pago que venza ESTE mes.
            ->whereHas('pagos', function ($q) use ($finDeMes) {
                $q->whereDate('periodo_fin', $finDeMes->toDateString());
            })

            // CONDICIÓN B (La Corrección Clave):
            // Excluir SOLO si ya tiene un pago FUTURO que esté 'Aprobado'.
            // Si tiene un futuro 'Pendiente', igual le enviamos el correo.
            ->whereDoesntHave('pagos', function ($q) use ($finDeMes) {
                $q->whereDate('periodo_fin', '>', $finDeMes->toDateString())
                    ->whereIn('estado', ['Aprobado', 'Pagado']); // <--- 2. CORRECCIÓN: Filtramos por estado válido
            })

            ->select(['id', 'nombre', 'email']);

        // ==========================================
        // VERIFICACIÓN EN CONSOLA
        // ==========================================
        $cantidad = $query->count();
        $this->info('Socios encontrados para notificar: '.$cantidad);

        // ==========================================
        // PASO 3: EL ENVÍO (Chunking + Cola)
        // ==========================================

        $query->chunk(100, function ($socios) use ($finDeMes) {
            foreach ($socios as $socio) {
                if (filter_var($socio->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        // Enviamos a la cola (Database Queue)
                        Mail::to($socio->email)
                            ->send(new RecordatorioAporteMailable($socio, $finDeMes->translatedFormat('F')));

                        // Opcional: Feedback visual en consola de prueba
                        // $this->line("Encolado: {$socio->email}");

                    } catch (\Exception $e) {
                        Log::error("Error procesando socio ID {$socio->id}: ".$e->getMessage());
                    }
                }
            }
        });

        $this->info('Proceso finalizado. Correos encolados en la base de datos.');

        return 0;
    }
}
