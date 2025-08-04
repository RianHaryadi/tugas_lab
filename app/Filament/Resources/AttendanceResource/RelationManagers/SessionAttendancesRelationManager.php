<?php

namespace App\Filament\Resources\AttendanceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification; // <-- Import Notification
use Illuminate\Support\Facades\Storage; // <-- Import Storage facade

class SessionAttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'sessionAttendances';

    public function form(Form $form): Form
    {
        // We don't need a form here since we are not creating/editing records directly
        return $form
            ->schema([
                // No form fields
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('session_name')
            ->columns([
                // Column for the proof photo preview
                Tables\Columns\ImageColumn::make('proof_photo_path')
                    ->label('Proof')
                    ->disk('public') // <-- Make sure this matches your filesystems.php config
                    ->circular(),

                // Column for the session name
                Tables\Columns\TextColumn::make('session_name')
                    ->label('Session Name')
                    ->badge(),

                // Column for the validation time
                Tables\Columns\TextColumn::make('session_validated_at')
                    ->label('Validation Time')
                    ->dateTime('d M Y, H:i:s')
                    ->placeholder('Not validated yet.') // <-- Show text if null
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // CreateAction is disabled
            ])
            ->actions([
                // <-- ACTION TO VIEW THE PROOF PHOTO -->
                Tables\Actions\Action::make('view_proof')
                    ->label('View Proof')
                    ->icon('heroicon-o-photo')
                    ->modalContent(fn ($record) => view('filament.modals.view-proof-photo', ['record' => $record]))
                    ->modalSubmitAction(false) // <-- Disables the submit button in the modal
                    ->modalCancelAction(false) // <-- Disables the cancel button
                    ->visible(fn ($record) => $record->proof_photo_path), // <-- Only show if a photo exists

                // <-- ACTION TO VALIDATE THE SESSION -->
                Tables\Actions\Action::make('validate')
                    ->label('Validate')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation() // <-- Asks for confirmation before running
                    ->action(function ($record) {
                        $record->update(['session_validated_at' => now()]);
                        Notification::make()
                            ->title('Session Validated')
                            ->body('The attendance for ' . $record->session_name . ' has been successfully validated.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => is_null($record->session_validated_at)), // <-- Only show if not yet validated
            ])
            ->bulkActions([
                // Bulk actions are disabled
            ]);
    }
}
