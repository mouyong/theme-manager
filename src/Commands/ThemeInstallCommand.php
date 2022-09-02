<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\ThemeManager\Commands;

use Fresns\ThemeManager\Theme;
use Fresns\ThemeManager\Support\Json;
use Fresns\ThemeManager\Support\Process;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ThemeInstallCommand extends Command
{
    protected $signature = 'theme:install {path}
        {--seed}
        ';

    protected $description = 'Install the theme from the specified path';

    public function handle()
    {
        try {
            $path = $this->argument('path');
            if (! str_contains($path, config('plugins.paths.plugins'))) {
                $this->call('theme:unzip', [
                    'path' => $path,
                ]);

                $unikey = Cache::pull('install:plugin_unikey');
            } else {
                $unikey = dirname($path);
            }

            if (! $unikey) {
                info('Failed to unzip, couldn\'t get the theme unikey');

                return 0;
            }

            $theme = new Theme($unikey);
            if (! $theme->isValidTheme()) {
                $this->error('theme is invalid');

                return 0;
            }

            $theme->manualAddNamespace();

            event('theme:installing', [[
                'unikey' => $unikey,
            ]]);

            $this->call('theme:publish', [
                'name' => $theme->getStudlyName(),
            ]);

            if (file_exists($theme->getComposerJsonPath())) {
                $composerJson = Json::make($theme->getComposerJsonPath())->get();
                $require = Arr::get($composerJson, 'require', []);
                $requireDev = Arr::get($composerJson, 'require-dev', []);

                // Triggers top-level computation of composer.json hash values and installation of extension packages
                // @see https://getcomposer.org/doc/03-cli.md#process-exit-codes
                if (count($require) || count($requireDev)) {
                    $process = Process::run('composer update', $this->output);
                    if (! $process->isSuccessful()) {
                        $this->error('Failed to install packages, calc composer.json hash value fail');

                        return 0;
                    }
                }
            }

            event('theme:installed', [[
                'unikey' => $unikey,
            ]]);

            $this->info("Installed: {$theme->getStudlyName()}");
        } catch (\Throwable $e) {
            $this->error("Install fail: {$e->getMessage()}");
        }

        return 0;
    }
}
