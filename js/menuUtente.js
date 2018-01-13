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


    //Scheda Prenotazioni
    stileCellaPagamento();
    //richiesta AJAX per la cancellazione di una prenotazione
    $(".btn-cancella").on("click", function () {
        var target = $(this).attr("data-target");
        var data = validaData($('#'+target).find(".giorno").text());
        var timeDiff = data - (new Date());
        var giorniDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        if(giorniDiff<2) {
            alert("NON puoi cancellare");
        }
        else {
           $.post("php/delete_prenotazione.php",{
               idPrenotazione: target
           },function (risposta) {
               risposta = JSON.parse(risposta);
               alert(risposta.messaggio);
               if(risposta.stato == 1) {
                   //successo
                   var pari = $('#'+target).parent().nextAll(".pari");
                   var dispari = $('#'+target).parent().nextAll(".dispari");

                   $('#'+target).parent().slideUp('Slow', function(){
                       $(this).remove();
                   });

                   dispari.removeClass("dispari").addClass("pari");
                   pari.removeClass("pari").addClass("dispari");
               }
               else {

               }
           });
        }


    });

    //Scheda Account
    $datiForm = salvaDatiForm();

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

function salvaDatiForm(){
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

