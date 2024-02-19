<?php

namespace Vanguard\Plugins\Contracts;

use Illuminate\Contracts\View\View;

interface Hook
{
    /**
     * Execute the hook action.
     */
    public function handle(): View;
}
