$(document).ready(function() {
    $("form").on("submit",function(event) {
        event.preventDefault();
        event.stopPropagation();

        $("div.alert").hide();

        $.post("php/do_login.php",$("form").serialize(),function(risposta){
            if(risposta == "1") {
                /**
                * In questo campo di testo stampo tramite PHP il valore della variabile globale $_SERVER{"HTTP_REFERRER"]
                * Per ulteriori informazioni vedere commento sotto.
                 */
                var redirectURL = $("#HTTP_REFERER").val();

                /**
                 * In pratica, l'HTTP_REFERRER è un parametro che indica la pagina visitata prima della corrente
                 * (ovvero la provenienza della navigazione). Cioè, se io sono su attivita.php e poi apro la pagina di
                 * login, in login.php $_SERVER["HTTP_REFERRER"] sarà "attivita.php". Questo è utile per fare il redirect
                 * una volta che si è effettutato il login (l'idea è quella di tornare alla pagina che si stava visitando
                 * prima). Il problema è che, se sono su login.php e ricarico la pagina, $_SERVER["HTTP_REFERRER"] sarà
                 * impostato a "login.php" (perché la pagina in cui ero prima è sempre login). Ecco che, se non faccio
                 * dei controlli, rischio di incorrere in un loop di reindirizzamento.
                 */
                if(window.location == redirectURL) {
                    /**
                     * Se sono in login e ho aggiornato la pagina (perdendo così l'HTTP_REFERRER) torno alla home
                     * per evitare il loop di reindirizzamento.
                     */
                    redirectURL = "index.php";
                    $("div.alert.successo > a").attr("href",redirectURL);
                }

                $("div.alert.successo").show();
                setTimeout(function() {
                    location.href = redirectURL;
                },1500);
            }
            else {
                $("div.alert.errore").show();
            }
        });
    });
});