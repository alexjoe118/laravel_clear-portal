<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Document;
use App\Models\LoanRequest;
use App\Models\OpenApproval;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;

class ReportController extends Controller
{
    /**
     * Displays stats of the portal usage.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
	{
		$reports = [];

		foreach ([
			'Applications' => [LoanRequest::class, true],
			'Approvals' => [OpenApproval::class, true],
			'Loans' => [Loan::class, true],
			'Documents' => [Document::class, false]
		] as $title => $class ) {
			$reports[] = [
				'title' => $title,
				'stats' => ($class[1] ? $class[0]::withTrashed()->get() : $class[0]::all())
					->groupBy(function ($value) {
						return Carbon::parse($value->created_at)->format('m/Y');
					})
					->mapWithKeys(function ($values, $date) {
						$values = $values->map(function ($value) {
							$model = Str::of($value->getTable())->singular()->slug();

							$value->link = route("admin.$model.edit", ['id' => $value->id]);
							return $value;
						});

						return [$date => $values];
					})
			];
		}

		return view('dashboard.admin.report-index', [
			'reports' => $reports
		]);
    }

	/**
     * Export a dump of the database as CSV.
     *
     * @return \Illuminate\Http\Response
     */
	public function export()
	{
		$zipName = 'db.zip';
		$zip = new ZipArchive;
		$zip->open(storage_path('app/public/' . $zipName), ZipArchive::CREATE);

		// Loop all the models and generate a CSV for each.
		foreach ([
			['Loan', ['contract_documents']],
			['Business', []],
			['LoanRequest', ['documents']],
			['OpenApproval', []],
			['User', ['new_email', 'email_verified_at', 'password', 'ssn_2', 'signature', 'remember_token']],
			['Partner', ['ssn_2', 'signature']]
		] as $model) {
			$file = fopen('php://temp/maxmemory:1048576', 'w');
			$modelClass = 'App\Models\\' . $model[0];

			$columns = DB::select('SHOW COLUMNS FROM ' . Str::of($model[0])->snake()->plural());

			$columnTitles = collect($columns)->filter(function($column) use($model) {
					return ! in_array($column->Field, $model[1]);
				})->map(function($column) {
					$column = (string) Str::of($column->Field)->replace('_id', '');

					// Make some specific columns names prettier.
					if ($column === 'id') return 'ID';
					if ($column === 'ssn_1') return 'SSN';
					else return Str::of($column)->headline();
				})
				->all();

			fputcsv($file, $columnTitles);

			foreach ($modelClass::all() as $row) {
				$fields = [];

				foreach ($columns as $column) {

					// Jump exceptions.
					if (in_array($column->Field, $model[1])) continue;

					// Get decrypted SSN value.
					if ($column->Field === 'ssn_1') {
						$value = $row->ssn_1 ? (Crypt::decrypt($row->ssn_1) . $row->ssn_2) : '';

					// Get pretty value instead of foreign key.
					} else if ($column->Key === 'MUL') {
						$relation = Str::of($column->Field)->before('_id')->camel();
						$value = isset($row->{$relation}) ? getTitle($row->{$relation}) : null;

					// Show "Yes" or "No" in columns with boolean values.
					} else if ($column->Type === 'tinyint(1)') {
						$value = $row->{$column->Field} ? 'Yes' : 'No';

					// Regular field value.
					} else {
						$value = $row->{$column->Field};
					}

					$fields[$column->Field] = is_array($value) ? json_encode($value) : $value;
				}

				fputcsv($file, $fields);
			}

			rewind($file);
			$zip->addFromString(Str::of($model[0])->plural()->kebab() . '.csv', stream_get_contents($file));
			fclose($file);
		}

		$zip->close();

    	return response()->download(storage_path('app/public/' . $zipName))->deleteFileAfterSend(true);
	}
}
