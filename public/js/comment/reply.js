$(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $("#contact_dialog").on("show.bs.modal", function (e) {
        var obj = $(e.relatedTarget), serviceUrl;
        var id = obj.data("id");
        $('#submitButton').click(function () {
            commentID = id;
            message = $('#message').val();
            if (message=="")
            {
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
});