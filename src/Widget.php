<?php

namespace Vanguard\Plugins;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;

abstract class Widget
{
    /**
     * The number of columns that the widget should take on the dashboard.
     * Possible values are from 1 to 12. Set the width to NULL if you do
     * not want a width class applied.
     */
    public ?string $width = null;

    /**
     * Permissions required for viewing the widget.
     *
     * @var string|array<int, string>|Closure
     */
    protected string|array|Closure $permissions = [];

    /**
     * The Vue component registered by the plugin bundle,
     * e.g. "user-activity::ActivityWidget".
     */
    abstract public function component(): string;

    /**
     * The props handed to the widget component.
     *
     * @return array<string, mixed>
     */
    abstract public function data(): array;

    /**
     * Authorize the request to verify if a user should be able to
     * see the widget on the dashboard.
     */
    public function authorize(Authenticatable $user): bool
    {
        if ($this->permissions instanceof Closure) {
            return (bool) call_user_func($this->permissions, $user);
        }

        foreach ((array) $this->permissions as $permission) {
            if (! $user->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Set permissions required for viewing the widget on the dashboard.
     *
     * @param  string|array<int, string>|Closure  $permissions
     */
    public function permissions(string|array|Closure $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Serialise the widget for the dashboard Inertia page.
     *
     * @return array{component: string, props: array<string, mixed>, width: ?string}
     */
    public function toArray(): array
    {
        return [
            'component' => $this->component(),
            'props' => $this->data(),
            'width' => $this->width,
        ];
    }
}
