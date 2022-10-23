@extends('layouts.dashboard.admin.resource-index', [
    'resource' => 'deleted-list',
    'create' => false,
])

@section('listing')
    
    {{-- loan requests --}}
    @include('components.page-subtitle-del', [
        'subtitle' => 'Loan Requests',
        'resource' => 'loan-request',
        'defaultValue' => $loanRequests_pageoption,
       
    ])
    @component('components.resource-listing-dellist', [
		// 'pagination' => $loanRequests_1
        'resource' => 'loan-request',
        'curpage' => $loanRequests_curpage,
        'pages' => $loanRequests_pages,
	])
		@foreach ($loanRequestsByUser as $user => $loanRequests)

			@foreach ($loanRequests as $loanRequest)
				@component('components.resource-listing-del-item', [
					'title' => $user.' - '.getTitle($loanRequest),
					'resource' => 'loan-request',
					'item' => $loanRequest,
					'actions' => [
						'notify',
						'edit',
						'delete' => [
							'confirmMessage' => 'The Open Approval(s) associated to this Loan Request will also be deleted.<br>The message below will be sent via email to the User.',
							'confirmFields' => [
								[
									'width' => 'full',
									'label' => 'Message',
									'type' => 'multiline',
									'input' => [
										'required' => false,
										'rows' => 2
									]
								]
							]
						]
					]
				])
					@foreach ($loanRequest->openApprovals as $openApproval)
						@include('components.resource-listing-item', [
							'resource' => 'open-approval',
							'item' => $openApproval,
							'actions' => ['notify', 'edit', 'delete']
						])
					@endforeach
				@endcomponent
			@endforeach
		@endforeach
	@endcomponent
    {{-- loan --}}
    
     @include('components.page-subtitle-del', [
        'subtitle' => 'Loans',
        'resource' => 'loan',
        'defaultValue' => $loans_pageoption,
    ])
    @component('components.resource-listing-dellist', [
		// 'pagination' => $loans
        'resource' => 'loan',
        'curpage' => $loans_curpage,
        'pages' => $loans_pages,
	])
		@foreach ($loansByUser as $user => $loans)
			@foreach ($loans as $loan)
				@include('components.resource-listing-del-item', [
                    'title' => $user.(' - ').getTitle($loan),
					'resource' => 'loan',
					'item' => $loan
				])
			@endforeach
		@endforeach
	@endcomponent

    {{-- open approvals --}}
  
    @include('components.page-subtitle-del', [
        'subtitle' => 'Open Approvals',
        'resource' => 'open-approval',
        'defaultValue' => $openApprovals_pageoption,
    ])
    @component('components.resource-listing-dellist', [
		// 'pagination' => $openApprovals
        'resource' => 'open-approval',
        'curpage' => $openApprovals_curpage,
        'pages' => $openApprovals_pages,
	])

		@foreach ($openApprovalsByUserAndType as $user => $openApprovalsByType)
			

			@foreach ($openApprovalsByType as $type => $openApprovals)
				@component('components.resource-listing-item', [
					'title' => $user.' - '.$type,
					'resource' => 'loan-request',
					'item' => $openApprovals[0]->loanRequest,
					'actions' => []
				])
					@foreach ($openApprovals as $openApproval)
						@include('components.resource-listing-item', [
							'resource' => 'open-approval',
							'item' => $openApproval,
							'actions' => ['create', 'notify', 'edit', 'delete']
						])
					@endforeach
				@endcomponent
			@endforeach
		@endforeach
	@endcomponent

    {{-- documents --}}
    
    @include('components.page-subtitle-del', [
        'subtitle' => 'Documents',
        'resource' => 'document',
        'defaultValue' => $documents_pageoption,
    ])
    @component('components.resource-listing-dellist', [
		// 'pagination' => $documents
        'resource' => 'document',
        'curpage' => $documents_curpage,
        'pages' => $documents_pages,
	])
		@foreach ($documentsByUser as $user => $documents)
			@foreach ($documents as $document)
				@include('components.resource-listing-del-item', [
                    'title' => $user.(' - ').getTitle($document),
					'resource' => 'document',
					'item' => $document
				])
			@endforeach
		@endforeach
	@endcomponent
	{{-- users --}}
    @include('components.page-subtitle-del', [
        'subtitle' => 'Users',
        'resource' => 'user',
        'defaultValue' => $users_pageoption,
    ])
	@component('components.resource-listing-dellist', [
		// 'pagination' => $users
        'resource' => 'user',
        'curpage' => $users_curpage,
        'pages' => $users_pages,
	])

		@foreach ($users as $user)
			@include('components.resource-listing-item', [
				'resource' => 'user',
				'item' => $user,
				'actions' => [
					'edit',
					'delete' => [
						'confirmMessage' => 'All related information such as Loans, Documents, Partners etc will also be deleted.'
					]
				]
			])
		@endforeach
	@endcomponent
{{-- </div> --}}
@endsection
<script>
    function getResult(resource, type) {
		url="/dashboard/admin/del-list?";
		loanRequest_curpage =$('#loan-request-curpage').val();
		loan_curpage = $('#loan-curpage').val();
		document_curpage = $('#document-curpage').val();
		openApproval_curpage = $('#open-approval-curpage').val();
		user_curpage = $('#user-curpage').val();

		loanRequest_option = $('#loan-request-pageoption').val();
		loan_option = $('#loan-pageoption').val();
		document_option = $('#document-pageoption').val();
		openApproval_option = $('#open-approval-pageoption').val();
		user_option = $('#user-pageoption').val();

		if(type == 'prev'){
			if(resource=='loan-request') loanRequest_curpage--;
			if(resource=='loan') loan_curpage--;
			if(resource=='document') document_curpage--;
			if(resource=='open-approval') openApproval_curpage--;
			if(resource=='user') user_curpage--;
		}
		if(type == 'next'){
			if(resource=='loan-request') loanRequest_curpage++;
			if(resource=='loan') loan_curpage++;
			if(resource=='document') document_curpage++;
			if(resource=='open-approval') openApproval_curpage++;
			if(resource=='user') user_curpage++;
		}
		url += "loanRequest_option="+loanRequest_option;
		url += "&loan_option="+loan_option;
		url += "&document_option="+document_option;
		url += "&openApproval_option="+openApproval_option;
		url += "&user_option="+user_option;
		
		url += "&loanRequest_curpage="+loanRequest_curpage;
		url += "&&loan_curpage="+loan_curpage;
		url += "&document_curpage="+document_curpage;
		url += "&openApproval_curpage="+openApproval_curpage;
		url += "&user_curpage="+user_curpage;
		url += "&resource="+resource;
		window.location.href=url;
    }
    // document.getElementById("focus-{{$resource}}").scrollIntoView(true);

    setTimeout(() => {
        $('html, body').animate({ scrollTop: $('#focus-{{$resource}}').offset().top}, 1000);
    }, 500);
    // $(document).ready(function(){


    //     $(window).load(function(){
    //     // Remove the # from the hash, as different browsers may or may not include it
    //         var hash = location.hash.replace('.','');

    //         if(hash != ''){

    //             // Clear the hash in the URL
    //             // location.hash = '';   // delete front "//" if you want to change the address bar
    //             $('html, body').animate({ scrollTop: $('.focus-{{$resource}}').offset().top}, 1000);

    //         }
    //     });
    // });
    
</script>