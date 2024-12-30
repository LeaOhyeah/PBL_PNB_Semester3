<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    public static function getLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Tagar' : 'Tag';
    }

    public static function getPluralLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Daftar Tagar' : 'Tags';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Tagar' : 'Tags';
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
                        'unique' => app()->getLocale() === 'id' ? 'Nama tager tersebut sudah tersedia' : 'The :attribute has already been taken',
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label(app()->getLocale() === 'id' ? 'Dibuat pada' : 'Created at')
                    ->dateTime()
                    ->sortable(),
                // ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->label(app()->getLocale() === 'id' ? 'Diperbarui pada' : 'Updated at')
                //     ->dateTime()
                //     ->sortable(),
                // ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
                    ->columnSpan(2),

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
                    ->columnSpan(2),

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

            ])->filtersFormColumns(2)
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ManageTags::route('/'),
        ];
    }
}
