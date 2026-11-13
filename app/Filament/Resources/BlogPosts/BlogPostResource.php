<?php

namespace App\Filament\Resources\BlogPosts;

use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Models\BlogPost;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $modelLabel = 'Blog post';

    protected static ?string $pluralModelLabel = 'Blog posts';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
                TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(function ($state, $set, $get) { if (blank($get('slug'))) { $set('slug', Str::slug((string) $state)); } }),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->helperText('Used in URLs. Auto-filled from the title.'),
                TextInput::make('tag')->default('Blog'),
                Textarea::make('excerpt')->rows(3)->columnSpanFull(),
                RichEditor::make('body')->columnSpanFull(),
                FileUpload::make('image')->image()->disk('public')->directory('site')->visibility('public')->imageEditor(),
                FileUpload::make('thumbnail')->image()->disk('public')->directory('site')->visibility('public')->imageEditor(),
                DateTimePicker::make('published_at'),
                Toggle::make('is_published')->default(true),
                Toggle::make('is_featured')->helperText('The featured post is the large one on the homepage.'),
                TextInput::make('seo_title')->label('SEO title'),
                Textarea::make('seo_description')->label('SEO description')->rows(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')->disk('public'),
                TextColumn::make('title')->searchable()->limit(50),
                TextColumn::make('published_at')->dateTime()->sortable(),
                IconColumn::make('is_featured')->boolean()->label('Featured'),
                IconColumn::make('is_published')->boolean()->label('Published'),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Published')
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }
}
