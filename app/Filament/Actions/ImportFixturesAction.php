<?php

namespace App\Filament\Actions;

use App\Services\ApiFootballService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\Asynchronous;
use Filament\Actions\Concerns\WithMiddleware;
use Filament\Actions\Concerns\WithRedirects;


class ImportFixturesAction extends Action
{
    use Asynchronous, WithRedirects, WithMiddleware;

    public function handle(ApiFootballService $apiFootballService)
    {
        $leagueId = '';

        $fixtures = $apiFootballService->getFixtures($leagueId);


        $this->redirectSuccess(__('Fixtures imported successfully.'));
    }
}