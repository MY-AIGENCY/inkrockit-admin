<?php

namespace App\Filament\Resources\QuoteRequestResource\Pages;

use App\Filament\Resources\QuoteRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuoteRequest extends CreateRecord
{
    protected static string $resource = QuoteRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
