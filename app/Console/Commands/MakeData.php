<?php

namespace App\Console\Commands;

use App\Models\Lender;
use App\Models\User;
use App\Models\Partner;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;

class MakeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the database with dummy data.';

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
		for ($i = 1; $i <= 40; $i++) {
			$factory = User::factory();

			$partnersNumber = 0;

			if ($i % 3 === 0) {
				$partnersNumber = Arr::random([1, 2]);

				$factory = $factory->has(
					Partner::factory()
						->state(['business_ownership' => 10 / $partnersNumber])
						->count($partnersNumber)
				);
			}

			$factory->create(['business_ownership' => $partnersNumber ? 90 : 100]);
		}

		$this->info('40 new Users were created successfully!');

		Lender::factory()
			->count(20)
			->create();

		$this->info('20 new Lenders were created successfully!');
    }
}
