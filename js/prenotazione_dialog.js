$(document).ready(function() {
    $(".primary-btn.inline-btn").on('click', function () {
        $("body").css({ overflow: 'hidden' })
        $("#overlay").fadeIn();
        var a = $(this).siblings('h2').text();
        $("#dialog-content > h2").text(a);
    });

    $("#overlay, #dialog-header > img ").on('click', function (event) {

        $("#overlay").fadeOut();
        $("body").css({ overflow: 'auto' })
    });

    $("#dialog-box").click(function (event) {
        event.stopPropagation();

    })


});
