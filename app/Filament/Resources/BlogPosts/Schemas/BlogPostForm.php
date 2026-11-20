<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Filament\Components\CloudinaryFileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required()->maxLength(180),
                TextInput::make('title_ur')->label('Title (Urdu)')->maxLength(180),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(200),
                Select::make('category')->options(['Recipes' => 'Recipes', 'Farm stories' => 'Farm stories', 'Guides' => 'Guides'])->required(),
                CloudinaryFileUpload::make('image', 'blog')->label('Cover image'),
                Textarea::make('excerpt')->required()->rows(3)->columnSpanFull(),
                RichEditor::make('body')->required()->columnSpanFull(),
                Toggle::make('is_published')->live(),
                DateTimePicker::make('published_at')->visible(fn ($get) => $get('is_published')),
            ]);
    }
}
