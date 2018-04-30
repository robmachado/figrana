function ValidaMesAno(sender, args)
{
	var valor = TiraMascara2(args);
	//completa com 6 dígitos	
	valor = (("0000000000000000000").substring(0,6-valor.length) + valor);

	var mes = valor.substring(0,2);
	var ano = valor.substring(2,6);
	return !(mes<'01' | mes >'12' |isNaN(ano)|isNaN(mes)|ano<'1900' | ano > '2100');
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
