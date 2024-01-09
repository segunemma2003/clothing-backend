<?php

namespace App\Filament\Resources\ShippingOrderResource\Pages;

use App\Filament\Resources\ShippingOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateShippingOrder extends CreateRecord
{
    protected static string $resource = ShippingOrderResource::class;
}
