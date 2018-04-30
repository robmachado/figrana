function ValidaParcelamento(oSrc, args)
{
	return (parcelamentoValido(TiraMascara2(args)))
}
function parcelamentoValido(numero)
{
	//completa com nove dígitos	
	numero = (("0000000000000000000").substring(0,9-numero.length) + numero);

	var soma  = numero.charAt(0)*8;
	soma += numero.charAt(1)*7;
	soma += numero.charAt(2)*6;
	soma += numero.charAt(3)*5;
	soma += numero.charAt(4)*4;
	soma += numero.charAt(5)*3;
	soma += numero.charAt(6)*2;
	soma += numero.charAt(7)*10;

	var digito = 11 - (soma % 11);
	if (digito == 10)digito = 0;
	if (digito == 11) return false;
	return (parseInt(numero.charAt(8)) == digito)
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
