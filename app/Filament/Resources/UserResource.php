<?php

namespace App\Filament\Resources;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 0;

    protected static ?string $pluralLabel = 'Pegawai';

    protected static ?string $navigationGroup = 'Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Diri')
                    ->schema([
                        Forms\Components\TextInput::make('nik')
                            ->helperText('NIK harus berisi 16 digit angka.')
                            ->label('NIK')
                            ->required()
                            ->minLength(16)
                            ->maxLength(16)
                            ->unique(User::class, 'nik', ignoreRecord: true)
                            ->numeric()
                            ->validationMessages([
                                'required' => 'NIK harus diisi',
                                'min_digits' => 'NIK harus berisi minimal 16 digit angka',
                                'max_digits' => 'NIK tidak boleh lebih dari 16 digit angka',
                                'unique' => 'NIK sudah digunakan. Silahkan gunakan NIK lain'
                            ]),
                        Forms\Components\TextInput::make('nip')
                            ->helperText('NIP harus berisi 6 digit angka.')
                            ->label('NIP')
                            ->required()
                            ->minLength(6)
                            ->maxLength(6)
                            ->unique(User::class, 'nip', ignoreRecord: true)
                            ->numeric()
                            ->validationMessages([
                                'required' => 'NIP harus diisi',
                                'min_digits' => 'NIP harus berisi minimal 6 digit angka',
                                'max_digits' => 'NIP tidak boleh lebih dari 6 digit angka',
                                'unique' => 'NIP sudah digunakan. Silahkan gunakan NIP lain'
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Nama harus diisi',
                                'maxLength' => 'Maksimal karakter Nama adalah 255'
                            ]),
                        Forms\Components\TextInput::make('place_of_birth')
                            ->label('Tempat Lahir')
                            ->required()
                            ->maxLength(50)
                            ->validationMessages([
                                'required' => 'Tempat Lahir harus diisi',
                                'maxLength' => 'Maksimal karakter Tempat Lahir adalah 50'
                            ]),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->required()
                            ->validationMessages([
                                'required' => 'Tanggal Lahir harus diisi'
                            ]),
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->numeric()
                            ->prefix('+62')
                            ->required()
                            ->maxLength(14)
                            ->minLength(9)
                            ->unique(User::class, 'phone', ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'Nomor Telepon harus diisi',
                                'max_digits' => 'Maksimal karakter Nomor Telepon adalah 14 digit angka',
                                'min_digits' => 'Nomor Telepon harus berisi minimal 9 digit angka',
                                'numeric' => 'Nomor Telepon harus berisi angka yang valid',
                                'unique' => 'Nomor Telepon sudah digunakan. Silahkan gunakan Nomor Telepon lain'
                            ]),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->required()
                            ->maxLength(256)
                            ->validationMessages([
                                'required' => 'Alamat harus diisi',
                                'max_length' => 'Maksimal karakter Alamat adalah 255'
                            ]),
                        Fieldset::make('Data Akun')
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->unique(User::class, 'email', ignoreRecord: true)
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'Nama harus diisi',
                                        'maxLength' => 'Maksimal karakter Nama adalah 255',
                                        'unique' => 'Email sudah digunakan. Silahkan gunakan Email lain'
                                    ]),
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->revealable()
                                    ->hiddenOn('view')
                                    ->validationMessages([
                                        'required' => 'Password harus diisi',
                                        'max_length' => 'Maksimal karakter Password adalah 255 karakter',
                                        'min_length' => 'Password harus berisi minimal 8 karakter'
                                    ]),
                                Forms\Components\Select::make('position_id')
                                    ->label('Posisi')
                                    ->relationship('position', 'title')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Posisi harus diisi'
                                    ]),
                                Forms\Components\Select::make('division_id')
                                    ->label('Divisi')
                                    ->relationship('division', 'title')
                                    ->nullable(),
                            ])
                    ])->columnSpan(8),
                Section::make('Dokumen Pegawai')
                    ->schema([
                        Forms\Components\DatePicker::make('date_of_entry')
                            ->label('Tanggal Masuk')
                            ->required()
                            ->validationMessages([
                                'required' => 'Tanggal Masuk harus diisi'
                            ]),
                        Forms\Components\DatePicker::make('mutation_date')
                            ->label('Tanggal Mutasi'),
                        Forms\Components\FileUpload::make('profile_picture')
                            ->image()
                            ->label('Foto Diri')
                            ->directory('profile-pictures'),
                        Forms\Components\FileUpload::make('signature')
                            ->image()
                            ->label('Tanda Tangan')
                            ->directory('signature'),
                        Forms\Components\FileUpload::make('lod_start')
                            ->acceptedFileTypes(['application/pdf'])
                            ->label('Surat Keputusan Mulai')
                            ->directory('lod-start')
                            ->downloadable(),
                        Forms\Components\FileUpload::make('lod_mutation')
                            ->acceptedFileTypes(['application/pdf'])
                            ->label('Surat Keputusan Mutasi')
                            ->directory('lod-mutation')
                            ->downloadable(),
                        Forms\Components\FileUpload::make('lod_stop')
                            ->acceptedFileTypes(['application/pdf'])
                            ->label('Surat Keputusan Berhenti')
                            ->directory('lod-stop')
                            ->downloadable(),
                    ])->columnSpan(4)
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->label('Alamat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('profile_picture')
                    ->label('Foto')
                    ->circular()
                    ->searchable(),
                Tables\Columns\TextColumn::make('position.title')
                    ->label('Posisi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('division.title')
                    ->label('Divisi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_entry')
                    ->label('Tanggal Masuk')
                    ->date()
                    ->sortable()
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->exporter(UserExporter::class)
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isResource();
    }
}
