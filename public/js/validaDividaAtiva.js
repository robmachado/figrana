function ValidaDividaAtiva(oSrc, args)
{
	valor = TiraMascara2(args);
	if (valor.length <= 9)valor = ("0000000000000000000").substring(0,9-valor.length) + valor;
	else valor = ("0000000000000000000").substring(0,13-valor.length) + valor;

	if (valor.length == 9)return (DividaAtivaValido(valor));
	else return (EtiquetaValido(valor));
}

function DividaAtivaValido(numero)
{
	// Se tiver mais ou menos que 12 dígitos, devolve o erro.
	if (numero.length != 9)return (false);

	var soma  = numero.charAt(0)*1;
	soma += numero.charAt(1)*3;
	soma += numero.charAt(2)*4;
	soma += numero.charAt(3)*5;
	soma += numero.charAt(4)*6;
	soma += numero.charAt(5)*7;
	soma += numero.charAt(6)*8;
	soma += numero.charAt(7)*10;

	var digito = soma % 11;
	if (digito == 10)digito = 0;
	return (parseInt(numero.charAt(8)) == digito)
}

function EtiquetaValido(numero)
{
	// Se tiver mais ou menos que 12 dígitos, devolve o erro.
	if (numero.length != 13)return (false);

	var soma  = numero.charAt(0)*1;
	soma += numero.charAt(1)*2;
	soma += numero.charAt(2)*3;
	soma += numero.charAt(3)*4;
	soma += numero.charAt(4)*5;
	soma += numero.charAt(5)*6;
	soma += numero.charAt(6)*7;
	soma += numero.charAt(7)*8;
	soma += numero.charAt(8)*9;
	soma += numero.charAt(9)*10;
	soma += numero.charAt(10)*11;
	soma += numero.charAt(11)*12;

	var digito = soma % 11;
	if (digito == 10)digito = 0;
	return (parseInt(numero.charAt(12)) == digito)
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

