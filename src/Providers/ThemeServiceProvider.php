<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\ThemeManager\Providers;

use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->autoload();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/themes.php', 'themes');
        $this->publishes([
            __DIR__.'/../../config/themes.php' => config_path('themes.php'),
        ], 'laravel-theme-config');

        $this->addMergePluginConfig();

        $this->registerCommands([
            __DIR__.'/../Commands/*',
        ]);
    }

    public function registerCommands($paths)
    {
        $allCommand = [];

        foreach ($paths as $path) {
            $commandPaths = glob($path);

            foreach ($commandPaths as $command) {
                $commandPath = realpath($command);
                if (! is_file($commandPath)) {
                    continue;
                }

                $commandClass = 'Fresns\\ThemeManager\\Commands\\'.pathinfo($commandPath, PATHINFO_FILENAME);

                if (class_exists($commandClass)) {
                    $allCommand[] = $commandClass;
                }
            }
        }

        $this->commands($allCommand);
    }

    protected function autoload()
    {
        $this->addFiles();
    }

    protected function addFiles()
    {
        $files = $this->app['config']->get('themes.autoload_files');

        foreach ($files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }

    protected function addMergePluginConfig()
    {
        $composerPath = base_path('composer.json');
        $composer = Json::make($composerPath)->get();

        $userMergePluginConfig = Arr::get($composer, 'extra.merge-plugin', []);

        $defaultMergePlugin = config('themes.merge_plugin_config');

        $mergePluginConfig = array_merge($defaultMergePlugin, $userMergePluginConfig);

        // merge include
        $diffInclude = array_diff($defaultMergePlugin['include'], $userMergePluginConfig['include']);
        $mergePluginConfigInclude = array_merge($diffInclude, $userMergePluginConfig['include']);

        $mergePluginConfig['include'] = $mergePluginConfigInclude;

        Arr::set($composer, 'extra.merge-plugin', $mergePluginConfig);

        try {
            $content = Json::make()->encode($composer);

            file_put_contents($composerPath, $content);
        } catch (\Throwable $e) {
            $message = str_replace(['file_put_contents('.base_path().'/', ')'], '', $e->getMessage());
            throw new \RuntimeException('cannot set merge-plugin to '.$message);
        }
    }
}
