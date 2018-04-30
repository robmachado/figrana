function ValidaIE(oSrc, args)
{
	//completa com nove dígitos	
	numero = TiraMascara2(args);
	numero = ("000000000").substring(0,12-numero.length) + numero;

	return (ieValido(numero));
}

function ieValido(numero)
{
	var soma = 0;
	var digito;

	// Se tiver mais ou menos que 12 dígitos, devolve o erro.
	if (numero.length != 12) return (false);
	
	//calcula o primeiro dígito
	soma  = numero.charAt(0)*1;
	soma += numero.charAt(1)*3;
	soma += numero.charAt(2)*4;
	soma += numero.charAt(3)*5;
	soma += numero.charAt(4)*6;
	soma += numero.charAt(5)*7;
	soma += numero.charAt(6)*8;
	soma += numero.charAt(7)*10;

	digito = soma % 11;
	if (digito > 9)	digito = 0;

	if (parseInt(numero.charAt(8)) != digito)return false;

	// trata o produtor rural
	if (parseInt(numero.charAt(0)) == 0)return true;
	
	// Se a IE não for de produtor rural, o segundo dígito é calculado.
	soma  = numero.charAt(0)*3;
	soma += numero.charAt(1)*2;
	soma += numero.charAt(2)*10;
	soma += numero.charAt(3)*9;
	soma += numero.charAt(4)*8;
	soma += numero.charAt(5)*7;
	soma += numero.charAt(6)*6;
	soma += numero.charAt(7)*5;
	soma += numero.charAt(8)*4;
	soma += numero.charAt(9)*3;
	soma += numero.charAt(10)*2;
	
	digito = soma % 11;
	if (digito > 9)digito = 0;

	return (parseInt(numero.charAt(11)) == digito);
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

