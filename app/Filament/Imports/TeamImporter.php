<?php

namespace App\Filament\Imports;

use App\Models\Team;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use App\Models\League;

class TeamImporter extends Importer
{
    protected static ?string $model = Team::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('league')
                ->relationship(resolveUsing: ['name'])
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): ?Team
    {
        $team = Team::firstOrNew([
            'name' => $this->data['name'],
        ]);

        
        $team->league_id = $this->getLeagueId($this->data['league']);

        return $team;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your team import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public static function getLeagueId($leagueName)
    {
        
        $league = League::where('name', $leagueName)->first();
        
        return $league->id;
    }

}
