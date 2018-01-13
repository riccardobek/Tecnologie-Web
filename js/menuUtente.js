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

    stileCellaPagamento();
    $datiForm = salvaDatiInizialiForm();


    $(":text").attr('disabled','disabled');

    $(".mostra-modifica").hide();
    $("#modifica").on("click",function () {
        $(".mostra-modifica").slideDown(200);
        $(":text").not('#username').removeAttr('disabled');

    });
    $("#annulla").on("click",function () {
        $(":text").attr('disabled','disabled');
        ripristinaDatiInizialiForm($datiForm);
        $(".mostra-modifica").slideUp(200, function () {
            $(this).hide();
        });
    });

});

function salvaDatiInizialiForm(){
    var inputs = $('input').not(':input[type=submit]');
     var datiForm = {};
    $(inputs).each(function () {
        datiForm[$(this).attr("id")] = $(this).val();
    });
    return datiForm;
}

function ripristinaDatiInizialiForm(oggettoDatiForm){
    var inputs = $('input').not(':input[type=submit]');
    $(inputs).each(function () {
        $(this).val(oggettoDatiForm[$(this).attr("id")]);
    });
}

function stileCellaPagamento(){
    $(".pagamento").each(function () {
        if( $(this).text() === "Non pagato")
            $(this).css("color","#B80000");
        else
            $(this).css("color","#34ba49");
  });
}