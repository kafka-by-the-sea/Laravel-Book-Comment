<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'comment'], function () {
    //評論列表
    Route::GET('/index', 'CommentListController@index');
    //取得未審核列表
    Route::GET('/getStatusComments', 'CommentListController@getStatusComments')->name('getStatusComments');
    //取得黑名單列表
    Route::GET('/getBlackListComments', 'CommentListController@getBlackListComments')->name('getBlackListComments');
    //搜尋列表
    Route::GET('/search', 'CommentListController@search');
    //一次通過或不通過
    Route::GET('/checkPass', 'CommentListController@checkPass');
    Route::GET('/checkFail', 'CommentListController@checkFail');
    //單筆通過或不通過
    Route::GET('/pass/{commentID}', 'CommentListController@pass')->name('pass');
    Route::GET('/fail/{commentID}', 'CommentListController@fail')->name('fail');
    //加入或取消黑名單
    Route::GET('/addBlackList/{userID}', 'CommentListController@addBlackList')->name('addBlack');
    Route::GET('/delBlackList/{userID}', 'CommentListController@delBlackList')->name('delBlack');
    //回到審核中
    Route::GET('/editStatus/{commentID}/{userID}/{status}', 'CommentListController@editStatus')->name('editStatus');
    //送出回覆訊息
    Route::GET('/replyMessage', 'CommentListController@replyMessage')->name('replyMessage');
    //直接加讚數量
    Route::GET('/addPraise/{commentID}', 'CommentListController@addPraise')->name('addPraise');
    //有劇透
    Route::GET('/spoiler/{commentID}', 'CommentListController@spoiler')->name('spoiler');

    //取消劇透
    Route::GET('/undoSpoiler/{commentID}', 'CommentListController@undoSpoiler')->name('undoSpoiler');

    //檢舉列表
    Route::GET('/report', 'CommentReportController@index');
    Route::GET('/addReport/{commentID}/{userID}', 'CommentReportController@addReport')->name('addReport');
    Route::GET('/reportPass/{reportID}/{commentID}/{userID}', 'CommentReportController@reportPass')->name('reportPass');
    Route::GET('/reportFail/{reportID}', 'CommentReportController@reportFail')->name('reportFail');
    Route::GET('/reportSuccess/{reportID}/{commentID}/{userID}', 'CommentReportController@reportSuccess')->name('reportSuccess');
     Route::GET('/addBlackListReport/{userID}', 'CommentReportController@addBlackList')->name('addBlackReport');
    Route::GET('/delBlackListReport/{userID}', 'CommentReportController@delBlackList')->name('delBlackReport');

    //回覆列表
    Route::GET('/reply', 'CommentReplyController@index');
    //是否顯示亂民的回覆訊息
    Route::GET('/reply/updateStatus/{replyID}/{status}', 'CommentReplyController@updateStatus')->name('updateStatus');
    //刪除亂民的回覆訊息
    Route::GET('/reply/deleteReply/{replyID}', 'CommentReplyController@deleteReply')->name('delReply');
    //搜尋回覆訊息
    Route::GET('/replySearch', 'CommentReplyController@search');
});
