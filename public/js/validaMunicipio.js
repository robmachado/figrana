function ValidaMunicipio(oSrc, args)
{
	return (MunicipioValido(TiraMascara2(args)))
}
function MunicipioValido(numero)
{
	// Se tiver mais ou menos que 12 dígitos, devolve o erro.
	if (numero.length != 4)return (false);

	var soma  = numero.charAt(0)*4;
	soma += numero.charAt(1)*3;
	soma += numero.charAt(2)*2;

	var digito = (soma % 11);
	if (digito == 10)digito = 0;
	return (parseInt(numero.charAt(3)) == digito)
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
