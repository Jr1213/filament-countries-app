<?php

namespace App\Filament\Resources\ProductImageResource\Pages;

use App\Filament\Resources\ProductImageResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProductImage extends CreateRecord
{
    protected static string $resource = ProductImageResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        Notification::make('Product Image Created')
            ->title('Product Image Created')
            ->success()
            ->sendToDatabase(auth()->user());
        return static::getModel()::create($data);
    }
}
