<?php

namespace Vanguard\Plugins\Contracts;

/**
 * A slot injects a plugin owned Vue component into a named position in the
 * host UI (for example "navbar:items" or "user:show").
 *
 * Slots replace the Blade "@hook" directive from Vanguard 8 and below. Instead
 * of rendering HTML server side, a slot names a Vue component that the plugin
 * bundle registered with the Vanguard runtime, plus the props it should get.
 */
interface Slot
{
    /**
     * The Vue component registered by the plugin bundle, e.g. "announcements::NavbarBell".
     */
    public function component(): string;

    /**
     * The props handed to the component.
     *
     * Page scoped slots receive the host page's context, e.g. the "user:show"
     * position passes ['user' => User]. Global positions receive an empty array.
     *
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function data(array $context = []): array;
}
