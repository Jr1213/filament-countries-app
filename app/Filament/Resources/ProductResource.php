<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImageRelationResource\RelationManagers\ProductImageRelationManager;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Infolists\Components\Fieldset as ComponentsFieldset;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Tabs as ComponentsTabs;
use Filament\Infolists\Components\Tabs\Tab as TabsTab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $recordTitleAttribute  = 'name';

    protected static ?string $navigationLabel = 'Your Products';

    protected static ?string $modelLabel = 'Your Products';

    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;
    protected static function getTableQuery()
    {
        return Product::query()->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Tabs::make('Tabs')->tabs([
                    Tab::make('Product Information')->schema([
                        Fieldset::make('Product Information')->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Textarea::make('description')
                                ->required()
                                ->columnSpanFull(),

                            Forms\Components\FileUpload::make('image')
                                ->required()
                                ->image()
                                ->columnSpanFull()
                                ->disk('public')
                                ->visibility('public')
                                ->directory('images/products'),
                        ])->columns(1),
                        Hidden::make('user_id')->default(auth()->id()),
                    ]),
                    Tab::make('Product Price')
                        ->schema([
                            Fieldset::make('Product Price')->schema([
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('discount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
                        ]),
                    Tab::make('Product Status')
                        ->schema([
                            Fieldset::make('Product Status')->schema([
                                Forms\Components\Toggle::make('active')
                                    ->required(),
                                Forms\Components\Toggle::make('is_hot')
                                    ->required(),
                            ])
                        ]),
                    Tab::make('Product Number')
                        ->schema([
                            Forms\Components\TextInput::make('stock')
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('score')
                                ->required()
                                ->numeric(),
                        ]),
                ])
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Status')
                    ->boolean(),
                ImageColumn::make('image_url')->circular(),
                Tables\Columns\IconColumn::make('is_hot')
                    ->label('Hot')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('created date')
                    ->dateTime('j F, Y, g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('updated date')
                    ->dateTime('j F, Y, g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                TernaryFilter::make('active')
                    ->label('only active products'),
                TernaryFilter::make('is_hot')
                ->label('only hot products'),
                QueryBuilder::make('stock')
                    ->constraints([
                        NumberConstraint::make('stock')
                    ])

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->query(self::getTableQuery());
    }

    public static function getRelations(): array
    {
        return [
            ProductImageRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsTabs::make('Product Information')->tabs([
                    TabsTab::make('Product Information')->schema([
                        ComponentsFieldset::make('Product Information')->schema([
                            TextEntry::make('user.name')
                            ->label('Owner'),
                            TextEntry::make('name')
                                ->label('Product Name'),

                            TextEntry::make('description')
                                ->label('Product Description'),
                            ImageEntry::make('image_url')
                                ->label('Product Image')
                                ->columnSpanFull()

                        ])->columns(1),
                    ]),
                    TabsTab::make('Product Price')->schema([
                        ComponentsFieldset::make('Product Price')->schema([
                            TextEntry::make('price')
                                ->label('Product Price')
                                ->money(),

                            TextEntry::make('discount')
                                ->label('Product Discount')
                                ->money(),

                        ])->columns(1),
                    ]),
                    TabsTab::make('Product Status')->schema([
                        ComponentsFieldset::make('Product Status')->schema([
                            IconEntry::make('active')
                                ->label('Product Active')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-badge')
                                ->falseIcon('heroicon-o-x-mark'),

                            IconEntry::make('is_hot')
                                ->label('Product Is Hot')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-badge')
                                ->falseIcon('heroicon-o-x-mark'),

                        ])->columns(2),
                    ]),
                    TabsTab::make('Product Numbers')->schema([
                        ComponentsFieldset::make('Product Numbers')->schema([
                            TextEntry::make('stock')
                                ->label('Stock Count')
                                ->numeric(),

                            TextEntry::make('score')
                                ->label('Product Score')
                                ->numeric(),

                        ]),
                    ])
                ])
            ])->columns(1);
    }

    protected function afterCreate()
    {
        Notification::make()
            ->title('Product Created')
            ->success()
            ->sendToDatabase(auth()->user());
    }
}
