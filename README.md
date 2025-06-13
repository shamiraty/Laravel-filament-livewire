<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

Laravel Filament Setup: Step-by-Step Guide
visit here  for official docs
https://filamentphp.com/

This guide will walk you through setting up a new Laravel project, installing Filament, creating an admin user, defining models, and integrating them into the Filament sidebar dashboard.

1. Start Laravel Project
```php
composer create-project laravel/laravel filament
cd filament
```

2. open  ```.env``` file for database setup
```php
#DB_CONNECTION=sqllite
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=filament
DB_USERNAME=root
DB_PASSWORD= 
```

- Once configured, run the initial migrations:
  
```php
php artisan migrate
```

2. Install Filament
```php
composer require filament/filament:"^3.3" -W
```
- After Composer finishes, run the Filament installation command which will publish assets and configure some basic files:
```php
php artisan filament:install --panels
```
3. Create Admin User
- Filament requires an admin user to access the panel. You can create one using an Artisan command provided by Filament.
```php
php artisan make:filament-user
```
![2](https://github.com/user-attachments/assets/73c4a397-dad4-42c1-9747-3dcb0825ca23)

- You can access your Filament admin panel by visiting /admin in your browser (e.g., http://127.0.0.1:8000/admin if you are using php artisan serve).
![3](https://github.com/user-attachments/assets/2f7ba4ed-f4d6-4aec-8da4-249da1de3f29)

4. Create Product Model
- We'll create a Product model along with its migration.

```php
php artisan make:model Product -m
```

This will create app/Models/Product.php and a migration file in database/migrations.
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```
- After modifying the migration, run it to create the products table:

```php
php artisan migrate
```

5. Create Sales Model (Sale Product)
- Next, let's create a Sale model and its migration. This model will represent a sale transaction, linking to products.

```php
php artisan make:model Sale -m
```
This will create app/Models/Sale.php and a migration file for sales.
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Foreign key to products table
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

- After modifying the migration, run it:

```php
php artisan migrate
```

create asset model
6. php artisan make:model Asset -m
This will create app/Models/Asset.php and a migration file for sales.
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Jina la asset
            $table->string('asset_type'); // Aina ya asset (electronic, bango, generator, fire extinguisher, etc.)
            $table->string('location')->nullable(); // Location au ofisi ilipo
            $table->string('registration_number')->unique()->nullable(); // Registration number (hakikisha ni unique)
            $table->date('purchase_date')->nullable(); // Tarehe iliyonunuliwa
            $table->decimal('purchase_price', 10, 2)->nullable(); // Bei iliyonunuliwa
            $table->string('custodian')->nullable(); // Jina la anaesimamia
            $table->string('status')->default('Good'); // Status yake (nzima, mbovu, Under Maintenance)
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
```

- After modifying the migration, run it:

```php
php artisan migrate
```


6. Make User, Sales, Product in Filament Sidebar Dashboard
- To display these models in the Filament sidebar, you need to create Filament Resources for each.

User Resource
Filament should have already generated a UserResource if you ran filament:install. You can verify its existence at app/Filament/Resources/UserResource.php. If it's not there, you can create it:

- User Resource
```php
php artisan make:filament-resource User
```
This command will generate app/Filament/Resources/UserResource.php.
// app/Filament/Resources/UserResource.php
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash; // Required for password hashing

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users'; // Changed to a more appropriate icon for users
    protected static ?string $navigationGroup = 'User Management'; // Optional: Group your resources

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true) // Ensure email is unique, ignore current record on edit
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)) // Hash the password
                    ->dehydrated(fn (?string $state): bool => filled($state)) // Only save if password is provided
                    ->required(fn (string $operation): bool => $operation === 'create') // Required on create
                    ->minLength(8)
                    ->confirmed(), // Requires a matching password_confirmation field
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create') // Required on create
                    ->minLength(8)
                    ->dehydrated(false) // Don't save this field to the database
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
```

- Product Resource
Create the Filament Resource for the Product model:
```php
php artisan make:filament-resource Product
```
This command will generate app/Filament/Resources/ProductResource.php.
Open app/Filament/Resources/ProductResource.php and configure its form and table methods to define how products are created/edited and displayed.

// app/Filament/Resources/ProductResource.php
```php
<?php
namespace App\Filament\Resources;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Inventory Management'; // Optional: Group your resources

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->minValue(0.01),
                Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->required()
                    ->minValue(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('usd') // Or your desired currency
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'), // Added label for clarity
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Updated At'), // Added label for clarity
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label('Created Date'), // Label for the filter

                Tables\Filters\Filter::make('updated_at')
                    ->form([
                        Forms\Components\DatePicker::make('updated_from'),
                        Forms\Components\DatePicker::make('updated_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['updated_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['updated_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    })
                    ->label('Updated Date'), // Label for the filter
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
```

- Sale Resource
Create the Filament Resource for the Sale model:
```php
php artisan make:filament-resource Sale
```

This will generate app/Filament/Resources/SaleResource.php.
Open app/Filament/Resources/SaleResource.php and configure its form and table methods. Remember that Sale has a product_id which should be a selectable field.

// app/Filament/Resources/SaleResource.php
```php
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
```
Important: For the SaleResource, you need to define the product relationship in your Sale model (app/Models/Sale.php) so Filament can properly display the product name.


---------------------------------------------------------------------------------------------------


- Asset Resource
Create the Filament Resource for the Sale model:
```php
php artisan make:filament-resource Asset
```
This will generate app/Filament/Resources/AssetResource.php.
Open app/Filament/Resources/AssetResource.php and configure its form and table methods. Remember that Sale has a product_id which should be a selectable field.

// app/Filament/Resources/AssetResource.php
```php
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
```








// app/Models/Sale.php

```php
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'total_price',
    ];

    /**
     * Get the product that owns the Sale.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```
You can also define the sales relationship in your Product model if needed (app/Models/Product.php):

// app/Models/Product.php

```php
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
```

Asset
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'asset_type',
        'location',
        'registration_number',
        'purchase_date',
        'purchase_price',
        'custodian',
        'status',
    ];
}
```


Now, run your Laravel development server if it's not already running:

php artisan serve
