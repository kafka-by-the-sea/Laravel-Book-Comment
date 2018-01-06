<?php
namespace App\Services;

use App\Repositories\CommentListRepository;
use App\Repositories\UserScoreRepository;
use App\Repositories\CommentReplyRepository;
use App\Repositories\CommentPraiseRepository;

class CommentListService
{
    protected $commentlistRepository;
    protected $userscoreRepository;
    protected $commentreplyRepository;
    protected $commentpraiseRepository;
  
    public function __construct(
        CommentListRepository $commentlistRepository,
        UserScoreRepository $userscoreRepository,
        CommentReplyRepository $commentreplyRepository,
        CommentPraiseRepository $commentpraiseRepository
    ) {
    
        $this->commentlistRepository = $commentlistRepository;
        $this->userscoreRepository = $userscoreRepository;
        $this->commentreplyRepository = $commentreplyRepository;
        $this->commentpraiseRepository = $commentpraiseRepository;
    }

    //取得評論列表
    public function getCommentList()
    {
        return $this->commentlistRepository->getComments();
    }

    //取得未審核評論
    public function getStatusComments()
    {
        return $this->commentlistRepository->getStatusComments();
    }

    //取得黑名單評論
    public function getBlackListComments()
    {
        return $this->commentlistRepository->getBlackListComments();
    }

    //取得未處理的筆數
    public function getStatusNumber()
    {
        return $this->commentlistRepository->getStatusNumber();
    }

    //搜尋
    public function search($data)
    {
        return $this->commentlistRepository->search($data);
    }

    //通過
    public function pass($commentID)
    {
        //取得該評論的userID
        $userID = $this->commentlistRepository->getUserID($commentID);
        //取得該評論的seriesID
        $seriesID = $this->commentlistRepository->getSeriesID($commentID);

        //取得目前的積分
        $score = $this->userscoreRepository->getScore($userID);
        //判斷是否是二次評論, 如果是>0 表示是2次評論
        $commentCount = $this->commentlistRepository->getCommentCount($userID, $seriesID, $commentID);

        if ($commentCount > 0) {
            //updateStatus($status, $isScore, $commentID) 4:二次評論 0:沒送
            $this->commentlistRepository->updateStatus('4', '0', $commentID);
        } else {
            //是否有消費 0:沒消費 1以上:有消費
            $isBuy = $this->commentlistRepository->isBuy($userID, $seriesID);
            if ($isBuy > 0) {
                //1:通過 1:有送
                $this->commentlistRepository->updateStatus('1', '1', $commentID);
                $today = date("Y-m-d H:i:s");
                //書評大賽雙倍積分
                if ($today > '2017-08-01 12:00:00' and $today <= '2017-08-31 12:00:00') {
                    $addScore = 50;
                    //目前總積分
                    $totalScore = $score + $addScore;
                    $memo = '';
                    $action = 'comment';
                    $this->userscoreRepository->insertScore($userID, $action, $addScore, $totalScore, $commentID, $memo);
                } else {
                    //一般送25積分
                    $addScore = 25;
                    //目前總積分
                    $totalScore = $score + $addScore;
                    $memo = '';
                    $action = 'comment';
                    $this->userscoreRepository->insertScore($userID, $action, $addScore, $totalScore, $commentID, $memo);
                }
            } else {
                //沒買，未消費不送積分
                //3:未消費 0:沒送
                $this->commentlistRepository->updateStatus('3', '0', $commentID);
            }
        }
    }

    //未通過
    public function fail($commentID)
    {
        //2:未通過 0:沒送
        $this->commentlistRepository->updateStatus('2', '0', $commentID);
    }

    //加入黑名單
    public function addBlackList($userID)
    {
        //1:設成黑名單
        $this->commentlistRepository->updateBlackStatus('1', $userID);
    }

    //刪除黑名單
    public function delBlackList($userID)
    {
        //0:正常名單
        $this->commentlistRepository->updateBlackStatus('0', $userID);
    }

    //回到審核中
    //$status = 1:通過 2:未通過 3:未消費 4:二次評論
    public function editStatus($commentID, $userID, $status)
    {
        //取得目前的積分
        $score = $this->userscoreRepository->getScore($userID);
        //目前的時間
        $t = time();
        if ($status == 2 || $status == 3 || $status == 4) {
            //狀態回到 0:未審核 0:沒送
            $this->commentlistRepository->updateStatus('0', '0', $commentID);
        } elseif ($status == 1) {
             //狀態回到 0:未審核 0:沒送
            $this->commentlistRepository->updateStatus('0', '0', $commentID);
            $today = date("Y-m-d H:i:s");
            //書評大賽積分雙倍送
            if ($today > '2017-08-01 12:00:00' and $today <= '2017-08-31 12:00:00') {
                $minusScore = -50;
                $totalScore = $score + $minusScore;
                $memo = '評論重新審核，扣回原積分';
                $action = 'comment';
                $this->userscoreRepository->insertScore($userID, $action, $minusScore, $totalScore, $commentID, $memo);
            } else {
                //一般扣25積分
                $minusScore = -25;
                $totalScore = $score + $minusScore;
                $memo = '評論重新審核，扣回原積分';
                $action = 'comment';
                $this->userscoreRepository->insertScore($userID, $action, $minusScore, $totalScore, $commentID, $memo);
            }
        }
    }

    //回覆訊息
    public function replyMessage($commentID, $message)
    {
        $this->commentreplyRepository->insertMessage($commentID, $message);
    }

    //加讚
    public function addPraise($commentID)
    {
        //取得目前的讚數
        $praise = $this->commentlistRepository->getPraise($commentID);
        //更新目前的讚數(+5)
        $this->commentlistRepository->updatePraise($praise, $commentID);
        //小編加讚
        $userID = '0';
        $t = time();
        //新增加讚紀錄
        $this->commentpraiseRepository->insertPraise($userID, $commentID, $t);
    }

    //設成劇透
    public function spoiler($commentID)
    {
        $this->commentlistRepository->updateSpoiler($commentID, '1');
    }

    //取消劇透
    public function undoSpoiler($commentID)
    {
        $this->commentlistRepository->updateSpoiler($commentID, '0');
    }
}
