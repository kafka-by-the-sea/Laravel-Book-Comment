@extends('layouts.default')
@section('title', '首頁')
@section('content')

<div class="container-fluid">  
    <div class="row">
        <div class="col col-md-10">
            <h4>回覆列表</h4>
        </div>
        <div class="col col-md-2">
            <form action="/comment/replySearch" method="GET" role="search">
                {{ csrf_field() }}
                <div class="input-group">
                    <input type="text" class="form-control" name="data"
                        placeholder="commentID and userID"> <span class="input-group-btn">
                        <button type="submit" class="btn btn-default">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col col-md-12">
            <table id="replyTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>回覆日期</th>
                    <th>CommentID</th>
                    <th>原評論</th>
                    <th>UserID</th>
                    <th>回覆人</th>
                    <th>內容</th>
                    <th>動作</th>
                </thead>
                @inject('CommentReplyPresenter', 'App\Presenters\Comment\CommentReplyPresenter')
                @inject('CommentListPresenter', 'App\Presenters\Comment\CommentListPresenter')
                @foreach($replys as $item)
                <tr>
                    <td width='200px'>{{$CommentReplyPresenter->getDate($item->created)}}</td>
                    <td>{{$item->comment_id}}</td>
                    <td class='showComment' width='400px'>{{$item->comment}}</td>
                    <td>{{$item->user_id}}</td>
                    <td>{{$item->name}}</td>
                    <td width='200px'>{{$item->content}}</td>
                    <!--<td class='status'>{{$CommentReplyPresenter->getStatus($item->status)}}</td>-->
                    <td>
                    @if ($item->status == 1)
                        <a href="{!! route('updateStatus', ['replyID'=>$item->id , 'status'=>$item->status]) !!}"><i class="btn btn-default fa fa-eye" data-toggle="tooltip" title="是否顯示回覆"></i></a>
                    @else
                        <a href="{!! route('updateStatus', ['replyID'=>$item->id , 'status'=>$item->status]) !!}"><i class="btn btn-default fa fa-eye-slash" data-toggle="tooltip" title="是否顯示回覆"></i></a>
                    @endif
                        <a data-toggle="modal" data-target="#contact_dialog" data-id={{$item->comment_id}}><i class="btn btn-warning fa fa-reply" data-toggle="tooltip" title="回覆訊息"></i></a>
                    </td>
                    <!--
                    <td>
                        <a href="{!! route('delReply', ['replyID'=>$item->id]) !!}"><i class="btn btn-default fa fa-trash-o"></i></a>
                    </td>
                    -->
                </tr>
                @endforeach
            </table>
            <!-- the div that represents the modal dialog -->
            <div class="modal fade" id="contact_dialog" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">輸入回覆訊息</h4>
                        </div>
                        <div class="modal-body">
                            <form>
                                {{ csrf_field() }}
                                <textarea rows="4" cols="77" id = "message"></textarea>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                                    <button type="submit" class="btn btn-success" id = "submitButton">送出</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{$replys->render()}}
        </div>
    </div>
</div>
@stop

@section('scripts')
<script src="../js/comment/reply.js"></script>
@stop