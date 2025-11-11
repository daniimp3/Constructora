<?php

namespace App\Services;

use App\Models\{Proyecto, Presupuesto, AlertaPresupuesto, Notificacion};
use Illuminate\Support\Facades\DB;

/**
 * SERVICIO DE PRESUPUESTO
 * 
 * Contiene la lógica de negocio relacionada con presupuestos,
 * alertas y control de gastos
 */
class ServicioPresupuesto
{
    /**
     * Actualizar el monto actual de un presupuesto
     * Suma todos los gastos asociados al presupuesto
     */
    public function actualizarMontoPresupuesto(Presupuesto $presupuesto)
    {
        // Calcular el total de gastos
        $presupuesto->monto_actual = $presupuesto->gastos()->sum('monto');
        $presupuesto->save();

        // Verificar si se deben crear alertas
        $this->verificarAlertasPresupuesto($presupuesto);
    }

    /**
     * Verificar y crear alertas de presupuesto
     * Crea alertas cuando se alcanza el 90% o 100% del presupuesto
     */
    public function verificarAlertasPresupuesto(Presupuesto $presupuesto)
    {
        $porcentaje = $presupuesto->obtenerPorcentajeVariacion();
        $proyecto = $presupuesto->proyecto;

        // Alerta al 90% (advertencia)
        if ($porcentaje >= 90 && $porcentaje < 100) {
            $this->crearAlerta($proyecto, $presupuesto, 90, $porcentaje, 'advertencia');
        }

        // Alerta al 100% o más (presupuesto excedido)
        if ($porcentaje >= 100) {
            $this->crearAlerta($proyecto, $presupuesto, 100, $porcentaje, 'excedido');
            $this->notificarAdministrador($proyecto, $presupuesto, $porcentaje);
        }
    }

    /**
     * Crear una alerta de presupuesto
     * Evita crear alertas duplicadas
     */
    private function crearAlerta($proyecto, $presupuesto, $umbral, $actual, $tipo)
    {
        // Verificar si ya existe una alerta similar sin leer
        $existe = AlertaPresupuesto::where('id_presupuesto', $presupuesto->id)
            ->where('porcentaje_umbral', $umbral)
            ->where('fue_leida', false)
            ->exists();

        if (!$existe) {
            AlertaPresupuesto::create([
                'id_proyecto' => $proyecto->id,
                'id_presupuesto' => $presupuesto->id,
                'porcentaje_umbral' => $umbral,
                'porcentaje_actual' => $actual,
                'tipo_alerta' => $tipo,
                'mensaje' => $this->obtenerMensajeAlerta($presupuesto, $actual, $tipo),
            ]);
        }
    }

    /**
     * Obtener el mensaje de alerta según el tipo
     */
    private function obtenerMensajeAlerta($presupuesto, $porcentaje, $tipo)
    {
        if ($tipo === 'advertencia') {
            return "⚠️ El presupuesto '{$presupuesto->concepto}' ha alcanzado el {$porcentaje}% de utilización";
        }

        return "🚨 ¡ALERTA! El presupuesto '{$presupuesto->concepto}' ha excedido el límite ({$porcentaje}%)";
    }

    /**
     * Notificar al administrador cuando se excede un presupuesto
     */
    private function notificarAdministrador($proyecto, $presupuesto, $porcentaje)
    {
        Notificacion::create([
            'id_usuario' => $proyecto->id_administrador,
            'tipo' => 'presupuesto_excedido',
            'titulo' => 'Presupuesto Excedido',
            'mensaje' => "El presupuesto '{$presupuesto->concepto}' del proyecto '{$proyecto->nombre}' ha excedido el límite ({$porcentaje}%)",
            'datos' => [
                'id_proyecto' => $proyecto->id,
                'id_presupuesto' => $presupuesto->id,
                'porcentaje' => $porcentaje,
            ],
        ]);
    }

    /**
     * Generar reporte de variación de presupuesto
     * Compara el presupuesto estimado vs el gasto actual
     */
    public function generarReporteVariacion(Proyecto $proyecto)
    {
        return $proyecto->presupuestos->map(function($presupuesto) {
            return [
                'categoria' => $presupuesto->categoria,
                'concepto' => $presupuesto->concepto,
                'estimado' => $presupuesto->monto_estimado,
                'actual' => $presupuesto->monto_actual,
                'variacion' => $presupuesto->obtenerVariacion(),
                'porcentaje_variacion' => $presupuesto->obtenerPorcentajeVariacion(),
                'estado' => $this->obtenerEstadoVariacion($presupuesto->obtenerPorcentajeVariacion()),
            ];
        });
    }

    /**
     * Obtener el estado de la variación según el porcentaje
     */
    private function obtenerEstadoVariacion($porcentaje)
    {
        if ($porcentaje < 80) return 'excelente';
        if ($porcentaje < 90) return 'bueno';
        if ($porcentaje < 100) return 'alerta';
        return 'excedido';
    }
}
