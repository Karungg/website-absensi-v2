<?php

namespace App\Filament\Exports;

use App\Enum\StatusRequest;
use App\Enum\TypeRequest;
use App\Models\Request;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RequestResourceExporter extends Exporter
{
    protected static ?string $model = Request::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user.name')
                ->label('Nama'),
            ExportColumn::make('type')
                ->label('Kategori Ajuan')
                ->formatStateUsing(function (TypeRequest $state): string {
                    return match ($state) {
                        TypeRequest::Leave => 'Cuti Tahunan',
                        TypeRequest::BigHoliday => 'Cuti Besar',
                        TypeRequest::Sick => 'Cuti Sakit',
                        TypeRequest::ImportantLeave => 'Cuti Penting',
                        TypeRequest::GiveBirth => 'Cuti Melahirkan',
                        TypeRequest::LeaveOutside => 'Cuti Diluar Tanggungan Negara'
                    };
                }),
            ExportColumn::make('start_date')
                ->label('Tanggal Mulai')
                ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->format('d-m-Y')),
            ExportColumn::make('end_date')
                ->label('Tanggal Selesai')
                ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->format('d-m-Y')),
            ExportColumn::make('start_time')
                ->label('Jam Mulai'),
            ExportColumn::make('end_time')
                ->label('Jam Selesai'),
            ExportColumn::make('location')
                ->label('Detail Lokasi'),
            ExportColumn::make('description')
                ->label('Alasan'),
            ExportColumn::make('status')
                ->formatStateUsing(function (StatusRequest $state): string {
                    return match ($state) {
                        StatusRequest::Zero => 'Pending',
                        StatusRequest::One => 'Disetujui Kepala Unit',
                        StatusRequest::Three => 'Disetujui Kepala Balai',
                        StatusRequest::Four => 'Ditolak'
                    };
                }),
            ExportColumn::make('created_at')
                ->label('Dibuat Saat'),
            ExportColumn::make('updated_at')
                ->label('Diupdate Saat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your request resource export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
