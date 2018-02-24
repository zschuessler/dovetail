<?php

namespace SquareBit\Dovetail\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

use Symfony\Component\Process\Process;

class RunTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dovetail:test';

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Foundation\Composer
     */
    protected $composer;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs PHPUnit test suite for Dovetail package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packageDir  = realpath(__DIR__ . '/..');

        $processCommand = 'cd ' . $packageDir . '; phpunit';

        $process = new Process($processCommand);
        try {
            $process->mustRun();

            echo $process->getOutput();
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();
        }
    }
}
