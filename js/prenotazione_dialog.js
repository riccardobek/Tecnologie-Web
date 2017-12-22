$(document).ready(function() {
    $(".primary-btn.inline-btn").click(function () {
        $("#overlay").show();
    });

    $("#overlay").click(function () {
        $("#overlay").hide();
    });
});
