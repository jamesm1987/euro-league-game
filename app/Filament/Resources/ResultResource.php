<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Models\Result;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

use App\Services\ApiFootballService;
use Carbon\Carbon;

//columns
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;

// Inputs
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('home_team_score')->label('Home')->numeric()->minValue(0),
                TextInput::make('away_team_score')->label('Away')->numeric()->minValue(0)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fixture.homeTeam.name'),
                TextColumn::make('finalScore')->label('Result'),
                TextColumn::make('fixture.awayTeam.name'),
                TextColumn::make('fixture.homeTeam.league.name')->label('League'),

            ])
            ->filters([
                SelectFilter::make('league')
                ->relationship('fixture.homeTeam.league', 'name', fn($query) => $query->orderBy('id')),
                Filter::make('Latest')->query(fn (Builder $query): Builder => 
                    $query->whereHas('fixture', fn ($q) =>
                        $q->whereBetween('kickoff_at', [
                            Carbon::today()->subDays(9),
                            Carbon::today()
                        ])
                    )
                )
            ])
            ->headerActions([
                Action::make('api map results')
                ->action(function (ApiFootballService $apiFootballService) {
                     $apiFootballService->getResults();
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
            RelationManagers\FixturesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResults::route('/'),
            'create' => Pages\CreateResult::route('/create'),
            'edit' => Pages\EditResult::route('/{record}/edit'),
        ];
    }
}
