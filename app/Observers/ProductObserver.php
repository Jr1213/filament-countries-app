<?php

namespace App\Observers;

use App\Models\Product;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

class ProductObserver implements ShouldDispatchAfterCommit
{

    public function created(Product $product): void
    {
        Notification::make()
            ->title('Product Created')
            ->success()
            ->sendToDatabase(auth()->user());
    }

    public function updated(Product $product): void
    {
        Notification::make()
            ->title($product->name .  ' Product Updated')
            ->success()
            ->sendToDatabase(auth()->user());
    }


    public function deleted(Product $product): void
    {
        Notification::make()
            ->title($product->name .  ' Product Deleted')
            ->success()
            ->sendToDatabase(auth()->user());
    }


    public function restored(Product $product): void
    {
        Notification::make()
            ->title($product->name .  ' Product Restored')
            ->success()
            ->sendToDatabase(auth()->user());
    }

    public function forceDeleted(Product $product): void
    {
        Notification::make()
            ->title($product->name .  ' Product Force Deleted')
            ->success()
            ->sendToDatabase(auth()->user());
    }
}
