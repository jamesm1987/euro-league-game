<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

use App\Filament\Imports\TeamImporter;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\Action;

use Filament\Resources\Forms\Components\BelongsToSelect;

use Filament\Tables\Columns\TextColumn;
use App\Forms\Components\Currency;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

use App\Models\League;
use App\Models\Team;
use App\Models\ApiRequest;

use App\Services\ApiFootballService;


class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                Select::make('league_id')->label('League')->relationship('league', 'name'),
                TextInput::make('price')
            ]);
        
    }

    public static function table(Table $table): Table
    {

        return $table
            ->headerActions([
                ImportAction::make()
                ->importer(TeamImporter::class),
                
                Action::make('api map all')
                ->action(function (ApiFootballService $apiFootballService) {
                     $apiFootballService->getTeams();
                })
        
            ])
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('league.name')->sortable(),
                TextColumn::make('price')->formatStateUsing(fn (string $state): string => __("£{$state}m")),
            ])
            ->filters([
                SelectFilter::make('league')
                ->options(League::getLeagues())
                ->attribute('league_id')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Action::make('api map team')
                // ->action(function (ApiFootballService $apiFootballService) {
                //      $apiFootballService->getTeams();
                // })

                Action::make('api map team')->visible(fn($record) => empty($record->api_id))                    
                
                
                    ->form([
                       
                        Select::make('team')
                            ->label('Team')
                            ->options(Team::where('api_id', 0)->pluck('name', 'id'))
                            ->required(),

                        Select::make('api_team')->label('API')
                            ->options(ApiRequest::where('request_type', 'team')->pluck('response'))
                            ->required(),
                    ])
                    ->action(function (array $data, Team $record): void {
                        dd($data);
                        // $record->author()->associate($data['authorId']);
                        // $record->save();
                    })->slideOver()
                
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
            RelationManagers\LeagueRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'view' => Pages\ViewTeam::route('/{record}'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
