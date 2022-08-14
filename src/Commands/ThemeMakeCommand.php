<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\ThemeManager\Commands;

use Illuminate\Console\Command;

class ThemeMakeCommand extends Command
{
    protected $signature = 'theme:make {name}
        {--force}
        ';

    protected $description = 'Alias of new command';

    public function handle()
    {
        $this->call('new-theme', [
            'name' => $this->argument('name'),
            '--force' => $this->option('force'),
        ]);
    }
}
