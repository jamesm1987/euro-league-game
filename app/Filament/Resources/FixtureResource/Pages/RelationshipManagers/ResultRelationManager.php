<?php

namespace App\Filament\Resources\FixtureResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Models\Result;

//Inputs

use Filament\Forms\Components\TextInput;


class ResultRelationManager extends RelationManager
{
    protected static string $relationship = 'result';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('home_team_score'),
                TextInput::make('away_team_score')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
         
            ->columns([
                Tables\Columns\TextColumn::make('home_team_score'),
                Tables\Columns\TextColumn::make('away_team_score'),

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
