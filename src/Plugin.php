<?php

namespace Vanguard\Plugins;

use App\Support\Sidebar\Item;
use Illuminate\Support\ServiceProvider;

abstract class Plugin extends ServiceProvider
{
    /**
     * A sidebar item for the plugin.
     */
    public function sidebar(): ?Item
    {
        return null;
    }

    /**
     * The compiled plugin bundle that the host layout should load.
     *
     * Plugin bundles externalise Vue, Inertia and PrimeVue and register their
     * pages, slots and widgets against the global Vanguard runtime, so the host
     * never needs rebuilding when a plugin is installed.
     *
     * @return array{js?: string, css?: string}
     */
    public function assets(): array
    {
        return [];
    }

    /**
     * Resolve a published Vite build into asset URLs.
     *
     * Expects the plugin to have published its build to
     * public/vendor/plugins/{slug} with `build.manifest` enabled.
     *
     * @return array{js?: string, css?: string}
     */
    protected function viteAssets(string $slug, string $entry = 'resources/js/plugin.ts'): array
    {
        $base = "vendor/plugins/{$slug}";

        $manifestPath = collect([
            public_path("{$base}/.vite/manifest.json"),
            public_path("{$base}/manifest.json"),
        ])->first(fn (string $path) => file_exists($path));

        if (! $manifestPath) {
            return [];
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);

        if (! is_array($manifest) || ! isset($manifest[$entry])) {
            return [];
        }

        $chunk = $manifest[$entry];
        $assets = [];

        if (! empty($chunk['file'])) {
            $assets['js'] = asset("{$base}/{$chunk['file']}");
        }

        if (! empty($chunk['css'][0])) {
            $assets['css'] = asset("{$base}/{$chunk['css'][0]}");
        }

        return $assets;
    }

    /**
     * Boot all the necessary plugin stuff. Basically it will
     * work as a plugin service provider that should ensure that all the
     * necessary plugin stuff is loaded, so it can work properly.
     */
    public function boot(): void {}
}
