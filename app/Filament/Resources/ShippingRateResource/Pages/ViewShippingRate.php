<?php

namespace App\Filament\Resources\ShippingRateResource\Pages;

use App\Filament\Resources\ShippingRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewShippingRate extends ViewRecord
{
    protected static string $resource = ShippingRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
