$(function() {
    $(".tabcontent").first().show();
    $(".tablinks").first().addClass("active");
    $(".tablinks").on("click", function (e) {
        var tabTarget = $(this).attr("data-target");
        $(".tablinks").removeClass("active");
        $(this).addClass("active");
        $(".tabcontent").hide();
        $('#' + tabTarget).show();
    });
    toggleMostra();
});

function toggleMostra() {
    $(".rate-button").click(function(){
        $(this).hide();
        $(this).next(".submit-rate").show();
    });
    $(".cancel").click(function(){
        $(this).parent().hide();
        $(this).parent().prev().show();
    });
}
