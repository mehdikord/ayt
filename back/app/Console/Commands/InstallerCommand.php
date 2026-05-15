<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

#[Signature('app:installer')]
#[Description('Command description')]
class InstallerCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Installing system ...');
        $this->info('Running database migrations');

        $migrationExitCode = $this->call('migrate', ['--force' => true]);
        if ($migrationExitCode !== SymfonyCommand::SUCCESS) {
            $this->error('Migration failed. Installer aborted.');
            return $migrationExitCode;
        }

        $this->info('Create sample admin account');
        Admin::query()->updateOrCreate(
            ['phone' => '09117926950'],
            [
                'name' => 'Administrator',
                'image' => null,
                'password_hash' => Hash::make('123456'),
                'is_active' => true,
            ]
        );

        $this->info('Installer completed successfully.');
        return SymfonyCommand::SUCCESS;
    }
}
