<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')->required()->unique(ignoreRecord: true)->disabledOn('edit')->helperText('Stable setting identifier; edit the value instead of the key.'),
                TextInput::make('label')->required()->maxLength(160),
                Textarea::make('value')->rows(5)->columnSpanFull(),
            ]);
    }
}
