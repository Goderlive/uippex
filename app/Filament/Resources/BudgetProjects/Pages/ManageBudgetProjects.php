<?php

namespace App\Filament\Resources\BudgetProjects\Pages;

use App\Filament\Resources\BudgetProjects\BudgetProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBudgetProjects extends ManageRecords
{
    protected static string $resource = BudgetProjectResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            CreateAction::make(),
        ];

        // Protección Open-Core IP
        if (class_exists(\App\Filament\Imports\BudgetProjectImporter::class)) {
            $actions[] = \Filament\Actions\Action::make('downloadTemplate')
                ->label('Descargar Plantilla')
                ->color('gray')
                ->icon('heroicon-o-document-text')
                ->action(function () {
                    $headers = ['budgetProgram', 'code', 'year', 'name', 'description'];
                    $callback = function () use ($headers) {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, $headers);
                        fputcsv($file, ['01030101', '010301010101', '2026', 'Nombre Proyecto', 'Descripción de ejemplo']);
                        fclose($file);
                    };
                    return response()->streamDownload($callback, 'plantilla_proyectos.csv');
                });

            $actions[] = \Filament\Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\BudgetProjectImporter::class)
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray');
        }

        return $actions;
    }
}
