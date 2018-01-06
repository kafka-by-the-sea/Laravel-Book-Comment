<?php
namespace App\Services;

use App\Repositories\CommentReportRepository;
use App\Repositories\CommentListRepository;
use App\Repositories\UserScoreRepository;

class CommentReportService
{
    protected $commentreportRepository;
    protected $commentlistRepository;
    protected $userscoreRepository;
  
    public function __construct(
        CommentReportRepository $commentreportRepository,
        CommentListRepository $commentlistRepository,
        UserScoreRepository $userscoreRepository
    ) {
        $this->commentreportRepository = $commentreportRepository;
        $this->commentlistRepository = $commentlistRepository;
        $this->userscoreRepository = $userscoreRepository;
    }

    //取得檢舉列表
    public function getReportList()
    {
        return $this->commentreportRepository->getReports();
    }

    //取得未處理的筆數
    public function getStatasNumber()
    {
        return $this->commentreportRepository->getStatasNumber();
    }

    //小編直接檢舉的情況(在評論列表操作)
    public function addReport($commentID, $userID)
    {
        $reason = '小編檢舉';
        //檢舉狀態 0:未處理 1:通過 2:不成立
        $status = '1';
        //將檢舉的狀態設為通過
        $this->commentreportRepository->insertReport('0', $commentID, $reason, $status);
        
        //取得reportID
        $reportID = $this->commentreportRepository->getReportID($commentID);
        //到book_comment的report_id欄位加入reportID
        $this->commentlistRepository->updateReportID($reportID, $commentID);
        
        //取得原評論人最新的積分
        $score = $this->userscoreRepository->getScore($userID);
        //取得目前的狀態 0:審核中 1:通過 2:未通過 3:未消費 4: 二次評論
        $status = $this->commentlistRepository->getStatus($commentID);
       
        $memo = '評論違規，倒扣積分';

        //狀態是已經通過，有送積分的情況
        if ($status == 1) {
            //取得該評論原先被扣除的積分
            $mvScore = $this->userscoreRepository->getmvScore($commentID);
            //增加此判斷是因為DB有的資料會是空的(找不到 memo='$commmetID')，避免頁面直接出現error
            if (!empty($mvScore)) {
                //確定有資料才抓原先被扣除的積分
                $mvscore = $mvScore[0]->mv_score;
                //總共要扣的積分: 原積分 + 要扣除的10積分
                $totalScore = $mvscore + 10;
                $action = 'report';
                //扣除積分
                $this->userscoreRepository->insertScore($userID, $action, -($totalScore), $score - $totalScore, $commentID, $memo);
                //原評論status設為2:不通過，且isScore設為"2:檢舉後被扣除"
                $this->commentlistRepository->updateStatus('2', '2', $commentID);
            }
        } elseif ($status == 0) {
            //還在審核中就被小編發現的情況
            //該評論倒扣10積分
            $action = 'report';
            $this->userscoreRepository->insertScore($userID, $action, '-10', $score - 10, $commentID, $memo);
            //原評論status設為2:不通過，且isScore設為"2:檢舉後被扣除"
            $this->commentlistRepository->updateStatus('2', '2', $commentID);
        } else {
            //狀態是未通過/未消費/二次評論的情況
            //該評論倒扣10積分
            $action = 'report';
            $this->userscoreRepository->insertScore($userID, $action, '-10', $score - 10, $commentID, $memo);
        }
    }

    //亂民檢舉，通過但不扣分:(1)原檢舉人加1積分 (2)該評論設為不通過
    public function reportPass($reportID, $commentID, $userID)
    {
        //將檢舉的狀態設為通過
        $this->commentreportRepository->setStatus('1', $reportID);
        //到book_comment的report_id欄位加入reportID
        $this->commentlistRepository->updateReportID($reportID, $commentID);
        //檢舉人+1積分
        $score = $this->userscoreRepository->getScore($userID);
        $memo = '檢舉成功獲得檢舉獎勵積分';
        $t = time();
        $action = 'report';
        $this->userscoreRepository->insertScore($userID, $action, '1', $score + 1, $commentID, $memo);
        //原評論status設為2:不通過，且isScore設為"3:檢舉但不扣分"
        $this->commentlistRepository->updateStatus('2', '3', $commentID);
    }

    //在檢舉列表不通過
    public function reportFail($reportID)
    {
        //將檢舉的狀態設為不通過
        $this->commentreportRepository->setStatus('2', $reportID);
    }

    //亂民檢舉成功:
    //(1)if 是因為劇透，被檢舉的評論不須隱藏，且設成劇透，檢舉人加1積分
    //   else 扣除原評論原本積分，並倒扣10積分，原評論設為不通過，檢舉人加5積分
    public function reportSuccess($reportID, $commentID, $userID)
    {
        //$userID是檢舉人

        //將檢舉的狀態設為通過
        $this->commentreportRepository->setStatus('1', $reportID);


        //取得檢舉原因
        $reason = $this->commentreportRepository->getReason($userID, $commentID);
        if ($reason == '有劇透') {
            //如果有劇透，把原評論設為有劇透
            $this->commentlistRepository->updateSpoiler($commentID, '1');

            //取得檢舉人的最新積分
            $score = $this->userscoreRepository->getScore($userID);
            $memo = '檢舉成功獲得檢舉獎勵積分';
            //新增積分紀錄(檢舉人+1積分)
            $action = 'report';
            $this->userscoreRepository->insertScore($userID, $action, '1', $score + 1, $commentID, $memo);
        } else {
            //取得檢舉人的最新積分
            $score = $this->userscoreRepository->getScore($userID);
            $memo = '檢舉成功獲得檢舉獎勵積分';

            //新增積分紀錄(檢舉人+5積分)
            $action = 'report';
            $this->userscoreRepository->insertScore($userID, $action, '5', $score + 5, $commentID, $memo);

            //取得該評論原先被扣除的積分
            $mvScore = $this->userscoreRepository->getmvScore($commentID);
            //增加此判斷是因為DB有的資料會是空的(找不到 memo='$commmetID')，避免頁面直接出現error
            if (!empty($mvScore)) {
                //確定有資料才抓取原先評論者的userID(這是原評論者)
                $commentUserID = $this->userscoreRepository->getCommentUserID($commentID);
                //原評論者的最新積分
                $commentUserScore = $this->userscoreRepository->getScore($commentUserID);
                //確定有資料才抓原先被扣除的積分
                $mvscore = $mvScore[0]->mv_score;
                //總共要扣的積分: 原積分 + 要扣除的10積分
                $totalScore = $mvscore + 10;
                //扣除原評論原本積分，並倒扣10積分
                $commentMemo = '評論違規，倒扣積分';
                $action = 'report';
                $this->userscoreRepository->insertScore($commentUserID, $action, -($totalScore), $commentUserScore - $totalScore, $commentID, $commentMemo);
                //原評論status設為2:不通過，且isScore設為"2:檢舉後被扣除"
                $this->commentlistRepository->updateStatus('2', '2', $commentID);
            }
        }
    }
}
