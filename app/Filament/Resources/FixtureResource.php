<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FixtureResource\Pages;
use App\Filament\Resources\FixtureResource\RelationManagers;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\League;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Services\ApiFootballService;

// inputs
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use App\Filament\Filters\DateFilter;
use Carbon\Carbon;



//columns
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class FixtureResource extends Resource
{
    protected static ?string $model = Fixture::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('home_team_id')->label('Home team')->relationship('homeTeam', 'name'),
                Select::make('away_team_id')->label('Away team')->relationship('awayTeam', 'name'),

                DateTimePicker::make('kickoff_at')->label('Kick off')->seconds(false)->displayFormat('D d F Y, H:i')->native(false)
            ]);
    }

    public static function table(Table $table): Table
    {
   
        return $table
            ->columns([
               TextColumn::make('homeTeam.name'),
               TextColumn::make('awayTeam.name'),
               TextColumn::make('homeTeam.league.name')->label('League'),
               TextColumn::make('kickoff_at')->label('Date')->dateTime('d/m/Y, H:i')->sortable(),
               IconColumn::make('result_exists')->exists('result')->boolean()->label('Result')
               
            ])
            ->filters([
                SelectFilter::make('league')
                ->relationship('homeTeam.league', 'name', fn($query) => $query->orderBy('id')),
                DateFilter::make('kickoff_at')
                ->label(__('Date'))
                ->minDate(Carbon::today()->subMonth(1))
                ->maxDate(Carbon::today()->addMonth(1))
                ->timeZone('Europe/London')
                ->range()
                ->fromLabel(__('From'))
                ->untilLabel(__('Until')),

                Filter::make('Latest')->query(fn (Builder $query): Builder => $query->whereBetween('kickoff_at', [
                    Carbon::today()->subDays(7), Carbon::today()->addDays(7)
                    ]
                ))
            ])
            ->headerActions([
                Action::make('api map fixtures')
                ->action(function (ApiFootballService $apiFootballService) {
                     $apiFootballService->getFixtures();
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
            RelationManagers\ResultRelationManager::class
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
