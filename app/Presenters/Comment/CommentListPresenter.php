<?php
namespace App\Presenters\Comment;

class CommentListPresenter
{
    public static function check($status)
    {
        if ($status == '1' || $status=='2' || $status=='3' || $status=='4') {
            echo "Disabled";
        } else {
            echo "checked = 'checked'";
        }
    }

    public static function getStatus($status)
    {
        switch ($status) {
            case 0:
                echo "未審核";
                break;
            case 1:
                echo "通過";
                break;
            case 2:
                echo "不通過";
                break;
            case 3:
                echo "未消費";
                break;
            case 4:
                echo "二次評論";
                break;
        }
    }

    public static function showScore($status, $isScore)
    {
        if ($status == 0) {
            echo "未審核";
        } elseif ($status == 1 || $status == 2) {
            switch ($isScore) {
                case 0:
                    echo "<span style='color:#0000FF;'>沒送</span>";
                    break;
                case 1:
                    echo "<span style='color:#FF0000;'>已送</span>";
                    break;
                case 2:
                    echo "<span style='color:#BB5E00;'>扣分</span>";
                    break;
                case 3:
                    echo "<span style='color:#BB5E00;'>檢舉但不扣分</span>";
                    break;
            }
        }
    }
}
