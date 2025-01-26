<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyNewsResource\Pages;
use App\Filament\Resources\MyNewsResource\RelationManagers;
use App\Models\MyNews;
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
use Illuminate\Support\Facades\Auth;
use App\Forms\Components\VideoPreview as Prev;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;

class MyNewsResource extends Resource
{
    protected static ?string $model = MyNews::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function getLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Postingan Saya' : 'My News';
    }

    public static function getPluralLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Daftar Postingan Saya' : 'My News';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Postingan Saya' : 'My News';
    }

    public static function getCreateLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Tambah Postingan Saya' : 'Create My News';
    }

    public static function getEditLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Edit Postingan Saya' : 'Edit My News';
    }

    public static function getDeleteLabel(): string
    {
        return app()->getLocale() === 'id' ? 'Hapus Postingan Saya' : 'Delete My News';
    }

    protected static function parsingIdUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            if (preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
                $videoId = $matches[1]; // Ambil video ID
                // return "<iframe width='560' height='315' src='https://www.youtube.com/embed/{$videoId}' frameborder='0' allowfullscreen></iframe>";
                return $videoId;
            }
            return 'Sumber URL tidak valid';
        }
        return ' tidak valid';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label(app()->getLocale() === 'id' ? 'Kategori' : 'Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => app()->getLocale() === 'id' ? 'Nama kategori wajib diisi' : 'The :attribute field is required.',
                            ])
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('temp_content_url')
                            ->label('Tautan Video')
                            ->helperText('Masukkan tautan video YouTube')
                            ->maxLength(2048)
                            ->validationMessages([
                                'required' => 'Tautan video wajib diisi.',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!empty($state)) {
                                    $embedCode = static::parsingIdUrl($state);
                                    if ($embedCode) {
                                        $set('content_url', $embedCode);
                                    }
                                }
                            })
                            ->required(fn(Page $livewire) => ($livewire instanceof CreateRecord))
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('content_url')
                            ->label('ID Video')
                            // ->unique(ignoreRecord: true)
                            ->required()
                            ->validationMessages([
                                'required' => 'Data wajib diisi',
                                // 'unique' => app()->getLocale() === 'id' ? 'Konten tersebut sudah tersedia' : 'The :attribute has already been taken',
                            ])
                            ->readOnly()
                            ->helperText('ID Video yang akan disimpan')
                            ->columnSpan(1),

                    ])
                    ->columns(1),

                Forms\Components\Group::make()
                    ->schema([
                        Fieldset::make('Prev')
                            ->label(app()->getLocale() === 'id' ? 'Preview, (simpan untuk melihat)' : 'Preview, (save to view)')
                            ->schema([
                                Prev::make()->columnSpanFull()
                            ])->columnSpan(1),
                    ])
                    ->columns(1),

                Forms\Components\TextInput::make('title')
                    ->label(app()->getLocale() === 'id' ? 'Judul' : 'Title')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => app()->getLocale() === 'id' ? 'Judul wajib diisi' : 'The :attribute field is required.',
                    ])
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label(app()->getLocale() === 'id' ? 'Deskripsi' : 'Description')
                    ->columnSpanFull()
                    ->rows(6),
                Select::make('tags')
                    ->label(app()->getLocale() === 'id' ? 'Tagar' : 'Tags')
                    ->hint(app()->getLocale() === 'id' ? 'Maksimal 3' : 'Max 3')
                    ->multiple()
                    ->relationship('tags', 'name')
                    ->maxItems(3)
                    ->columnSpanFull()
                    ->preload()
                    ->validationMessages([
                        'max.array' => app()->getLocale() === 'id' ? 'Maksimal hanya 3 hastag.' : 'max 3',
                    ]),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(app()->getLocale() === 'id' ? 'Kategori' : 'Category')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(app()->getLocale() === 'id' ? 'Judul' : 'Title')
                    ->sortable()
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('tags.name')
                    ->label(app()->getLocale() === 'id' ? 'Tag' : 'Tags')
                    ->sortable()
                    ->toggleable()
                    ->searchable(), 
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
                    ->falseIcon('heroicon-o-no-symbol')
                    ->getStateUsing(function ($record) {
                        return !is_null($record->verified_at); // Konversi nilai menjadi boolean
                    }),
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->url(fn($record) => 'https://www.youtube.com/watch?v=' . $record->content_url)
                    ->getStateUsing(fn($record) => 'https://img.youtube.com/vi/' . $record->content_url . '/hqdefault.jpg'),
            ])
            ->filters([
                // Filter Tags (Many-to-Many)
                SelectFilter::make('tags')
                    ->label(app()->getLocale() === 'id' ? 'Tag' : 'Tags')
                    ->multiple() // Memungkinkan memilih lebih dari satu tag
                    ->columnSpanFull()
                    ->preload()
                    ->searchable()
                    ->relationship('tags', 'name'),

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
            'index' => Pages\ListMyNews::route('/'),
            'create' => Pages\CreateMyNews::route('/create'),
            'edit' => Pages\EditMyNews::route('/{record}/edit'),
        ];
    }
}
