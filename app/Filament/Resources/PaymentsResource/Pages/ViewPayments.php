<?php

namespace App\Filament\Resources\PaymentsResource\Pages;

use App\Filament\Resources\PaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPayments extends ViewRecord
{
    protected static string $resource = PaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
