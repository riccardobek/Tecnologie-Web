$("#creaMacro").on("click", function(){
	$("#creaMacro").show();
});

$(".btn").on("click", function(){
	$("#creaMacro").hide();
});

$("#creaMacroattivita input[type=submit]").on("click", function(){
	$.post(php/macroattivita.php, $(".form").serialize(),function(risposta){
		risposta = JSON.parse(risposta);
		if(risposta.stato == 1) {
			generaAlert('green',"Successo",risposta.messaggio);
		}
		else {
			generaAlert('red',"Errore",risposta.messaggio);
		}
	});
});