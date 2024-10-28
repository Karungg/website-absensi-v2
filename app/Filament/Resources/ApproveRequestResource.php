<?php

namespace App\Filament\Resources;

use App\Enum\StatusRequest;
use App\Enum\TypeRequest;
use App\Filament\Exports\RequestExporter;
use App\Filament\Resources\ApproveRequestResource\Pages;
use App\Models\Request;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApproveRequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $label = 'Approval Pengajuan';

    protected static ?string $navigationGroup = 'Transaksi Approval';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nip')
                    ->label('NIP'),
                Forms\Components\TextInput::make('name')
                    ->label('Nama'),
                Forms\Components\ToggleButtons::make('type')
                    ->label('Kategori Ajuan')
                    ->options(TypeRequest::class)
                    ->inline()
                    ->required()
                    ->live()
                    ->validationMessages([
                        'required' => 'Kategori Ajuan harus diisi.'
                    ])
                    ->hint(function (?Model $record, $state): string {
                        $allowances = [
                            'leave' => 'leave_allowance',
                            'sick' => 'sick_allowance',
                            'giveBirth' => 'give_birth_allowance',
                        ];

                        if (isset($allowances[$state])) {
                            $allowance = DB::table('users')
                                ->where('id', $record->user->id)
                                ->value($allowances[$state]);

                            return "Sisa Cuti : $allowance";
                        }

                        return "";
                    }),
                Forms\Components\ToggleButtons::make('status')
                    ->inline()
                    ->options(function (?Model $record) {
                        return match ($record->status) {
                            StatusRequest::Zero => ['Pending'],
                            StatusRequest::One => ['Disetujui Kepala Unit'],
                            // StatusRequest::Two => ['Disetujui SDM'],
                            StatusRequest::Three => ['Disetujui Kepala Balai'],
                            StatusRequest::Four => ['Ditolak'],
                        };
                    })
                    ->hiddenOn(['create', 'edit']),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Dari Tanggal')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Sampai Tanggal')
                    ->required()
                    ->after('start_date')
                    ->validationMessages([
                        'after' => 'Tanggal Selesai tidak boleh sebelum dari Tanggal Mulai'
                    ]),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->required()
                    ->after('start_time')
                    ->validationMessages([
                        'after' => 'Jam Selesai tidak boleh kurang dari Jam Mulai.'
                    ]),
                Forms\Components\ToggleButtons::make('condition')
                    ->label('Lokasi')
                    ->inline()
                    ->options([
                        true => 'Dalam Kota',
                        false => 'Luar Kota'
                    ])->colors([
                        true => 'primary',
                        false => 'danger'
                    ])->icons([
                        true => 'heroicon-m-home',
                        false => 'heroicon-m-arrow-right-start-on-rectangle'
                    ])
                    ->required()
                    ->live()
                    ->hiddenOn('view')
                    ->validationMessages([
                        'required' => 'Lokasi harus diisi.'
                    ]),
                Forms\Components\Textarea::make('location')
                    ->label('Detail Lokasi')
                    ->required()
                    ->maxLength(256)
                    ->columnSpanFull()
                    ->visible(fn(Get $get): bool => !$get('condition')),
                Forms\Components\Textarea::make('description')
                    ->label('Alasan')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                $status = null;

                if ($user->isHeadOfDivision()) {
                    $status = StatusRequest::Zero;
                    $role = 'employee';
                }

                if ($status !== null) {
                    $query->whereHas('user.roles', function (Builder $query) use ($role) {
                        $query->where('roles.name', $role);
                    })->where('status', $status)
                        ->whereNot('user_id', auth()->id());
                }
                $query->orderBy('created_at', 'desc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('user.nip')
                    ->hidden(auth()->user()->isEmployee())
                    ->label('NIP'),
                Tables\Columns\TextColumn::make('user.name')
                    ->hidden(auth()->user()->isEmployee())
                    ->label('Name'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Kategori Ajuan')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->sortable()
                    ->label('Jam Selesai'),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Saat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Diupdate Saat')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Select::make('user_id')
                            ->label('Pilih Pegawai')
                            ->options(
                                User::query()
                                    ->role(['employee', 'resource', 'headOfDivision'])
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->placeholder('Semua Pegawai')
                            ->searchable(),
                        ToggleButtons::make('type')
                            ->label('Kategori Ajuan')
                            ->options(TypeRequest::class)
                            ->inline(),
                        Select::make('status')
                            ->label('Status')
                            ->options(StatusRequest::class),
                        DatePicker::make('created_from')
                            ->label('Tanggal Mulai'),
                        DatePicker::make('created_until')
                            ->label('Tanggal Selesai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['user_id'],
                                fn(Builder $query, $userId): Builder => $query->where('user_id', $userId)
                            )
                            ->when(
                                $data['type'],
                                fn(Builder $query, $type): Builder => $query->where('type', $type)
                            )
                            ->when(
                                $data['status'],
                                fn(Builder $query, $status): Builder => $query->where('status', $status)
                            )
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail'),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->exporter(RequestExporter::class)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApproveRequests::route('/'),
            'view' => Pages\ViewApproveRequest::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return !auth()->user()->isEmployeeJuniorChief();
    }
}
