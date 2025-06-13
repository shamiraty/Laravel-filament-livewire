<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Sale;
use App\Models\Product; // Don't forget to import Product model
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Closure;
use Illuminate\Support\Str; // Required for Str::of in afterStateUpdated

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Inventory Management'; // Optional: Group your resources

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name') // 'product' is the relation name in Sale model
                    ->required()
                    ->searchable()
                    ->preload() // Improves UX for large number of products
                    ->reactive() // Make this field reactive to trigger updates
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        // Ensure a product is selected and quantity is valid
                        if ($state && $get('quantity')) {
                            $product = Product::find($state);
                            if ($product) {
                                $total = $product->price * $get('quantity');
                                $set('total_price', number_format($total, 2, '.', '')); // Format to 2 decimal places
                            }
                        } else {
                            $set('total_price', 0.00); // Reset if no product or quantity
                        }
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->live(onBlur: true) // Make this field reactive on blur to update total_price
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        // Ensure quantity is valid and a product is selected
                        if ($state && $get('product_id')) {
                            $product = Product::find($get('product_id'));
                            if ($product) {
                                $total = $product->price * $state;
                                $set('total_price', number_format($total, 2, '.', '')); // Format to 2 decimal places
                            }
                        } else {
                            $set('total_price', 0.00); // Reset if no quantity or product
                        }
                    }),
                Forms\Components\TextInput::make('total_price')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->readOnly() // This field is calculated and not editable manually
                    ->default(0.00) // Set a default value for new records
                    ->dehydrateStateUsing(fn ($state) => (float) $state), // Ensure it's stored as a float
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name') // Display product name from the relationship
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('usd') // Or your desired currency
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
