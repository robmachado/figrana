// valida data deverá ser usado no custom DomValidator
// abaixo exemplo de utilização deste validador
//<cc1:CustomDomValidator id="DataDesocupacao" runat="server" CssClass="validacao" Display="Dynamic" ControlToValidate="DtDesocupacao" ErrorMessage="Data Inválida. dd/mm/aaaa" ClientValidationFunction="validaData"></cc1:CustomDomValidator>
function validaData(oSrc, args)
{	
	var valor = TiraMascara2(args);
	// completa com zeros a esquerda
	valor = ("0000000000000000000").substring(0,8-valor.length) + valor;

	day = valor.substring(0,2);
	month = valor.substring(2,4);
	year = valor.substring(4,8);
	isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
	return !((month < 1 || month > 12)||(day < 1 || day > 31)||((month==4 || month==6 || month==9 || month==11) && day==31)||(month == 2 && (day>29 || (day==29 && !isleap))));
}

function TiraMascara2(valor)
{
	var digitos = "0123456789_";
	resultado = "";
	deslocamento = 0;
	for(var i=0; i<valor.length; i++)
		if (digitos.indexOf(valor.charAt(i)) >= 0) 
			resultado += valor.charAt(i);
	return resultado;
}
