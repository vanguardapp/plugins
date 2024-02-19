<?php

namespace Vanguard\Plugins;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;

abstract class Widget
{
    /**
     * The number of columns that widget should take on the dashboard.
     * Possible values are from 1 to 12. Set the width to NULL
     * if you don't want to disable the width class.
     */
    public ?string $width = null;

    /**
     * Permissions required for viewing the widget.
     */
    protected string|array|Closure $permissions;

    /**
     * Renders the widget HTML.
     */
    abstract public function render(): View;

    /**
     * Authorize the request to verify if a user should be able to
     * see the widget on the dashboard.
     *
     * @return bool
     */
    public function authorize(Authenticatable $user)
    {
        if ($this->permissions instanceof Closure) {
            return call_user_func($this->permissions, $user);
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
     */
    public function permissions($permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Custom scripts that are required by this widget to work
     * and that should be rendered on the dashboard only.
     */
    public function scripts(): ?View
    {
        return null;
    }
}
