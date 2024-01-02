<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class ImportFixtures extends Component implements HasForms, HasActions
{

    use InteractsWithActions, InteractsWithForms;

    public function render()
    {
        return view('livewire.import-fixtures');
    }
}
