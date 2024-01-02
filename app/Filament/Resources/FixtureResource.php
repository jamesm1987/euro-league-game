<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FixtureResource\Pages;
use App\Filament\Resources\FixtureResource\RelationManagers;
use App\Models\Fixture;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// inputs
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;


//columns
use Filament\Tables\Columns\TextColumn;

class FixtureResource extends Resource
{
    protected static ?string $model = Fixture::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('Home Team'),
                Select::make('Away Team'),
                DateTimePicker::make('Kick off')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Home'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('api map fixtures')
                ->action(function (ApiFootballService $apiFootballService) {
                     $apiFootballService->getFixtures();
                })
            ])
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
            'index' => Pages\ListFixtures::route('/'),
            'create' => Pages\CreateFixture::route('/create'),
            'edit' => Pages\EditFixture::route('/{record}/edit'),
        ];
    }
}
