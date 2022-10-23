<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show the form for editing the specified or a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function form()
    {
		$settings = Settings::all()->mapWithKeys(function($row) {
			return [$row->key => $row->value];
		});

        return view('dashboard.admin.settings-form', [
			'settings' => $settings
		]);
    }

	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
		foreach ($request->settings as $key => $value) {
			if (! $value) {
				if ($existingValue = Settings::firstWhere('key', $key)) {
					$existingValue->delete();
				}

				continue;
			}

			if ($file = $request->file("settings.$key")) {
				$filename = "$key.{$file->extension()}";
				$file->storeAs('settings', $filename);
				$type = $file->getMimeType();
				$value = "settings/$filename";
			} else if (is_array($value)) {
				$type = 'array';
				$value = json_encode($value);
			} else {
				$type = 'string';
			}

			Settings::updateOrCreate(
				['key' => $key],
				['value' => $value, 'type' => $type]
			);
		}

		return $this->responseWithMessage('Global Settings', 'update', true);
    }
}
