@extends('layouts.default')
@section('title', '首頁')
@section('content')
<style>
#setColor{
    color: red;
}
</style>
<div class="container-fluid">  
    <div class="row">
        <div class="col col-md-7">
            <ul>
                <li><h5>未審核的評論→小編檢舉→(1)該評論者倒扣10積分 (2)該評論設為不通過</h5></li>
                <li><h5>已經通過的評論→小編檢舉→(1)扣除該評論送出的積分 (2)該評論者倒扣10積分 (3)該評論設為不通過</h5></li>
                <li><h5>「不通過」、「未消費」、「二次評論」的評論→小編檢舉→(1)該評論者倒扣10積分 (2)該評論不做更動</h5></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col col-md-6">
            <h4>未審核:{{$total[0]->count}}筆</h4>
        </div>
        <div class="col col-md-2">
            <a href="{!! route('getBlackListComments') !!}"><button class="btn btn-info">取得黑名單列表</button></a>
        </div>
        <div class="col col-md-2">
            <a href="{!! route('getStatusComments') !!}"><button class="btn btn-success">取得未審核列表</button></a>
        </div>
        <div class="col col-md-2">
            <form action="/comment/search" method="GET" role="search">
                {{ csrf_field() }}
                <div class="input-group">
                    <input type="text" class="form-control" name="data"
                        placeholder="搜尋 userID"> <span class="input-group-btn">
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
            <table id="listTable" class="table table-striped table-bordered" width="100%">
                <thead>
                    <th><input type='checkbox' id='checkAll' /></th>
                    <th>commentID</th>
                    <th>日期</th>
                    <th>series_id</th>
                    <th>書名</th>
                    <th>UserID</th>
                    <th>黑名單 | 評論人</th>
                    <th>劇透 | 評論</th>
                    <th>動作</th>
                    <th>積分</th>
                    <th>狀態</th>
                    <th><i class="fa fa-thumbs-up"></i></th>
                    <th><i class="fa fa-thumbs-down"></i></th>
                    <th>評論比對</th>
                </thead>
                @inject('CommentListPresenter', 'App\Presenters\Comment\CommentListPresenter')
                @foreach($comments as $item)
                <tr>
                    <td id={{$item->id}}>
                        <input type="checkbox" name="chbox" {{$CommentListPresenter->check($item->status)}} value={{$item->id}}>
                    </td>
                    <td width='100px'>
                        {{$item->id}}
                    </td>
                    <td width='100px'>{{$item->datetime}}</td>
                    <td width='100px'>
                     <a onclick="window.open('http://tw.myrenta.com/item/{{$item->series_id}}', '_blank')">{{$item->series_id}}</a>
                    </td>
                    <td width='200px'>{{$item->seriesName}}</td>
                    <td>{{$item->uid}}</td>
                    <td>
                        @if ($item->blackName == 0)
                            <a href="{!! route('addBlack', ['userID'=>$item->uid]) !!}#{{$item->id}}" name={{$item->id}} data-toggle="tooltip" title="設為黑名單"><i class="fa fa-user-o"></i></a>
                        @else
                            <a id="setColor" href="{!! route('delBlack', ['userID'=>$item->uid]) !!}#{{$item->id}}" name={{$item->id}} data-toggle="tooltip" title="移除黑名單"><i class="fa fa-user"></i></a>
                        @endif
                        {{$item->name}}
                    </td>
                    <td width='450px'>
                        @if ($item->spoiler == 0)
                            <a href="{!! route('spoiler', ['commentID'=>$item->id]) !!}#{{$item->id}}" name={{$item->id}} data-toggle="tooltip" title="設為劇透"><i class="fa fa-square-o"></i></a>
                        @else
                            <a href="{!! route('undoSpoiler', ['commentID'=>$item->id]) !!}#{{$item->id}}" name={{$item->id}} data-toggle="tooltip" title="取消劇透"><i class="fa fa-square"></i></a>
                        @endif

                        <font color="#800000">
                            {{$item->comment}}
                        </font>
                    </td>
                     <td id = action_{{$item->id}}>
                        @if ($item->status == 1 || $item->status == 2 || $item->status == 3 || $item->status == 4)
                        <a href="{!! route('editStatus', ['commentID'=>$item->id , 'userID'=>$item->uid, 'status'=>$item->status]) !!}#{{$item->id}}" name={{$item->id}} data-toggle="tooltip" title="回到未審核"><i class="btn btn-info fa fa-undo"></i></a>
                        <a data-toggle="modal" data-target="#contact_dialog" data-id={{$item->id}}><i class="btn btn-warning fa fa-reply"  data-toggle="tooltip" title="回覆訊息"></i></a>
                        @else
                        <a href="{!! route('pass', ['commentID'=>$item->id]) !!}#{{$item->id}}" name={{$item->id}} data-toggle="tooltip" title="通過"><i class="btn btn-success fa fa-check"></i></a>
                        <a href="{!! route('fail', ['commentID'=>$item->id]) !!}#{{$item->id}}" name={{$item->id}} data-toggle="tooltip" title="不通過"><i class="btn btn-danger fa fa-times"></i></a>
                        <a data-toggle="modal" data-target="#contact_dialog" data-id={{$item->id}}><i class="btn btn-warning fa fa-reply" data-toggle="tooltip" title="回覆訊息"></i></a>
                        @endif

                        @if ($item->report_id == 0)
                            <a href="{!! route('addReport', ['commentID'=>$item->id, 'userID'=>$item->uid]) !!}#{{$item->id}}" name={{$item->id}} data-toggle="tooltip" title="小編檢舉"><i class="btn btn-default fa fa-minus-circle"></i></a>
                        @endif
                    </td>
                    <td id = score_{{$item->id}}>{{$CommentListPresenter->showScore($item->status, $item->isScore)}}</td>
                    <td id = status_{{$item->id}}>{{$CommentListPresenter->getStatus($item->status)}}</td>
                    <td>
                    {{$item->praise}}  
                    <a href="{!! route('addPraise', ['commentID'=>$item->id]) !!}#{{$item->id}}" name={{$item->id}} data-toggle="tooltip" title="加5個讚"><i class="fa fa-plus"></i></a>
                    </td>
                    <td>{{$item->dislike}}</td>
                    <td>
                        <a href="http://192.168.66.222/comment/s1.php?id={{$item->id}}&content={{$item->comment}}&book={{$item->seriesName}}" target="_blank">
                            <i class="btn btn-default fa fa-commenting-o"></i>
                        </a>
                    </td>
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
            {{$comments->render()}}
        </div>
    </div>

    <div class="row">
        <div class="col col-md-11">
            <button type="button" class="btn btn-success pull-right" id="checkPass">通過</button>
        </div>
        <div class="col col-md-1">
            <button type="button" class="btn btn-danger pull-left" id="checkFail">不通過</button>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script src="http://m.papy.com.tw:2221/js/comment/list.js"></script>
@stop