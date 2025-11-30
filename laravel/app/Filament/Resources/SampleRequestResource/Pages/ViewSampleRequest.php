<?php

namespace App\Filament\Resources\SampleRequestResource\Pages;

use App\Filament\Resources\SampleRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSampleRequest extends ViewRecord
{
    protected static string $resource = SampleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
