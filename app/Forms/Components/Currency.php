<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class Currency extends Field
{
    protected string $view = 'forms.components.currency';

    public $prefix = '&pound;';

}
