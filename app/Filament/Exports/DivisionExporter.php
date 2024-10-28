<?php

namespace App\Filament\Exports;

use App\Models\Division;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class DivisionExporter extends Exporter
{
    protected static ?string $model = Division::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('title')
                ->label('Nama Divisi'),
            ExportColumn::make('description')
                ->label('Deskripsi'),
            ExportColumn::make('created_at')
                ->label('Dibuat Saat'),
            ExportColumn::make('updated_at')
                ->label('Diupdate Saat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your division export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
