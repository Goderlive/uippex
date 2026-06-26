<?php

namespace App\Filament\Resources\AuxiliarySectors\Pages;

use App\Filament\Resources\AuxiliarySectors\AuxiliarySectorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAuxiliarySectors extends ManageRecords
{
    protected static string $resource = AuxiliarySectorResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            CreateAction::make(),
        ];

        // Protección Open-Core IP
        if (class_exists(\App\Filament\Imports\AuxiliarySectorImporter::class)) {
            $actions[] = \Filament\Actions\Action::make('downloadTemplate')
                ->label('Descargar Plantilla')
                ->color('gray')
                ->icon('heroicon-o-document-text')
                ->action(function () {
                    $headers = ['code', 'year', 'name'];
                    $callback = function () use ($headers) {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, $headers);
                        fputcsv($file, ['01030101', '2026', 'Nombre Sector Auxiliar']);
                        fclose($file);
                    };
                    return response()->streamDownload($callback, 'plantilla_sectores_auxiliares.csv');
                });

            $actions[] = \Filament\Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\AuxiliarySectorImporter::class)
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray');
        }

        return $actions;
    }
}
