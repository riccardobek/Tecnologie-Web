$(document).ready(function() {
    $(".primary-btn.inline-btn").on('click', function () {


        //non so dove mettere AJAX
        $.ajax({
            url:"php/check_login.php",
            type:"POST",
            dataType : "json",
            success: function (response) {
                console.log(response);

                var checkLogin = response.logged;

                console.log("ckLogin");
                console.log(checkLogin);

                if(checkLogin){
                    $("body").css({ overflow: 'hidden' });
                    $("#overlay").fadeIn();
                    var a = $(this).siblings('h2').text();
                    $("#dialog-content > h2").text(a);

                    $("#overlay, #dialog-header > img ").on('click', function (event) {
                        $("#overlay").fadeOut();
                        $("body").css({ overflow: 'auto' })
                    });

                    $("#dialog-box").click(function (event) {
                        event.stopPropagation();
                    });
                }
                else{
                    alert("Non sei loggato!!!");
                }
            }
        });
    });
});

/*
* Funzione presa da:
* https://stackoverflow.com/questions/9062400/javascript-date-validation-for-mm-dd-yyyy-format-in-asp-net
 */
function validaData(d) {
    var match = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(d);
    if (!match) {
        // La data non è nel formato corretto
        return false;
    }

    var giorno   = parseInt(match[3], 10);
    var mese = parseInt(match[2], 10) - 1; // i mesi sono nell'intervallo 0-11, non 1-12
    var anno  = parseInt(match[3], 10);
    var date  = new Date(anno, mese, giorno);

    /* La funzione Date accetta qualsiasi parametro come anno, mese, giorno e lo converte
    * in una data valida. Quindi basta confrontare i valori del giorno, mese, anno in input
    * con quelli generati dall'oggetto date */
    return date.getDate() == day && date.getMonth() == month && date.getFullYear() == year;
}
