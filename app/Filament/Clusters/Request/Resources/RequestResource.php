<?php

namespace App\Filament\Clusters\Request\Resources;

use App\Enum\StatusRequest;
use App\Filament\Clusters\Request;
use App\Filament\Clusters\Request\Resources\RequestResource\Pages;
use App\Models\Request as ModelRequest;
use App\Enum\TypeRequest;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RequestResource extends Resource
{
    protected static ?string $model = ModelRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-circle';

    protected static ?string $navigationLabel = 'Pengajuan';

    protected static ?string $pluralLabel = 'Pengajuan';

    protected static ?string $cluster = Request::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\ToggleButtons::make('type')
                    ->label('Kategori Ajuan')
                    ->options(TypeRequest::class)
                    ->inline()
                    ->required()
                    ->live()
                    ->hint(function ($state) {
                        $allowances = [
                            'leave' => 'leave_allowance',
                            'sick' => 'sick_allowance',
                            'giveBirth' => 'give_birth_allowance',
                        ];

                        if (isset($allowances[$state])) {
                            $allowance = DB::table('users')
                                ->where('id', auth()->id())
                                ->value($allowances[$state]);

                            return "Cuti anda tersisa $allowance";
                        }
                    })
                    ->rules([
                        fn(): Closure => function (string $attribute, $value, Closure $fail) {
                            $allowances = [
                                'leave' => 'leave_allowance',
                                'sick' => 'sick_allowance',
                                'giveBirth' => 'give_birth_allowance'
                            ];

                            if (isset($allowances[$value])) {
                                $allowance = DB::table('users')
                                    ->where('id', auth()->id())
                                    ->value($allowances[$value]);

                                if ($allowance <= 0) {
                                    $fail('Sisa cuti anda sudah habis');
                                }
                            }
                        }
                    ])
                    ->live()
                    ->validationMessages([
                        'required' => 'Kategori Ajuan harus diisi.'
                    ]),
                Forms\Components\ToggleButtons::make('status')
                    ->inline()
                    ->options(function (?Model $record) {
                        return match ($record->status) {
                            StatusRequest::Zero => ['Pending'],
                            StatusRequest::One => ['Disetujui Kepala Unit'],
                            StatusRequest::Two => ['Disetujui SDM'],
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
                    ->afterOrEqual(fn(Get $get) => $get('start_date'))
                    ->rules(fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                        // Get different days
                        $startDate = Carbon::parse($get('start_date'));
                        $endDate = Carbon::parse($value);
                        $differentDays = $startDate->diffInDays($endDate);

                        $allowances = [
                            'leave' => 'leave_allowance',
                            'sick' => 'sick_allowance',
                            'giveBirth' => 'give_birth_allowance'
                        ];

                        if (isset($allowances[$get('type')])) {
                            $allowance = User::query()
                                ->where('id', auth()->id())
                                ->value($allowances[$get('type')]);

                            if ($differentDays >= $allowance) {
                                $fail("Batas cuti anda adalah $allowance hari");
                            }
                        }
                    })
                    ->validationMessages([
                        'after_or_equal' => 'Sampai Tanggal harus lebih dari atau sama dengan Tanggal Mulai'
                    ]),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->default(fn(Get $get): ?string => $get('type') != 'permission' ? '08:00' : null)
                    ->readOnly(fn(Get $get): bool => $get('type') != 'permission')
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->required()
                    ->after('start_time')
                    ->default(fn(Get $get): ?string => $get('type') != 'permission' ? '17:00' : null)
                    ->readOnly(fn(Get $get): bool => $get('type') != 'permission')
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
                if (!auth()->user()->isAdminDirector()) {
                    $query->where('user_id', auth()->id());
                }
                $query->orderBy('created_at', 'desc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('user.nip')
                    ->hidden(!auth()->user()->isAdminDirector())
                    ->label('NIP'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->hidden(!auth()->user()->isAdminDirector()),
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
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
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
                                $data['type'],
                                fn(Builder $query, $type): Builder => $query->where('type', $type)
                            )
                            ->when(
                                $data['status'],
                                fn(Builder $query, $status): Builder => $query->where('status', $status)
                            )
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail'),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
            'view' => Pages\ViewRequest::route('/{record}'),
            'edit' => Pages\EditRequest::route('/{record}/edit'),
            'timeline' => Pages\Timeline::route('/timeline/{record}')
        ];
    }

    public static function canCreate(): bool
    {
        return !auth()->user()->isAdminDirector();
    }

    public static function canEdit(Model $record): bool
    {
        return $record->status == StatusRequest::Zero;
    }

    public static function canViewAny(): bool
    {
        return !auth()->user()->isAdminDirector();
    }
}
