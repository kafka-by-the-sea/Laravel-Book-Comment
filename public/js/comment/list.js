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
        }).done(function () {
            swal("更新成功!");
            setTimeout(function () { location.reload(); }, 800);
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
        });
    });
});