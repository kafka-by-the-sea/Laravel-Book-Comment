<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class CommentReportRepository
{
    //取得檢舉列表
    public function getReports()
    {
        $reports = DB::table('db_user.t_account')
            ->Join('book_comment_report', 't_account.user_id', '=', 'book_comment_report.user_id')
            ->Join('book_comment', 'book_comment_report.comment_id', '=', 'book_comment.id')
            ->leftJoin('db_cms.series', 'series.id', '=', 'book_comment.series_id')
            ->select('book_comment_report.*', 'book_comment.comment', 't_account.name', 'book_comment.series_id', 'book_comment.uid', 'series.name as book')
            //->where('t_account.user_id', '>', '0')
            //->orWhere('t_account.user_id', '=', '0')
            ->orderBy('id', 'desc')
            ->paginate(50);
        
        return $reports;
    }

    // 取得檢舉未處理的筆數
    public function getStatasNumber()
    {
        $total = DB::table('book_comment_report')->where('status', '=', '0')->count();
        return $total;
    }

    //新增檢舉紀錄
    public function insertReport($userID, $commentID, $reason, $status)
    {
         DB::insert('insert into book_comment_report (user_id, comment_id, reason, status) value (:user_id, :comment_id, :reason, :status)',
            ['user_id' => $userID, 'comment_id' => $commentID, 'reason' => $reason, 'status' => $status]);
    }

    //取得檢舉ID
    public function getReportID($commentID)
    {
        $reportID = DB::select('select id from book_comment_report where comment_id = :comment_id', ['comment_id' => $commentID]);
        return $reportID[0]->id;
    }

    //設定檢舉狀態 0:未處理 1:通過 2:不成立
    public function setStatus($status, $reportID)
    {
        DB::update('update book_comment_report set status = :status where id = :id', ['status' => $status, 'id' => $reportID]);
    }

    //取得檢舉理由
    public function getReason($userID, $commentID)
    {
        $reason = DB::select('select reason from book_comment_report where user_id = :user_id and comment_id = :comment_id', ['user_id' => $userID, 'comment_id' => $commentID]);
        return $reason[0]->reason;
    }
}
