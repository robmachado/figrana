// valida o Código de Cnae da Gare
// usar no Custom DomValidator, conforme exemplo abaixo
// <cc1:CustomDomValidator id="CustomDomValidator1" runat="server" Width="450px" CssClass="validacao" Display="Dynamic" ControlToValidate="Cnae" ErrorMessage="Dígito de controle do Código de Cnae incorreto" ClientValidationFunction="ValidaCnae"></cc1:CustomDomValidator>

function ValidaCnae(oSrc, args)
{
	return (CnaeValido(TiraMascara2(args)))
}

function CnaeValido(numero)
{
	var soma = 0;
	var digito;
	var resto;

	//completa com zeros a esquerda
	numero = ("0000000000000000000").substring(0,5-numero.length) + numero;
	soma += numero.charAt(0)*5;
	soma += numero.charAt(1)*4;
	soma += numero.charAt(2)*3;
	soma += numero.charAt(3)*2;


	digito = 11 - (soma % 11);

	if (digito < 9) digito = digito + 1;
	else 
	    if (digito == 9) digito = 0
	    else digito = 1
	    
	//resto = soma % 11;
	//if (resto == 0 | resto ==1) digito = 0;
	//else digito = 11 - resto
	
	if (parseInt(numero.charAt(4)) == digito)
		return(true);
	else
		return(false); 
}

function ValidaCnae5ou7(oSrc, args)
{
	if (args.length == 5) return (CnaeValido(TiraMascara2(args)))
	else return (args.length ==7)
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
