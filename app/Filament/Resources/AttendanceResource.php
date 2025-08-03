<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\AttendanceResource\RelationManagers;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('attendance_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('checked_in_at')
                    ->required(),
                Forms\Components\DateTimePicker::make('checked_out_at'),
                Forms\Components\FileUpload::make('final_proof_photo_path')
                    ->label('Proof Photo')
                    ->image()
                    ->disk('public'), // Pastikan ini sesuai dengan disk penyimpanan Anda
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable(),
                Tables\Columns\TextColumn::make('attendance_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('checked_in_at')->dateTime('H:i:s'),
                Tables\Columns\TextColumn::make('checked_out_at')->dateTime('H:i:s'),
                Tables\Columns\ImageColumn::make('final_proof_photo_path')->disk('public'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Tombol Edit sekarang tersedia
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
{
    return [
        // Pastikan baris ini ada dan tidak ada salah ketik
        RelationManagers\SessionAttendancesRelationManager::class,
    ];
}
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            // 'create' => Pages\CreateAttendance::route('/create'), // Halaman untuk membuat data baru
            'edit' => Pages\EditAttendance::route('/{record}/edit'), // Halaman untuk mengedit
        ];
    }    
}