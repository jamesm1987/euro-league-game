<?php

namespace App\Filament\Actions;

use App\Services\ApiFootballService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\Asynchronous;
use Filament\Actions\Concerns\WithMiddleware;
use Filament\Actions\Concerns\WithRedirects;


class SetupTeamsAction extends Action
{
    use Asynchronous, WithRedirects, WithMiddleware;

    public function handle(ApiFootballService $apiFootballService)
    {
        $leagueId = '';

        $teams = $apiFootballService->getTeams($leagueId);


        $this->redirectSuccess(__('Teams imported successfully.'));
    }
}