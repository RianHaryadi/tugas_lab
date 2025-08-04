<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('User'),

                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->label('Tanggal Bertugas'),

                Forms\Components\Select::make('role')
                    ->required()
                    ->options([
                        'programmer' => 'Programmer',
                        'asisten' => 'Asisten',
                    ])
                    ->label('Peran'),

                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->maxLength(65535),

                Forms\Components\Toggle::make('is_sick_leave')
                    ->label('Sakit')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->label('Tanggal'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_sick_leave')
                    ->label('Sakit')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('sick_leave')
                    ->query(fn (Builder $query): Builder => $query->where('is_sick_leave', true))
                    ->label('Sakit'),
            ])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
