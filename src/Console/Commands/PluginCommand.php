<?php

namespace Vanguard\Plugins\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

abstract class PluginCommand extends Command
{
    /**
     * Create a new controller creator command instance.
     *
     * @return void
     */
    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the plugin.'],
        ];
    }

    /**
     * Get the desired class name from the input.
     */
    protected function getNameInput(): string
    {
        return trim($this->argument('name'));
    }

    /**
     * Get the root namespace for the class.
     */
    protected function rootNamespace(): string
    {
        return rtrim($this->laravel->getNamespace(), '\\');
    }

    /**
     * The namespace of the plugin itself.
     *
     * @return string
     */
    protected function pluginNamespace()
    {
        return sprintf("%s\%s", $this->rootNamespace(), $this->studlyName());
    }

    /**
     * Build the directory for the class if necessary.
     */
    protected function makeDirectory(string $path): string
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    /**
     * The path to the plugin directory.
     */
    protected function pluginPath(): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $this->getNameInput());

        return $this->laravel['path.base'].'/plugins/'.str_replace('\\', '/', Str::studly($name));
    }

    /**
     * Check if plugin exists on a given path.
     *
     * @param  string  $pluginPath
     */
    protected function pluginExists($pluginPath): bool
    {
        return $this->files->exists($pluginPath);
    }

    /**
     * Name of the plugin in StudlyCase format.
     */
    protected function studlyName(): string
    {
        return Str::studly($this->getNameInput());
    }

    /**
     * Name of the plugin in snake-case format.
     */
    protected function snakeName(): string
    {
        return Str::snake($this->getNameInput(), '-');
    }
}
