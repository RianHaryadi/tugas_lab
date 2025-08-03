<?php

namespace App\Filament\Resources\AttendanceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SessionAttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'sessionAttendances';

    public function form(Form $form): Form
    {
        // Biarkan kosong karena kita tidak akan membuat/mengedit dari sini
        return $form
            ->schema([
                // Tidak ada field form
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('session_name')
            ->columns([
                // Kolom untuk menampilkan sesi yang divalidasi
                Tables\Columns\TextColumn::make('session_name')
                    ->label('Sesi yang Divalidasi')
                    ->badge(),
                Tables\Columns\TextColumn::make('session_validated_at')
                    ->label('Waktu Validasi')
                    ->dateTime('H:i:s'), // Tampilkan jam, menit, detik
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(), // Tombol Create kita nonaktifkan
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),   // Tombol Edit kita nonaktifkan
                // Tables\Actions\DeleteAction::make(), // Tombol Delete kita nonaktifkan
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}