$("document").ready(function() {
    $("#footer > ul > li").on("click",function(event) {
        var url = $(event.target).children("a").attr("href");
        if(url != undefined && url != null && location.href != url)
            location.href = url;
    });
});