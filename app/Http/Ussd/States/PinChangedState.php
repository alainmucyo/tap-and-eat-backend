<?php

namespace App\Http\Ussd\States;

use Sparors\Ussd\State;

class PinChangedState extends State
{
    protected function beforeRendering(): void
    {
        $this->menu->text('END Your PIN has been changed successfully');
    }

    protected function afterRendering(string $argument): void
    {
        //
    }
}
