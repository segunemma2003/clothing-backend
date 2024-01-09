<?php

namespace App\Filament\Resources\ShippingOrderResource\Pages;

use App\Filament\Resources\ShippingOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewShippingOrder extends ViewRecord
{
    protected static string $resource = ShippingOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
