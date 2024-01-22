<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeagueResource\Pages;
use App\Filament\Resources\LeagueResource\RelationManagers;
use App\Models\League;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

use App\Services\ApiFootballService;


// inputs
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;


//columns
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;

class LeagueResource extends Resource
{
    protected static ?string $model = League::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {

        $countryList = League::getCountries();
    
        foreach ($countryList as  $country) {
            $countries[$country['name']] = "{$country['flag']} {$country['name']}"; 
        }

        return $form
            ->schema([
                TextInput::make('name'),
                Select::make('country')->options($countries),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('country'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('api map leagues')
                ->action(function (ApiFootballService $apiFootballService) {
                     $apiFootballService->getLeagues();

                     Notification::make()
                    ->title('Api Mapped successfully')
                    ->success()
                    ->send();
                })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\TeamsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeagues::route('/'),
            'create' => Pages\CreateLeague::route('/create'),
            'view' => Pages\ViewLeague::route('/{record}'),
            'edit' => Pages\EditLeague::route('/{record}/edit'),
        ];
    }

    
}
