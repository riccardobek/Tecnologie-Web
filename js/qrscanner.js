$(document).ready(function() {
    //Convalida prenotazione
    $("#scheda-QR").on("click", function () {
        $("#lettoreQR").show();
        scanner = new Instascan.Scanner({
            video: $('#videoQR')[0],
            backgroundScan: false
        });
        scanner.addListener('scan', function (content) {
            if (content.match("^pr-")) {
                $("#testoQR").val(content);
                //Visto che ho scansionato un QR apparentemente valido, provo subito la convalida senza che l'utente debba
                //cliccare il tasto "conferma"
                convalidaPrenotazione($("#testoQR").val());
            }
            else
                generaAlert('red', "Errore", "Codice QR inquadrato non inerente ad una prenotazione.");
        });
        try {
            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]).catch(function (e) {
                        generaAlert('red', "Errore", "La lettura dei codici QR via HTTP è supportata solo da Firefox o Edge. Assicurati di avere un browser compatibile o usa HTTPS per abilitare il supporto anche su Chrome.");
                    });
                } else {
                    generaAlert('red', "Errore", "Nessuna fotocamera trovata. La lettura dei codici QR verrà quindi disabilitata.");
                    //Mostro errore nessuna camera trovata
                }
            }).catch(function (e) {
                generaAlert('red', "Errore", "Errore nell'inizializzazione del lettore QR. La lettura dei codici QR non sarà quindi possibile.");
            });
        }
        catch (e) {
            generaAlert('red', "Errore", "Errore nell'inizializzazione del lettore QR. La lettura dei codici QR non sarà quindi possibile.");
        }
        bloccaScroll();
    });
});
