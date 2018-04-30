function fAceita(e,lista,criterio)
{
	keyPressed = e.which ? e.which : e.keyCode;
	// verifica se é um dos caracteresde controle delete, backspace,seta direita,enter etc
	if ((",8,9,13,16,37,39,").indexOf("," + keyPressed + ",")>-1) return;
	
	//verifica se o critério (true ou false) bate com a existencia do caracter desejado
	if (((","+lista+",").indexOf("," + keyPressed + ",")>-1) !=criterio) 
		if (e.preventDefault) 
			{ //Firefox - cancela a escrita do caracter no campo.
				e.preventDefault();
				e.stopPropagation();
			} 
		else e.returnValue = false; //IE - cancela a escrita do caracter no campo. 
}


//Permite a entrada de dados numéricos,usar no onkeypress=travaCaracter().
function travaCaracter(e)
{	
	fAceita(e,"48,49,50,51,52,53,54,55,56,57",true);
}

function travaEnter(e)
{
	if (e.keyCode == 13)
	{
		e.keyCode = 9;
	}
	e.returnValue = true;
}

// mostra o caracter digitado,usado só para o desenvolvimento
function mostraCaracter()
{
	alert(event.keyCode);
}
