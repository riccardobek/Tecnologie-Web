$(document).ready(function() {
    $(".primary-btn.inline-btn").on('click', function () {
        $("body").css({ overflow: 'hidden' })
        $("#overlay").fadeIn();
    });

    $("#overlay, #dialog-header > img ").on('click', function (event) {

        $("#overlay").fadeOut();
        $("body").css({ overflow: 'auto' })
    });

    $("#dialog-box").click(function (event) {
        event.stopPropagation();

    })
});
