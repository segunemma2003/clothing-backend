<?php

namespace App\Filament\Resources\ShippingOrderResource\Pages;

use App\Filament\Resources\ShippingOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShippingOrder extends EditRecord
{
    protected static string $resource = ShippingOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
