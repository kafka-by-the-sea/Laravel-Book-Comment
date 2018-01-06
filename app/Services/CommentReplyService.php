<?php
namespace App\Services;

use App\Repositories\CommentReplyRepository;

class CommentReplyService
{
    protected $commentreplyRepository;
  
    public function __construct(CommentReplyRepository $commentreplyRepository)
    {
        $this->commentreplyRepository = $commentreplyRepository;
    }

    //取得回覆列表
    public function getReplyList()
    {
        return $this->commentreplyRepository->getReplys();
    }

    //回覆訊息是否顯示
    public function updateStatus($replyID, $status)
    {
        $this->commentreplyRepository->updateStatus($replyID, $status);
    }

    //刪除回覆訊息
    public function deleteReply($replyID)
    {
        $this->commentreplyRepository->deleteReply($replyID);
    }

    //搜尋
    public function search($data)
    {
        return $this->commentreplyRepository->search($data);
    }
}
