<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\ThemeManager\Commands;

use Fresns\ThemeManager\Theme;
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
            $this->call('theme:unzip', [
                'path' => $path,
            ]);

            $unikey = Cache::pull('install:theme_unikey');
            if (! $unikey) {
                info('Failed to unzip, couldn\'t get the theme unikey');

                return 0;
            }
            $theme = new Theme($unikey);
            $theme->manualAddNamespace();

            $type = $theme->getType();

            event('theme:installing', [[
                'unikey' => $unikey,
                'type' => $type,
            ]]);

            $this->call('theme:publish', [
                'name' => $theme->getStudlyName(),
            ]);

            // Triggers top-level computation of composer.json hash values and installation of extension packages
            $isOk = @exec('composer update');
            if ($isOk === false) {
                throw new \RuntimeException('Failed to install packages');
            }

            event('theme:installed', [[
                'unikey' => $unikey,
                'type' => $type,
            ]]);

            $this->info("Installed: {$theme->getStudlyName()}");
        } catch (\Throwable $e) {
            $this->error("Install fail: {$e->getMessage()}");
        }

        return 0;
    }
}
