<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an user with manager privileges.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$email = $this->ask('Email');
		$firstName = $this->ask('First Name');
		$lastName = $this->ask('Last Name');
		$password = $this->secret('Password');

		// Return if the email is already being used.
		if (User::where('email', $email)->exists()) {
			return $this->error('This email belongs to an existing user.');
		}

		// Create the admin user.
        $user = User::forceCreate([
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'password' => Hash::make($password),
			'role' => 'manager',
			'email_verified_at' => now()
        ]);

        return $this->info('The admin user was created successfully!');
    }
}
