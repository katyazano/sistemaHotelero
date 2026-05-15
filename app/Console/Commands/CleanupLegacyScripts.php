<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupLegacyScripts extends Command
{
    protected $signature = 'cleanup:legacy-scripts';
    protected $description = 'Elimina scripts PHP sueltos de la raíz (obsoletos)';

    public function handle()
    {
        $scripts = [
            base_path('crear_admin_rapido.php'),
            base_path('actualizar_usuario_existente.php'),
            base_path('reset_demo_env.php'),
        ];

        $eliminados = 0;
        foreach ($scripts as $file) {
            if (File::exists($file)) {
                File::delete($file);
                $this->line("✓ Eliminado: {$file}");
                $eliminados++;
            }
        }

        if ($eliminados === 0) {
            $this->info('No se encontraron scripts legados.');
        } else {
            $this->info("✓ {$eliminados} script(s) eliminado(s).");
        }

        return 0;
    }
}
