<?php

namespace App\Filament\Resources\BudgetProgramResource\Pages;

use App\Filament\Resources\BudgetProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBudgetPrograms extends ManageRecords
{
    protected static string $resource = BudgetProgramResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make(),
        ];

        // Protección Open-Core IP
        if (class_exists(\App\Filament\Imports\BudgetProgramImporter::class)) {
            $actions[] = \Filament\Actions\Action::make('downloadTemplate')
                ->label('Descargar Plantilla')
                ->color('gray')
                ->icon('heroicon-o-document-text')
                ->action(function () {
                    $headers = ['code', 'year', 'name', 'description'];
                    $callback = function () use ($headers) {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, $headers);
                        fputcsv($file, ['01030101', '2026', 'Nombre Programa', 'Descripción de ejemplo']);
                        fclose($file);
                    };
                    return response()->streamDownload($callback, 'plantilla_programas.csv');
                });

            $actions[] = \Filament\Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\BudgetProgramImporter::class)
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray');
        }

        return $actions;
    }
}
