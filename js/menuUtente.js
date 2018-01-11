$(document).ready(function(){
    $(".tabcontent").first().show();
    $(".tablinks").first().addClass("active");
    $(".tablinks").on("click",function (e) {
       var tabTarget = $(this).attr("data-target");
       $(".tablinks").removeClass("active");
        $(this).addClass("active");
       $(".tabcontent").hide();
       $('#'+tabTarget).show();
    });
});