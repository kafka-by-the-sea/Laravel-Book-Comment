<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class CommentPraiseRepository
{

    //新增加讚紀錄
    public function insertPraise($userID, $commentID, $t)
    {
        DB::insert('insert into book_comment_praise (user_id, comment_id, created) value (:user_id, :comment_id, :created)',
                ['user_id' => $userID, 'comment_id' => $commentID, 'created' => $t]);
    }
}
