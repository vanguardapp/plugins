<?php

namespace Vanguard\Plugins;

use Illuminate\Support\ServiceProvider;
use Vanguard\Support\Sidebar\Item;

abstract class Plugin extends ServiceProvider
{
    /**
     * A sidebar item for the plugin.
     *
     * @return mixed|null
     */
    public function sidebar(): ?Item
    {
        return null;
    }

    /**
     * Boot all the necessary plugin stuff. Basically it will
     * work as a plugin service provider that should ensure that all the
     * necessary plugin stuff is loaded, so it can work properly.
     */
    public function boot(): void
    {
    }
}
