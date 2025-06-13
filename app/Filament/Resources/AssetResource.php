<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon; // Import Carbon for date handling

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2'; // Icon for assets
    protected static ?string $navigationGroup = 'Company Assets'; // New group for assets

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Asset Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('asset_type')
                    ->label('Asset Type')
                    ->options([
                        'Electronic' => 'Electronic',
                        'Furniture' => 'Furniture',
                        'Vehicle' => 'Vehicle',
                        'Machinery' => 'Machinery',
                        'Fire Extinguisher' => 'Fire Extinguisher',
                        'Generator' => 'Generator',
                        'Signage' => 'Signage (Bango)',
                        'HVAC' => 'HVAC System',
                        'Security Equipment' => 'Security Equipment',
                        'IT Equipment' => 'IT Equipment',
                        'Office Supplies' => 'Office Supplies',
                        'Medical Equipment' => 'Medical Equipment',
                        'Laboratory Equipment' => 'Laboratory Equipment',
                        'Networking Equipment' => 'Networking Equipment',
                        'Audio Visual Equipment' => 'Audio Visual Equipment',
                        'Software Licenses' => 'Software Licenses',
                        'Tools' => 'Tools',
                        'Heavy Machinery' => 'Heavy Machinery',
                        'Agricultural Equipment' => 'Agricultural Equipment',
                        'Piping System' => 'Piping System',
                        'Electrical System' => 'Electrical System',
                        'Plumbing Fixtures' => 'Plumbing Fixtures',
                        'Safety Equipment' => 'Safety Equipment',
                        'Uniforms' => 'Uniforms',
                        'Artwork' => 'Artwork',
                        'Library Books' => 'Library Books',
                        'Consumables' => 'Consumables',
                        'Sporting Equipment' => 'Sporting Equipment',
                        'Cleaning Equipment' => 'Cleaning Equipment',
                        'Kitchen Appliances' => 'Kitchen Appliances',
                        'Building Structure' => 'Building Structure',
                        'Land' => 'Land',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('location')
                    ->label('Location/Office')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('registration_number')
                    ->label('Registration Number')
                    ->unique(ignoreRecord: true) // Ensure uniqueness, ignore current record on edit
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\DatePicker::make('purchase_date')
                    ->label('Purchase Date')
                    ->nullable(),
                Forms\Components\TextInput::make('purchase_price')
                    ->label('Purchase Price')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->nullable(),
                Forms\Components\TextInput::make('custodian')
                    ->label('Custodian Name')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Good' => 'Good (Nzima)',
                        'Broken' => 'Broken (Mbovu)',
                        'Under Maintenance' => 'Under Maintenance',
                        'Disposed' => 'Disposed', // Added 'Disposed' status
                        'Lost' => 'Lost', // Added 'Lost' status
                    ])
                    ->required()
                    ->default('Good'),
            ])->columns(2); // Arrange fields in two columns for better layout
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Asset Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('asset_type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_number')
                    ->label('Reg. No.')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Purchase Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Price')
                    ->money('usd') // Format as currency
                    ->sortable(),
                Tables\Columns\TextColumn::make('custodian')
                    ->label('Custodian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge() // Display status as a badge
                    ->color(fn (string $state): string => match ($state) {
                        'Good' => 'success',
                        'Broken' => 'danger',
                        'Under Maintenance' => 'warning',
                        'Disposed' => 'gray', // Color for 'Disposed'
                        'Lost' => 'info', // Color for 'Lost'
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter by Asset Type
                Tables\Filters\SelectFilter::make('asset_type')
                    ->options([
                        'Electronic' => 'Electronic',
                        'Furniture' => 'Furniture',
                        'Vehicle' => 'Vehicle',
                        'Machinery' => 'Machinery',
                        'Fire Extinguisher' => 'Fire Extinguisher',
                        'Generator' => 'Generator',
                        'Signage' => 'Signage (Bango)',
                        'HVAC' => 'HVAC System',
                        'Security Equipment' => 'Security Equipment',
                        'IT Equipment' => 'IT Equipment',
                        'Office Supplies' => 'Office Supplies',
                        'Medical Equipment' => 'Medical Equipment',
                        'Laboratory Equipment' => 'Laboratory Equipment',
                        'Networking Equipment' => 'Networking Equipment',
                        'Audio Visual Equipment' => 'Audio Visual Equipment',
                        'Software Licenses' => 'Software Licenses',
                        'Tools' => 'Tools',
                        'Heavy Machinery' => 'Heavy Machinery',
                        'Agricultural Equipment' => 'Agricultural Equipment',
                        'Piping System' => 'Piping System',
                        'Electrical System' => 'Electrical System',
                        'Plumbing Fixtures' => 'Plumbing Fixtures',
                        'Safety Equipment' => 'Safety Equipment',
                        'Uniforms' => 'Uniforms',
                        'Artwork' => 'Artwork',
                        'Library Books' => 'Library Books',
                        'Consumables' => 'Consumables',
                        'Sporting Equipment' => 'Sporting Equipment',
                        'Cleaning Equipment' => 'Cleaning Equipment',
                        'Kitchen Appliances' => 'Kitchen Appliances',
                        'Building Structure' => 'Building Structure',
                        'Land' => 'Land',
                    ]),
                // Filter by Status
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Good' => 'Good',
                        'Broken' => 'Broken',
                        'Under Maintenance' => 'Under Maintenance',
                        'Disposed' => 'Disposed',
                        'Lost' => 'Lost',
                    ]),
                // Filter by Location
                Tables\Filters\SelectFilter::make('location')
                    ->options(fn (): array => Asset::distinct()->pluck('location', 'location')->toArray())
                    ->searchable()
                    ->label('Filter by Location'),
                // Filter by Custodian
                Tables\Filters\SelectFilter::make('custodian')
                    ->options(fn (): array => Asset::distinct()->pluck('custodian', 'custodian')->toArray())
                    ->searchable()
                    ->label('Filter by Custodian'),
                // Filter by Purchase Date Range
                Tables\Filters\Filter::make('purchase_date')
                    ->form([
                        Forms\Components\DatePicker::make('purchase_date_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('purchase_date_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['purchase_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('purchase_date', '>=', $date),
                            )
                            ->when(
                                $data['purchase_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('purchase_date', '<=', $date),
                            );
                    }),
                // Filter by Purchase Price Range
                Tables\Filters\Filter::make('purchase_price')
                    ->form([
                        Forms\Components\TextInput::make('purchase_price_from')
                            ->numeric()
                            ->label('Min Price')
                            ->prefix('$')
                            ->minValue(0),
                        Forms\Components\TextInput::make('purchase_price_to')
                            ->numeric()
                            ->label('Max Price')
                            ->prefix('$')
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['purchase_price_from'],
                                fn (Builder $query, $price): Builder => $query->where('purchase_price', '>=', $price),
                            )
                            ->when(
                                $data['purchase_price_to'],
                                fn (Builder $query, $price): Builder => $query->where('purchase_price', '<=', $price),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
