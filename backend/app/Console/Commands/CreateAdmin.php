<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {--name=} {--email=} {--password=}';

    protected $description = 'Create a new admin account';

    public function handle(): void
    {
        $name     = $this->option('name')     ?? $this->ask('Name');
        $email    = $this->option('email')    ?? $this->ask('Email');
        $password = $this->option('password') ?? $this->secret('Password');

        if (Admin::where('email', $email)->exists()) {
            $this->error("An admin with email '{$email}' already exists.");
            return;
        }

        Admin::create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ]);

        $this->info("Admin '{$name}' created successfully.");
    }
}
