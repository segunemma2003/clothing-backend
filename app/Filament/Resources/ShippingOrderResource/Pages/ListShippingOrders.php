<?php

namespace App\Filament\Resources\ShippingOrderResource\Pages;

use App\Filament\Resources\ShippingOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShippingOrders extends ListRecords
{
    protected static string $resource = ShippingOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
