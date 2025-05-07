<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class HelperCreator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:helper {name} {--invokable} {--i}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new helper file and base helper class if not already present';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $helpersPath = app_path('Helpers');
        $helperFilePath = $helpersPath . '/' . $name . '.php';
        $baseHelperPath = $helpersPath . '/Helper.php';
        $kernelPath = $helpersPath . '/kernel.php';
        

        if (!File::exists($helpersPath)) {
            File::makeDirectory($helpersPath, 0755, true);
        }

        if (!File::exists($baseHelperPath)) {
            $baseStub = file_get_contents(__DIR__ . '/stubs/base-helper.stub');
            File::put($baseHelperPath, $baseStub);
        }

        if (!File::exists($kernelPath)) {
            $kernelStub = file_get_contents(__DIR__ . '/stubs/kernel.stub');
            File::put($kernelPath, $kernelStub);
        }


        // Prevent overwriting
        if (File::exists($helperFilePath)) {
            $this->error($name . ' helper file already exists!');
            return;
        }

        // Create specific helper file from stub
        if ($this->option('invokable') || $this->option('i')) {
            $stub = file_get_contents(__DIR__ . '/stubs/helper-invokable.stub');
        } else {
            $stub = file_get_contents(__DIR__ . '/stubs/helper.stub');
        }
        $stub = str_replace('{{ name }}', $name, $stub);
        $stub = str_replace('{{ extends }}', 'Helper', $stub);

        File::put($helperFilePath, $stub);
        $this->info($name . ' helper file created successfully!');
    }
}
