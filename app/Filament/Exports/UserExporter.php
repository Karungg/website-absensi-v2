<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('nik')
                ->label('NIK'),
            ExportColumn::make('nip')
                ->label('NIP'),
            ExportColumn::make('name')
                ->label('Nama'),
            ExportColumn::make('email'),
            ExportColumn::make('place_of_birth')
                ->label('Tempat Lahir'),
            ExportColumn::make('date_of_birth')
                ->label('Tanggal Lahir'),
            ExportColumn::make('phone')
                ->label('Nomor Telepon'),
            ExportColumn::make('address')
                ->label('Alamat'),
            ExportColumn::make('leave_allowance')
                ->label('Sisa Cuti Tahunan'),
            ExportColumn::make('sick_allowance')
                ->label('Sisa Cuti Sakit'),
            ExportColumn::make('give_birth_allowance')
                ->label('Sisa Cuti Melahirkan'),
            ExportColumn::make('date_of_entry')
                ->label('Tanggal Masuk'),
            ExportColumn::make('mutation_date')
                ->label('Tanggal Mutasi'),
            ExportColumn::make('position.title')
                ->label('Jabatan'),
            ExportColumn::make('division.title')
                ->label('Divis'),
            ExportColumn::make('created_at')
                ->label('Dibuat Saat'),
            ExportColumn::make('updated_at')
                ->label('Diupdate Saat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
