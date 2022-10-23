<?php

namespace App\Http\Controllers;


use App\Models\LoanRequest;
use App\Models\Loan;
use App\Models\Document;
use App\Models\OpenApproval;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class DelListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
	{
        $defaultcount = 20;
        $defaultpage = 1;
        // $request->pagecount
        $resource = 'loan-request';
        if(empty($request["loanRequest_curpage"])){
            $loanRequest_option = $defaultcount;
            $loan_option = $defaultcount;
            $document_option = $defaultcount;
            $openApproval_option = $defaultcount;
            $user_option = $defaultcount;

            $loanRequest_curpage = $defaultpage;
            $loan_curpage = $defaultpage;
            $document_curpage = $defaultpage;
            $openApproval_curpage = $defaultpage;
            $user_curpage = $defaultpage;
            $request["user_curpage"] = $resource;
        }else{
            $loanRequest_option = $request["loanRequest_option"];
            $loan_option = $request["loan_option"];
            $document_option = $request["document_option"];
            $openApproval_option = $request["openApproval_option"];
            $user_option = $request["user_option"];

            $loanRequest_curpage = $request["loanRequest_curpage"];
            $loan_curpage = $request["loan_curpage"];
            $document_curpage = $request["document_curpage"];
            $openApproval_curpage = $request["openApproval_curpage"];
            $user_curpage = $request["user_curpage"];
            
        }

        $loanRequests = LoanRequest::whereNotNull('deleted_at')->skip($loanRequest_option*($loanRequest_curpage-1))->take($loanRequest_option)->get();
        $loanRequestsByUser = $loanRequests->groupBy('user.listing_title')->sortKeys();
        $loanRequests_count = LoanRequest::whereNotNull('deleted_at')->count();

        $loans = Loan::whereNotNull('deleted_at')->skip($loan_option*($loan_curpage-1))->take($loan_option)->get();
		$loansByUser = $loans->groupBy('user.listing_title')->sortKeys();
        $loans_count = Loan::whereNotNull('deleted_at')->count();

        $documents = Document::whereNotNull('deleted_at')->skip($document_option*($document_curpage-1))->take($document_option)->get();
		$documentsByUser = $documents->groupBy('user.listing_title')->sortKeys();
        $documents_count = Document::whereNotNull('deleted_at')->count();

        $openApprovals = OpenApproval::whereNotNull('deleted_at')->orderBy('user_id')
        ->where('approval_expires', '>=', now())
        ->skip($openApproval_option*($openApproval_curpage-1))->take($openApproval_option)->get();

        

        $openApprovalsByUserAndType = $openApprovals->groupBy('user.listing_title')
            ->mapWithKeys(function($openApprovalsByType, $user) {
                return [$user => $openApprovalsByType->groupBy(function($openApproval) {
                    $expirationDate = Carbon::parse($openApproval->approval_expires)->format('m/d/Y');
                    $loanProduct = $openApproval->loanProduct->title;

                    return "$loanProduct - Expires At {$expirationDate}";
                })];
            })
            ->sortKeys();
        $openApprovals_count = OpenApproval::whereNotNull('deleted_at')->orderBy('user_id')
        ->where('approval_expires', '>=', now())->count();

        $users = User::whereNotNull('deleted_at')->where('role', 'user')->skip($user_option*($user_curpage-1))->take($user_option)->get();
        $users_count = User::whereNotNull('deleted_at')->count();

		return view('dashboard.admin.del-list-index', [
            // 'loanRequests' => $loanRequests,
            'loanRequestsByUser' => $loanRequestsByUser,
            'loanRequests_pages' => ceil($loanRequests_count/$loanRequest_option),
            'loanRequests_curpage' => $loanRequest_curpage,
            'loanRequests_pageoption' => $loanRequest_option,
            
            // 'loans' => $loans,
            'loansByUser' => $loansByUser,
            'loans_pages' => ceil($loans_count/$loan_option),
            'loans_curpage' =>  $loan_curpage,
            'loans_pageoption' => $loan_option,

            // 'documents' => $documents,
			'documentsByUser' => $documentsByUser,
            'documents_pages' => ceil($documents_count/$document_option),
            'documents_curpage' => $document_curpage,
            'documents_pageoption' => $document_option,
            
            // 'openApprovals' => $openApprovals ,
            'openApprovalsByUserAndType' =>$openApprovalsByUserAndType,
            'openApprovals_pages' => ceil($openApprovals_count/$openApproval_option),
            'openApprovals_curpage' => $openApproval_curpage,
            'openApprovals_pageoption' =>  $openApproval_option,

            'users' => $users,
            'users_pages' => ceil($users_count/$user_option),
            'users_curpage' => $user_curpage,
            'users_pageoption' => $user_option,
            'resource' => $request["resource"],
		]);
    }
    public function getPage(){
        $page = 1;
        $perpage = 2;
        if(!empty($_GET["page"])) {
            $page = $_GET["page"];
        }
        $start = ($page-1)*$perpage;
        if($start < 0) $start = 0;
        $count = LoanRequest::whereNotNull('deleted_at')->count();
        if($_GET['resource'] == 'loan-request'){
            $loanRequests_test = LoanRequest::whereNotNull('deleted_at')->skip($start)->take($perpage)->get();
            $loanRequestsByUser_test = $loanRequests_test->groupBy('user.listing_title')->sortKeys();
        }
        
        // return Response::json(array(
        //     'success' => true,
        //     'loanRequests_test'   => $loanRequests_test,
        //     'loanRequestsByUser_test' => $loanRequestsByUser_test,
        //     'pagenation' => getPrevNext($count, '/dashboard/admin/del-list/getPage?page='),
        //     'total' => ceil($count/$perpage),
        //     'page' => $page,
        // )); 
        return response()->json([
            'success' => true,
            'data'   => $loanRequests_test,
            'dataByUser_test' => $loanRequestsByUser_test,
            'pagenation' => $this->getPrevNext($count, '/dashboard/admin/del-list/getpage?page='),
            'total' => ceil($count/$perpage),
            'page' => $page,
        ]);
        

    }
    function getPrevNext($count,$href) {
        $perpage = 2;
		$output = '';
		if(!isset($_GET["page"])) $_GET["page"] = 1;
		if($perpage != 0)
			$pages  = ceil($count/$perpage);
		if($pages>1) {
			if($_GET["page"] == 1) 
				$output = $output . '';
			else	
				// $output = $output . '<a class="link first" onclick="getresult(\'' . $href . ($_GET["page"]-1) . '\')" >Prev</a>';			
                $output = $output . '<div><a class="button style-primary arrow-next js-button" onclick="getResult(\'' . $href . ($_GET["page"]-1) . '\')">
                <span class="button-wrapper"><span class="icon">Prev</span></span></a></div>';
			
			if($_GET["page"] < $pages)
                $output = $output . '<div><a class="button style-primary arrow-next js-button" onclick="getResult(\'' . $href . ($_GET["page"]+1) . '\')">
                <span class="button-wrapper"><span class="icon">Next</span></span></a></div>';
			else				
				// $output = $output . '<span class="link disabled">Next</span>';
                $output = $output . '';
            $output = $output . '<span class="status">Page '.$_GET["page"].' of '.$pages.'</span>';
		}

		return $output;
	}

}
