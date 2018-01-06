<?php
namespace App\Presenters\Comment;

class CommentReplyPresenter
{
    public static function getStatus($status)
    {
        switch ($status) {
            case 1:
                echo "顯示";
                break;
            case 2:
                echo "不顯示";
                break;
        }
    }
    public static function getDate($unixtime)
    {
        return date('Y-m-d H:i:s', $unixtime);
    }
}
