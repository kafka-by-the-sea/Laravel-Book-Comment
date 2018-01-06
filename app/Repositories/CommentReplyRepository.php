<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class CommentReplyRepository
{
    //取得回覆列表
    public function getReplys()
    {
        $replys = DB::table('db_user.t_account')
            ->join('book_comment_reply', 't_account.user_id', '=', 'book_comment_reply.user_id')
            ->join('book_comment', 'book_comment_reply.comment_id', '=', 'book_comment.id')
            ->select('book_comment_reply.*', 'book_comment.comment', 't_account.name')
            //->where('t_account.user_id', '>', '1000')
            //->orWhere('t_account.user_id', '=', '0')
            ->orderBy('book_comment_reply.comment_id', 'desc')
            ->paginate(20);
        return $replys;
    }

    //搜尋結果
    public function search($data)
    {
        $data = DB::table('db_user.t_account')
            ->join('book_comment_reply', 't_account.user_id', '=', 'book_comment_reply.user_id')
            ->join('book_comment', 'book_comment_reply.comment_id', '=', 'book_comment.id')
            ->select('book_comment_reply.*', 'book_comment.comment', 't_account.name')
            ->where('book_comment_reply.comment_id', '=', $data)
            ->orWhere('book_comment_reply.user_id', '=', $data)
            ->orderBy('book_comment_reply.comment_id', 'desc')
            ->paginate(20)
            ->setPath ('');
        return $data;
    }

    //回覆的訊息是否顯示
    public function updateStatus($replyID, $status)
    {
        if ($status == 1) {
            DB::table('book_comment_reply')->where('id', $replyID)->update(['status' => '2']);
        } elseif ($status == 2) {
            DB::table('book_comment_reply')->where('id', $replyID)->update(['status' => '1']);
        }
    }

    //刪除回覆訊息
    public function deleteReply($replyID)
    {
        DB::table('book_comment_reply')->where('id', $replyID)->delete();
    }

    //新增小編的回覆訊息
    public function insertMessage($commentID, $message)
    {
        $t = time();
        DB::insert('insert into book_comment_reply (comment_id, content, created, user_id, status) value (:comment_id, :content, :created, :user_id, :status)',
        ['comment_id' => $commentID, 'content' => $message, 'created' => $t, 'user_id' => '0', 'status' => '1']);
    }
}
