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
        $widgets = collect($this->widgets())->map(function ($class) {
            return $this->app->make($class);
        })->toArray();

        Vanguard::widgets($widgets);

        \Blade::directive('hook', function ($name) {
            return "<?php if (\Vanguard\Plugins\Vanguard::hasHook($name)) { 
                collect(\Vanguard\Plugins\Vanguard::getHookHandlers($name))
                    ->each(function (\$hook) {
                        echo resolve(\$hook)->handle();
                    });
            } ?>";
        });
    }

    /**
     * Dashboard widgets.
     */
    abstract protected function widgets(): array;

    /**
     * List of registered plugins.
     */
    abstract protected function plugins(): array;
}
