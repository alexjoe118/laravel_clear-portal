@extends('layouts.dashboard', [
	'pageTitle' => 'Reports'
])

@section('button')
	@include('components.button', [
		'title' => 'Export Database',
		'url' => route('admin.report.export')
	])
@endsection

@section('page')
	<div class="reports">
		@foreach ($reports as $report)
			<div class="report">
				<h3 class="title">{{ $report['title'] }}</h3>

				<table>
					<thead>
						<tr>
							<th>Month/Year</th>
							<th>Value</th>
						</tr>
					</thead>

					<tbody>
						@foreach ($report['stats'] as $date => $stats)
							<tr>
								<td>{{ $date }}</td>
								<td class="stats js-stats">
									{{ count($stats) }}

									<div class="list js-list">
										@foreach ($stats as $stat)
											<a
												@if ($stat->deleted_at)
													class="deleted"
												@else
													href="{{ $stat->link }}"
												@endif>

												@if ($stat->user)
													{{ $stat->user->business->name }} -
												@endif

												{{ $stat->listingTitle }}
											</a>
										@endforeach
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endforeach
	</div>
@endsection
