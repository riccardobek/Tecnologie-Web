$(document).ready(function() {
    $(".primary-btn.inline-btn").on('click', function () {
        $("#overlay").show();
    });

    $("#overlay, #dialog-header > img ").on('click', function (event) {

        $("#overlay").hide();
    });

    $("#dialog-box").click(function (event) {
        event.stopPropagation();

    })
});
