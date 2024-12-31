<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Forms\Components\VideoPreview as Prev;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    public static function getLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Postingan' : 'News';
    }

    public static function getPluralLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Daftar Postingan' : 'News';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Postingan' : 'News';
    }


    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->disabled()
                            ->label(app()->getLocale() === 'id' ? 'Kategori' : 'Category')
                            ->relationship('category', 'name'),
                        Forms\Components\TextInput::make('content_url')
                            ->label('ID Video')
                            ->readOnly(),

                        Select::make('tags')
                            ->label(app()->getLocale() === 'id' ? 'Tagar' : 'Tags')
                            ->multiple()
                            ->relationship('tags', 'name')
                            ->disabled(),
                    ])
                    ->columns(1),

                Forms\Components\Group::make()
                    ->schema([
                        Fieldset::make('Prev')
                            ->label(app()->getLocale() === 'id' ? 'Preview, (simpan untuk melihat)' : 'Preview, (save to view)')
                            ->schema([
                                Prev::make()->columnSpanFull()
                            ]),
                    ])
                    ->columns(1),

                Forms\Components\Textarea::make('title')
                    ->label(app()->getLocale() === 'id' ? 'Judul' : 'Title')
                    ->columnSpanFull()
                    ->readOnly(),
                Forms\Components\Select::make('user_id')
                    ->disabled()
                    ->label(app()->getLocale() === 'id' ? 'Penulis' : 'Author')
                    ->relationship('user', 'name'),
                Forms\Components\Toggle::make('verified_at')
                    ->label(
                        fn($record) =>
                        $record && !is_null($record->verified_at) && $record->updated_at > $record->verified_at
                            ? (app()->getLocale() === 'id' ? 'Verifikasi Ulang' : 'Re-Verify')
                            : (app()->getLocale() === 'id' ? 'Verifikasi' : 'Verified')
                    )
                    ->helperText(app()->getLocale() === 'id'
                        ? 'Aktifkan atau aktifkan ulang untuk menandai berita sebagai terverifikasi.'
                        : 'Enable re-enable to mark the news as verified.')
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false)
                    ->dehydrated()
                    ->beforeStateDehydrated(function ($state, callable $set) {
                        $set('verified_at', $state ? now()->addSeconds(10) : null);
                    })
                    ->default(fn($record) => !is_null($record?->verified_at)),
                Forms\Components\Textarea::make('description')
                    ->label(app()->getLocale() === 'id' ? 'Deskripsi' : 'Description')
                    ->columnSpanFull()
                    ->rows(6)
                    ->readOnly(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(app()->getLocale() === 'id' ? 'Penulis' : 'Writer')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(app()->getLocale() === 'id' ? 'Kategori' : 'Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(app()->getLocale() === 'id' ? 'Judul' : 'Title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(app()->getLocale() === 'id' ? 'Dibuat pada' : 'Created at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(app()->getLocale() === 'id' ? 'Diperbarui pada' : 'Updated at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label(app()->getLocale() === 'id' ? 'Diverifikasi pada' : 'Verified at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('verified_at_status')
                    ->boolean()
                    ->label(app()->getLocale() === 'id' ? 'Diverifikasi' : 'Verified')
                    ->trueIcon('heroicon-o-check-badge')
                    ->trueColor('primary')
                    ->falseIcon('heroicon-o-no-symbol')
                    ->falseColor('secondary')
                    ->getStateUsing(function ($record) {
                        return !is_null($record->verified_at); // Konversi nilai menjadi boolean
                    }),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label(app()->getLocale() === 'id' ? 'Penulis' : 'Writer')
                    ->columnSpanFull()
                    ->preload()
                    ->searchable()
                    ->relationship('user', 'name'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_at')
                            ->label(app()->getLocale() === 'id' ? 'Dibuat pada (tanggal)' : 'Created at (date)'),
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_at'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '=', $date),
                            );
                    })
                    ->columnSpan(1),

                Filter::make('custom_tahunan')
                    ->form([
                        Select::make('year')
                            ->label(app()->getLocale() === 'id' ? 'Dibuat pada (tahun)' : 'Created at (year)')
                            ->options(collect(range(now()->year, 2024))->mapWithKeys(fn($year) => [$year => $year])->toArray()),
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['year'],
                            fn(Builder $query): Builder => $query->whereYear('created_at', $data['year'])
                        );
                    })
                    ->columnSpan(1),

                SelectFilter::make('category_id')
                    ->label(app()->getLocale() === 'id' ? 'Kategori' : 'Category')
                    ->columnSpan(1)
                    ->preload()
                    ->searchable()
                    ->relationship('category', 'name'),

                // rentang bulan
                Filter::make('custom_bulanan')
                    ->label('Spesifik Bulan')
                    ->form([
                        Fieldset::make(app()->getLocale() === 'id' ? 'Dibuat Pada (bulan)' : 'Created at (month)')
                            ->schema([
                                Select::make('month')
                                    ->label(app()->getLocale() === 'id' ? 'Bulan' : 'Month')
                                    ->options([
                                        '1' => (app()->getLocale() === 'id' ? 'Januari' : 'January'),
                                        '2' => (app()->getLocale() === 'id' ? 'Februari' : 'February'),
                                        '3' => (app()->getLocale() === 'id' ? 'Maret' : 'March'),
                                        '4' => (app()->getLocale() === 'id' ? 'April' : 'April'),
                                        '5' => (app()->getLocale() === 'id' ? 'Mei' : 'May'),
                                        '6' => (app()->getLocale() === 'id' ? 'Juni' : 'June'),
                                        '7' => (app()->getLocale() === 'id' ? 'Juli' : 'July'),
                                        '8' => (app()->getLocale() === 'id' ? 'Agustus' : 'August'),
                                        '9' => (app()->getLocale() === 'id' ? 'September' : 'September'),
                                        '10' => (app()->getLocale() === 'id' ? 'Oktober' : 'October'),
                                        '11' => (app()->getLocale() === 'id' ? 'November' : 'November'),
                                        '12' => (app()->getLocale() === 'id' ? 'Desember' : 'December'),
                                    ]),
                                Select::make('year')
                                    ->label(app()->getLocale() === 'id' ? 'Tahun' : 'Year')
                                    ->options(collect(range(now()->year, 2024))->mapWithKeys(fn($year) => [$year => $year])->toArray()),
                            ])

                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['month'],
                            fn(Builder $query) => $query->whereYear('created_at', '=', $data['year'])
                                ->whereMonth('created_at', '=', $data['month'])
                        );
                    })
                    ->columnSpan(3),

                // rentang hari
                Filter::make('rentang')
                    ->form([
                        Fieldset::make(app()->getLocale() === 'id' ? 'Rentang Tanggal Spesifik' : 'Specific Date Range')
                            ->schema([
                                DatePicker::make('created_from')
                                    ->label(app()->getLocale() === 'id' ? 'Tanggal Mulai' : 'Start Date'),
                                DatePicker::make('created_until')
                                    ->label(app()->getLocale() === 'id' ? 'Tanggal Selesai' : 'End Date'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->columnSpan(3),

                // saat ini
                Filter::make('today')
                    ->label(app()->getLocale() === 'id' ? 'Hari ini' : 'Today')
                    ->query(fn(Builder $query) => $query->whereDate('created_at', today())),
                Filter::make('this week')
                    ->label(app()->getLocale() === 'id' ? 'Minggu ini' : 'This Week')
                    ->query(fn(Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),
                Filter::make('this month')
                    ->label(app()->getLocale() === 'id' ? 'Bulan ini' : 'This Month')
                    ->query(fn(Builder $query) => $query->whereMonth('created_at', now()->month)),

            ])->filtersFormColumns(3)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
