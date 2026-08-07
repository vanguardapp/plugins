<?php

namespace Vanguard\Plugins;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Vanguard\Plugins\Contracts\Slot;

class Vanguard
{
    /**
     * All of the registered Vanguard plugins, keyed by class name.
     *
     * @var array<string, Plugin>
     */
    public static array $plugins = [];

    /**
     * All of the registered Vanguard dashboard widgets, keyed by class name.
     *
     * @var array<string, Widget>
     */
    public static array $widgets = [];

    /**
     * All registered UI slot handlers, keyed by position name.
     *
     * @var array<string, array<int, class-string<Slot>>>
     */
    public static array $slots = [];

    /**
     * Register a handler for a named UI slot position.
     *
     * Registering the same handler twice is a no-op. Handlers are declared as
     * class names, so a repeat only ever means the provider booted again --
     * under Octane, or between tests, where the static outlives the container.
     *
     * @param  class-string<Slot>  $handler
     */
    public static function slot(string $name, string $handler): void
    {
        if (in_array($handler, self::$slots[$name] ?? [], true)) {
            return;
        }

        self::$slots[$name][] = $handler;
    }

    /**
     * Determine whether any handler is registered for the given position.
     */
    public static function hasSlot(string $name): bool
    {
        return ! empty(self::$slots[$name]);
    }

    /**
     * The handler class names registered for the given position.
     *
     * @return array<int, class-string<Slot>>
     */
    public static function slotHandlers(string $name): array
    {
        return self::$slots[$name] ?? [];
    }

    /**
     * Resolve a slot position into serialisable payloads for Inertia.
     *
     * @param  array<string, mixed>  $context
     * @return array<int, array{component: string, props: array<string, mixed>}>
     */
    public static function resolveSlot(string $name, array $context = []): array
    {
        $resolved = [];

        foreach (self::slotHandlers($name) as $handler) {
            /** @var Slot $instance */
            $instance = resolve($handler);

            $resolved[] = [
                'component' => $instance->component(),
                'props' => $instance->data($context),
            ];
        }

        return $resolved;
    }

    /**
     * Register the given plugins.
     *
     * @param  array<string, Plugin>  $plugins
     */
    public static function plugins(array $plugins): void
    {
        self::$plugins = array_merge(self::$plugins, $plugins);
    }

    /**
     * Get the list of registered plugins.
     *
     * @return array<string, Plugin>
     */
    public static function availablePlugins(): array
    {
        return self::$plugins;
    }

    /**
     * Register the list of given dashboard widgets.
     *
     * Keyed by class name so that a provider booting twice replaces its
     * widgets rather than appending a second copy of each.
     *
     * @param  array<int, Widget>  $widgets
     */
    public static function widgets(array $widgets): void
    {
        foreach ($widgets as $widget) {
            self::$widgets[$widget::class] = $widget;
        }
    }

    /**
     * Get the list of widgets available for the provided user.
     *
     * @return Collection<int, Widget>
     */
    public static function availableWidgets(Authenticatable $user): Collection
    {
        return collect(self::$widgets)->filter->authorize($user)->values();
    }

    /**
     * Every plugin owned asset that the host layout must load.
     *
     * @return array<int, array{js?: string, css?: string}>
     */
    public static function assets(): array
    {
        return collect(self::$plugins)
            ->map(fn (Plugin $plugin) => $plugin->assets())
            ->filter(fn (array $assets) => ! empty($assets['js']) || ! empty($assets['css']))
            ->values()
            ->all();
    }

    /**
     * Reset the registry. Intended for use in tests.
     */
    public static function flush(): void
    {
        self::$plugins = [];
        self::$widgets = [];
        self::$slots = [];
    }
}
