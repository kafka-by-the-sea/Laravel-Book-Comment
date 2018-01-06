<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UserScoreRepository
{
    //取得目前積分
    public function getScore($userID)
    {
        $score = DB::select('select score from user_score where user_id = :user_id order by id desc, created desc limit 1', ['user_id' => $userID]);
        if (!empty($score)) {
            return $score[0]->score;
        } else {
            return 0;
        }
    }

    //取得該評論當時送的積分
    public function getmvScore($commentID)
    {
        return DB::select('select mv_score from user_score where memo = :memo', ['memo' => $commentID]);
    }

    //新增加減分紀錄
    public function insertScore($userID, $action, $addScore, $totalScore, $commentID, $memo)
    {
        $t = time();
        DB::insert('insert into user_score (user_id, action, mv_score, score, memo, created) value (:user_id, :action, :mv_score, :score, :memo, :created)',
                ['user_id' => $userID, 'action' => $action, 'mv_score' => $addScore, 'score' => $totalScore ,'memo' => $commentID . $memo, 'created' => $t]);
    }

    //取得原評論者的userID(檢舉時用commentID反查)
    public function getCommentUserID($commentID)
    {
        $userID = DB::select('select user_id from user_score where memo = :memo', ['memo' => $commentID]);
        return $userID[0]->user_id;
    }
}
