<?php

namespace Vanguard\Plugins;

use Illuminate\Support\ServiceProvider;

abstract class VanguardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $instances = [];

        foreach ($this->plugins() as $plugin) {
            $instances[$plugin] = $this->app->register($plugin);
        }

        Vanguard::plugins($instances);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $widgets = collect($this->widgets())
            ->map(fn (string $class) => $this->app->make($class))
            ->all();

        Vanguard::widgets($widgets);
    }

    /**
     * Dashboard widgets.
     *
     * @return array<int, class-string<Widget>>
     */
    abstract protected function widgets(): array;

    /**
     * List of registered plugins.
     *
     * @return array<int, class-string<Plugin>>
     */
    abstract protected function plugins(): array;
}
