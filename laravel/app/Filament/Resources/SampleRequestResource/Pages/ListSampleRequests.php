<?php

namespace App\Filament\Resources\SampleRequestResource\Pages;

use App\Filament\Resources\SampleRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSampleRequests extends ListRecords
{
    protected static string $resource = SampleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Requests are typically created through the customer portal
        ];
    }
}
