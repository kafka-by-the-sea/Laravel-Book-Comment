<?php
namespace App\Presenters\Comment;

class CommentReportPresenter
{
    public static function getStatus($status)
    {
        switch ($status) {
            case 0:
                echo "未處理";
                break;
            case 1:
                echo "通過";
                break;
            case 2:
                echo "不成立";
                break;
        }
    }
}
