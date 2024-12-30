<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Kategori' : 'Category';
    }

    public static function getPluralLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Daftar Kategori Ubah' : 'Categories';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Kategori' : 'Category';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(app()->getLocale() === 'id' ? 'Nama' : 'Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Nama kategori wajib diisi',
                        'unique' => app()->getLocale() === 'id' ? 'Nama kategori tersebut sudah tersedia' : 'The :attribute has already been taken',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->label(app()->getLocale() === 'id' ? 'Nama' : 'Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(app()->getLocale() === 'id' ? 'Dibuat pada' : 'Created at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(app()->getLocale() === 'id' ? 'Diperbarui pada' : 'Updated at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCategories::route('/'),
        ];
    }
}
