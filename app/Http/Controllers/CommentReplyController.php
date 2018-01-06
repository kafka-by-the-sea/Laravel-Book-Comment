<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CommentReplyService;

class CommentReplyController extends Controller
{
    protected $commentreplyService;

    public function __construct(CommentReplyService $commentreplyService)
    {
        $this->commentreplyService = $commentreplyService;
    }

    //取得回覆列表
    public function index()
    {
        $replys = $this->commentreplyService->getReplyList();
        return view('comment.reply', compact('replys'));
    }

    //將回覆訊息顯示或隱藏
    public function updateStatus($replyID, $status)
    {
        $this->commentreplyService->updateStatus($replyID, $status);
        return back();
    }

    //刪除回覆訊息
    public function deleteReply($replyID)
    {
        $this->commentreplyService->deleteReply($replyID);
        return back();
    }

    //搜尋結果與未處理筆數
    public function search(Request $request)
    {
        $data = $request->input('data');
        if ($data != "") {
             $replys = $this->commentreplyService->search($data);
             $pagination = $replys->appends(array('data' => $request->input('data')));
        }
        return view('comment.reply', compact('replys'));
    }
}
