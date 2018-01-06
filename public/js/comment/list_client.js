$(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $("#contact_dialog").on("show.bs.modal", function (e) {
        var obj = $(e.relatedTarget), serviceUrl;
        var id = obj.data("id");
        $('#submitButton').click(function () {
            commentID = id;
            message = $('#message').val();
            if (message == "") {
                alert("請輸入內容");
                return false;
            } else {
                //alert(commentID);
                //alert(message);
                var url = "/comment/replyMessage/";
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: { commentID: commentID, message: message }
                }).done(function () {
                    console.log('送出');
                });
            }
        });
    });

    $('#checkAll').click(function (obj, chbox) {
        var list = document.getElementsByName('chbox');
        for (var i = 0; i < list.length; i++) {
            if (list[i].disabled) {
                continue;
            }
            var e = list[i];
            e.checked = !e.checked;
        }
    });

    var actionHTML1 = '<a href="http://192.168.66.155:8000/comment/editStatus/cid/uid/status#cid" name=cid data-toggle="tooltip" title="回到未審核"><i class="btn btn-info fa fa-undo"></i></a> <a data-toggle="modal" data-target="#contact_dialog" data-id=cid><i class="btn btn-warning fa fa-reply" data-toggle="tooltip" title="回覆訊息"></i></a>';
    var actionHTML2 = ' <a href="http://192.168.66.155:8000/comment/addReport/cid/uid#cid" name=cid data-toggle="tooltip" title="小編檢舉"><i class="btn btn-default fa fa-minus-circle"></i></a>';
    String.prototype.replaceAll = function (search, replacement) {
        var target = this;
        return target.split(search).join(replacement);
    };

    $('#checkPass').click(function () {
        var idstr = $('input[name="chbox"]:checked').map(function () {
            return $(this).val();
        }).get().join(",");
        //console.log(idstr);

        var url = "/comment/checkPass/";
        $.ajax({
            type: 'GET',
            url: url,
            data: { idstr: idstr }
        }).done(function (data) {
            var obj = jQuery.parseJSON(data);
            var length = obj.length;
            obj.reverse();
            for (var i = 0; i < length; i++) {
                var cid = obj[i]["id"];
                var uid = obj[i]["uid"];
                var status = obj[i]["status"];
                var isScore = obj[i]["isScore"];
                var report_id = obj[i]["report_id"];

                var checkboxHTML = '<input type="checkbox" name="chbox" disabled="" value=cid>';
                var checkboxHTML = checkboxHTML.replaceAll('cid', cid);
                $('#' + cid).html(checkboxHTML);

                //動作
                var appendHTML1 = actionHTML1.replaceAll('cid', cid);
                var appendHTML1 = appendHTML1.replaceAll('uid', uid);
                var appendHTML1 = appendHTML1.replaceAll('status', status);

                if (report_id == 0) {
                    var appendHTML2 = actionHTML2.replaceAll('cid', cid);
                    var appendHTML2 = appendHTML2.replaceAll('uid', uid);
                    $('#action_' + cid).html(appendHTML1 + appendHTML2);
                } else {
                    $('#action_' + cid).html(appendHTML1);
                }

                //積分
                if (isScore == 1) {
                    $('#score_' + cid).html('<span style="color:#FF0000;">已送</span>');
                } else if (isScore == 0) {
                    $('#score_' + cid).html('');
                }

                //狀態
                if (status == 1) {
                    $('#status_' + cid).html('通過');
                } else if (status == 3) {
                    $('#status_' + cid).html('未消費');
                } else if (status == 4) {
                    $('#status_' + cid).html('二次評論');
                }
            }
            swal("更新成功!");
        });

    });

    $('#checkFail').click(function () {
        var idstr = $('input[name="chbox"]:checked').map(function () {
            return $(this).val();
        }).get().join(",");
        //console.log(idstr);

        var url = "/comment/checkFail/";
        $.ajax({
            type: 'GET',
            url: url,
            data: { idstr: idstr }
        }).done(function () {
            swal("更新成功!");
            setTimeout(function () { location.reload(); }, 800);
            //location.reload();
        });
    });
});