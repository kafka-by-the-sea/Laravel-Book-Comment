<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\CommentReportService;
use App\Services\CommentListService;

class CommentReportController extends Controller
{
    protected $commentreportService;

    public function __construct(CommentReportService $commentreportService, CommentListService $commentlistService)
    {
        $this->commentreportService = $commentreportService;
        $this->commentlistService = $commentlistService;
    }

    //取得檢舉列表
    public function index()
    {
        $reports = $this->commentreportService->getReportList();
        $total = $this->commentreportService->getStatasNumber();
        return view('comment.report', compact('reports', 'total'));
    }

    //小編檢舉通過
    public function addReport($commentID, $userID)
    {
        $this->commentreportService->addReport($commentID, $userID);
        return back();
    }

    //亂民檢舉通過但不扣分
    public function reportPass($reportID, $commentID, $userID)
    {
        $this->commentreportService->reportPass($reportID, $commentID, $userID);
        return redirect()->action('CommentReportController@index');
    }

    //亂民檢舉不通過
    public function reportFail($reportID)
    {
        $this->commentreportService->reportFail($reportID);
        return redirect()->action('CommentReportController@index');
    }

    //亂民檢舉通過且扣分
    public function reportSuccess($reportID, $commentID, $userID)
    {
        $this->commentreportService->reportSuccess($reportID, $commentID, $userID);
        return redirect()->action('CommentReportController@index');
    }

    //加入黑名單
    public function addBlackList($userID)
    {
        $this->commentlistService->addBlackList($userID);
        return redirect()->action('CommentReportController@index');
    }

    //刪除黑名單
    public function delBlackList($userID)
    {
        $this->commentlistService->delBlackList($userID);
        return redirect()->action('CommentReportController@index');
    }
}
