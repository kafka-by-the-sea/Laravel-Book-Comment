<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

use App\Models\Account;
use App\Models\Comment;
use App\Models\Series;

class CommentListRepository
{
    //取得comment內容
    public function getComments()
    {
        $comments = DB::table('book_comment')
                        ->leftJoin('db_user.t_account', 'book_comment.uid', '=', 't_account.user_id')
                        ->leftJoin('db_cms.series', 'series.id', '=', 'book_comment.series_id')
                        ->select('book_comment.*', 't_account.name', 't_account.blacklist as blackName', 'series.name as seriesName')
                        //->where('book_comment.uid', '>', '1000')
                        ->orderBy('datetime', 'desc')
                        ->paginate(50);
        
        return $comments;
    }

    //取得單筆更新後的comment(for JSON)
    public function getComment($commentID)
    {
        $comment = DB::select ('select id, uid, status, isScore, report_id from book_comment where id = :id', ['id' => $commentID]);
        return $comment;
    }

    //取得未審核評論
    public function getStatusComments()
    {
        $comments = DB::table('book_comment')
                        ->leftJoin('db_user.t_account', 'book_comment.uid', '=', 't_account.user_id')
                        ->leftJoin('db_cms.series', 'series.id', '=', 'book_comment.series_id')
                        ->select('book_comment.*', 't_account.name', 't_account.blacklist as blackName', 'series.name as seriesName')
                        //->where('book_comment.uid', '>', '1000')
                        ->where('book_comment.status', '=', '0')
                        ->where('book_comment.datetime', '>', '2017-08-01 00:00:00')
                        ->orderBy('datetime', 'desc')
                        ->paginate(50);
        return $comments;
    }

    //取得黑名單列表
    public function getBlackListComments()
    {
        $comments = DB::table('book_comment')
                        ->leftJoin('db_user.t_account', 'book_comment.uid', '=', 't_account.user_id')
                        ->leftJoin('db_cms.series', 'series.id', '=', 'book_comment.series_id')
                        ->select('book_comment.*', 't_account.name', 't_account.blacklist as blackName', 'series.name as seriesName')
                        ->where('t_account.blacklist', '=', '1')
                        //->where('book_comment.datetime', '>', '2017-08-01 00:00:00')
                        ->orderBy('datetime', 'desc')
                        ->paginate(50);
        return $comments;
    }

     //取得未審核的筆數
    public function getStatusNumber()
    {
        $total = DB::select ('select count(*) as count from book_comment where status = 0 and datetime > :datetime', ['datetime' => '2017-08-01 00:00:00']);
        return $total;
    }

    //搜尋
    public function search($data)
    {
        $data = DB::table('book_comment')
                    ->leftJoin('db_user.t_account', 'book_comment.uid', '=', 't_account.user_id')
                    ->leftJoin('db_cms.series', 'series.id', '=', 'book_comment.series_id')
                    ->select('book_comment.*', 't_account.name', 't_account.blacklist as blackName', 'series.name as seriesName')
                    ->where('book_comment.uid', '=', $data)
                    ->orderBy('datetime', 'desc')
                    ->paginate(10)
                    ->setPath ('');
        return $data;
    }

    //取得userID
    public function getUserID($commentID)
    {
        $uid = DB::select('select uid from book_comment where id = :id', ['id' => $commentID]);
        return $uid[0]->uid;
    }

    //取得seriesID
    public function getSeriesID($commentID)
    {
        $series_id = DB::select('select series_id from book_comment where id = :id', ['id' => $commentID]);
        return $series_id[0]->series_id;
    }

    //判斷是否已經二次評論
    public function getCommentCount($userID, $seriesID, $commentID)
    {
        $commentCount = DB::select('select count(*) as count from book_comment where uid = :uid and series_id = :series_id and id != :commentID and isScore = 1 and status = 1',
                            ['uid' => $userID, 'series_id' => $seriesID, 'commentID' => $commentID]);
        return $commentCount[0]->count;
    }

    //更新狀態
    public function updateStatus($status, $isScore, $commentID)
    {
        DB::update('update book_comment set status = :status where id = :id', ['status' => $status, 'id' => $commentID]);
        DB::update('update book_comment set isScore = :isScore where id = :id', ['isScore' => $isScore, 'id' => $commentID]);
    }

    //判斷是否有消費這本書
    public function isBuy($userID, $seriesID)
    {
         $isBuy = DB::select('select count(*) as count from t_hist where uid = :uid and ((series_id = :series_id 
                                    and payment > 0 ) or (payment = 0 and order_dt < update_dt and return_dt = \'9999-12-31 23:59:59\'))',
                                    ['uid' => $userID, 'series_id' => $seriesID]);
        return $isBuy[0]->count;
    }

    //取得目前的讚數
    public function getPraise($commentID)
    {
        $praise = DB::select('select praise from book_comment where id = :id', ['id' => $commentID]);
        return $praise[0]->praise;
    }

    //加5個讚
    public function updatePraise($praise, $commentID)
    {
        DB::update('update book_comment set praise = :praise where id = :id', ['praise' => $praise + 5, 'id' => $commentID]);
    }

    //設成劇透
    public function updateSpoiler($commentID, $spoiler)
    {
        DB::update('update book_comment set spoiler = :spoiler where id = :id', ['spoiler' => $spoiler, 'id' => $commentID]);
    }

    //取得reportID
    public function updateReportID($reportID, $commentID)
    {
        DB::update('update book_comment set report_id = :report_id where id = :id', ['report_id' => $reportID, 'id' => $commentID]);
    }

    //取得狀態 0:審核中 1:通過 2:未通過 3:未消費 4: 二次評論
    public function getStatus($commentID)
    {
        $status = DB::select('select status from book_comment where id = :id', ['id' => $commentID]);
        return $status[0]->status;
    }

    //更新黑名單的狀態
    public function updateBlackStatus($blacklist, $userID)
    {
        DB::update('update db_user.t_account set blacklist = :blacklist where user_id = :user_id', ['blacklist' => $blacklist, 'user_id' => $userID]);
    }
}
