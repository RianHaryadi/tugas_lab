<?php

namespace App\Filament\Resources\TaskProgressResource\Pages;

use App\Filament\Resources\TaskProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaskProgress extends EditRecord
{
    protected static string $resource = TaskProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
