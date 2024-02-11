<?php

namespace App\Filament\Resources\ResultResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

//Inputs
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;

class FixturesRelationManager extends RelationManager
{
    protected static string $relationship = 'fixture';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('home_team_id')->label('Home team')->relationship('homeTeam', 'name'),
                Select::make('away_team_id')->label('Away team')->relationship('awayTeam', 'name'),

                DateTimePicker::make('kickoff_at')->label('Kick off')->seconds(false)->displayFormat('D d F Y, H:i')->native(false)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Hometeam')
            ->columns([
                Tables\Columns\TextColumn::make('result.fixture.homeTeam.name'),
                Tables\Columns\TextColumn::make('result.fixture.awayTeam.name'),
                Tables\Columns\TextColumn::make('result.fixture.kickoff_at')->dateTime('D d M Y, H:i'),
                Tables\Columns\TextColumn::make('result.fixture.homeTeam.league.name')

            ])
            ->filters([
                //
            ])
            ->headerActions([
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
 
            ]);
    }
}
