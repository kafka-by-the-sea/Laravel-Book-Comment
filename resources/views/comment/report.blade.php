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
                <li><h5>檢舉通過→檢舉原因:「有劇透」以外的原因→(1)檢舉人加5積分 (2) 扣除該評論送出的積分 (3)該評論者倒扣10積分 (4)該評論設為不通過</h5></li>
                <li><h5>檢舉通過→檢舉原因:「有劇透」→(1)檢舉人加1積分 (2)該評論設為有劇透</h5></li>
                <li><h5>檢舉通過但不扣分→(1)檢舉人加1積分 (2)該評論設為不通過</h5></li>
            </ul>
        </div>
    </div> 
    <div class="row">
        <div class="col col-md-12">
            <h4>檢舉未處理者:{{$total}}筆</h4>
        </div>
    </div>

    <div class="row">
        <div class="col col-md-12">
            <table id="listTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>檢舉ID</th>
                    <th>書名</th>
                    <th>查看series評論</th>
                    <th>檢舉人UserID</th>
                    <th>檢舉人</th>
                    <th>評論人的UID</th>
                    <th>CommentID</th>
                    <th>被檢舉的評論</th>
                    <th>檢舉理由</th>
                    <th>狀態</th>
                    <th>動作</th>
                    <th>評論比對</th>
                </thead>
                @inject('CommentReportPresenter', 'App\Presenters\Comment\CommentReportPresenter')
                @foreach($reports as $item)
                <tr>
                    <td width='100px'>{{$item->id}}</td>
                    <td width='300px'>{{$item->book}}</td>
                    <td> <a onclick="window.open('http://tw.myrenta.com/item/{{$item->series_id}}', '_blank')">{{$item->series_id}}</a></td>
                    <td>{{$item->user_id}}</td>
                    <td>{{$item->name}}</td>
                    <td>
                    {{$item->uid}}
                    </td>
                    <td>{{$item->comment_id}}</td>
                    <td width='400px'>{{$item->comment}}</td>
                    <td width='200px'>{{$item->reason}}</td>
                    <td>{{$CommentReportPresenter->getStatus($item->status)}}</td>
                    <td>
                        @if ($item->status == 0)
                            <a href="{!! route('reportSuccess', ['reportID'=>$item->id,'commentID'=>$item->comment_id, 'userID'=>$item->user_id]) !!}"><i class="btn btn-success fa fa-check" data-toggle="tooltip" title="檢舉通過"></i></i></a>
                            <a href="{!! route('reportFail', ['id'=>$item->id]) !!}"><i class="btn btn-danger fa fa-times" data-toggle="tooltip" title="檢舉不通過"></i></a>
                            <a href="{!! route('reportPass', ['reportID'=>$item->id,'commentID'=>$item->comment_id, 'userID'=>$item->user_id]) !!}"><i class="btn btn-default fa fa-check-circle" data-toggle="tooltip" title="檢舉通過但不扣分"></i></a>
                        @endif
                    </td>
                    <td>
                        <a onclick="window.open('http://192.168.66.222/comment/s1.php?id={{$item->comment_id}}&content={{$item->comment}}&book={{$item->book}}', '_blank')"  data-toggle="tooltip" title="評論比對"><i class="btn btn-default fa fa-commenting-o"></i></a>    
                    </td>
                </tr>
                @endforeach
            </table>
            {{$reports->render()}}
        </div>
    </div>
</div>
@stop

@section('scripts')
<script src="../js/comment/report.js"></script>
@stop