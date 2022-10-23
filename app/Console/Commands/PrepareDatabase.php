<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Models\DocumentGroup;
use App\Models\LoanProduct;
use Illuminate\Console\Command;

class PrepareDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the database with the base information.';

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
		// Create Document Groups.
		$documentGroups = [
			'Profile',
			'Bank Statements',
			'Financials',
			'Closing Documents',
			'Contracts'
		];

		foreach ($documentGroups as $documentGroup) {
			DocumentGroup::firstOrCreate([
				'title' => $documentGroup
			]);
		}

		// Create Loan Products.
		$loanProducts = [
			'Short Term Loan',
			'Revenue Advance',
			'SBA Loan',
			'ABL Loan',
			'Term Loan',
			'CRE Loan',
			'Equipment Financing',
			'Line of Credit',
			'Invoice Factoring'
		];

		foreach ($loanProducts as $loanProduct) {
			LoanProduct::firstOrCreate([
				'slug' => Str::slug($loanProduct)
			], [
				'title' => $loanProduct,
			]);
		}

        return $this->info('The database is ready!');
    }
}
