<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CommentListService;
use App\Repositories\CommentListRepository;

class CommentListController extends Controller
{
    protected $commentlistService;
    protected $commentlistRepository;

    public function __construct(CommentListService $commentlistService, CommentListRepository $commentlistRepository)
    {
        $this->commentlistService = $commentlistService;
        $this->commentlistRepository = $commentlistRepository;
    }

    //取得列表與未處理筆數
    public function index()
    {
        $comments = $this->commentlistService->getCommentList();
        $total = $this->commentlistService->getStatusNumber();
        return view('comment.index', compact('comments', 'total'));
    }

    //取得未審核評論
    public function getStatusComments()
    {
        $comments = $this->commentlistService->getStatusComments();
        $total = $this->commentlistService->getStatusNumber();
        return view('comment.index', compact('comments', 'total'));
    }

    //取得黑名單評論
    public function getBlackListComments()
    {
        $comments = $this->commentlistService->getBlackListComments();
        $total = $this->commentlistService->getStatusNumber();
        return view('comment.index', compact('comments', 'total'));
    }

    //搜尋結果與未處理筆數
    public function search(Request $request)
    {
        $data = $request->input('data');
        if ($data != "") {
             $comments = $this->commentlistService->search($data);
             $pagination = $comments->appends(array('data' => $request->input('data')));
        }

        $total = $this->commentlistService->getStatusNumber();
        return view('comment.index', compact('comments', 'total'));
    }

    //通過
    public function pass($commentID)
    {
        $this->commentlistService->pass($commentID);
        return back();
    }

    //不通過
    public function fail($commentID)
    {
        $this->commentlistService->fail($commentID);
        return back();
    }

    //加入黑名單
    public function addBlackList($userID)
    {
        $this->commentlistService->addBlackList($userID);
        return back();
    }

    //刪除黑名單
    public function delBlackList($userID)
    {
        $this->commentlistService->delBlackList($userID);
        return back();
    }

    //回到審核中
    public function editStatus($commentID, $userID, $status)
    {
        $this->commentlistService->editStatus($commentID, $userID, $status);
        return back();
    }

    //回覆訊息
    public function replyMessage(Request $request)
    {
        $commentID = $request->input('commentID');
        $message = $request->input('message');
        $this->commentlistService->replyMessage($commentID, $message);
        return back();
    }

    //加讚
    public function addPraise($commentID)
    {
        $this->commentlistService->addPraise($commentID);
        return back();
    }

    //設成劇透
    public function spoiler($commentID)
    {
        $this->commentlistService->spoiler($commentID);
        return back();
    }

    //取消劇透
    public function undoSpoiler($commentID)
    {
        $this->commentlistService->undoSpoiler($commentID);
        return back();
    }

    //全部通過
    public function checkPass(Request $request)
    {
        $idstr = $request->input('idstr');
        $idArray = explode(",", $idstr);
        $idArray = array_reverse($idArray);
        $length = count($idArray);
        for ($i = 0; $i < $length; $i++) {
            $this->commentlistService->pass($idArray[$i]);
        }
        /*
        $idstr = $request->input('idstr');
        $idArray = explode(",", $idstr);
        $idArray = array_reverse($idArray);
        $length = count($idArray);

        for ($i = 0; $i < $length; $i++) {
            $this->commentlistService->pass($idArray[$i]);
            $arr = $this->commentlistRepository->getComment($idArray[$i]);
            foreach ($arr as $item) {
                $result['id'] = $item->id;
                $result['uid'] = $item->uid;
                $result['status'] = $item->status;
                $result['isScore'] = $item->isScore;
                $result['report_id'] = $item->report_id;
            }
            $data[] = $result;
        }
        return json_encode($data);
        */
    }

    //全部不通過
    public function checkFail(Request $request)
    {
        $idstr = $request->input('idstr');
        $idArray = explode(",", $idstr);
        $idArray = array_reverse($idArray);
        $length = count($idArray);
        for ($i = 0; $i < $length; $i++) {
            $this->commentlistService->fail($idArray[$i]);
        }
    }
}
