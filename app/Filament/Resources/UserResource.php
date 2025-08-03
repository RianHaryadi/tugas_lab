<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengguna')
                    ->description('Detail dasar untuk pengguna.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $roles = $get('roles') ?? [];
                                
                                // FIX: Menggunakan nama peran lowercase agar konsisten
                                if (Str::endsWith($state, '@admin.com')) {
                                    $id = Role::where('name', 'admin')->first()?->id;
                                    if ($id && !in_array($id, $roles)) {
                                        $roles[] = $id;
                                    }
                                }

                                if (Str::endsWith($state, '@lab.com')) {
                                    $id = Role::where('name', 'asisten lab')->first()?->id;
                                    if ($id && !in_array($id, $roles)) {
                                        $roles[] = $id;
                                    }
                                }

                                if (Str::endsWith($state, '@dev.com')) {
                                    $id = Role::where('name', 'programmer')->first()?->id;
                                    if ($id && !in_array($id, $roles)) {
                                        $roles[] = $id;
                                    }
                                }

                                $set('roles', $roles);
                            })
                            ->rules([
                                function (Get $get, string $operation) {
                                    if ($operation !== 'create') return null;

                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $selectedRoles = Role::whereIn('id', $get('roles') ?? [])->pluck('name');

                                        // FIX: Menggunakan nama peran lowercase untuk validasi
                                        if ($selectedRoles->contains('admin') && !Str::endsWith($value, '@admin.com')) {
                                            $fail('Email untuk peran Admin harus berakhiran @admin.com.');
                                        }

                                        if ($selectedRoles->contains('programmer') && !Str::endsWith($value, '@dev.com')) {
                                            $fail('Email untuk peran Programmer harus berakhiran @dev.com.');
                                        }

                                        if ($selectedRoles->contains('asisten lab') && !Str::endsWith($value, '@lab.com')) {
                                            $fail('Email untuk peran Asisten Lab harus berakhiran @lab.com.');
                                        }
                                    };
                                }
                            ]),
                    ])->columns(2),

                Forms\Components\Section::make('Peran & Keamanan')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(', ')
                    // FIX: Menggunakan nama peran lowercase untuk warna badge
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'warning',
                        'programmer' => 'info',
                        'asisten lab' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
