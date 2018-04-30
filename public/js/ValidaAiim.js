function ValidaAiim6(oSrc, args)// consiste o caso de 6 d�gitos
{
	return (args.length == 6);//aiim manual, n�o tem d�gito
}

function ValidaAiim7(oSrc, args)// consiste o caso de 7 d�gitos
{
	if (args.length > 7)//o m�ximo s�o 7 d�gitos 
		return false;	
	//completa com zeros a esquerda
	aiim = (("000000").substring(0,7-TiraMascara2(args).length) + args);
	//verifica o digito de controle
	soma = aiim.charAt(0)*7 +aiim.charAt(1)*6 +aiim.charAt(2)*5 +aiim.charAt(3)*4 +aiim.charAt(4)*3 +aiim.charAt(5)*2;
	dc=soma%11;
	if (dc == 10) dc=0;
	return (parseInt(aiim.charAt(6)) == dc);
}

function ValidaAiim9(oSrc, args)// consiste o caso de 9 d�gitos
{
	aiim = ("000000000").substring(0,9-args.length)+args;
	if (aiim == "000000000")//o aiim tem 9 d�gitos e n�o pode ser 000000000
		return false;
	// se a esquerda tiver dois zeros aceita
	if (aiim.charAt(0) == "0" & aiim.charAt(1) == "0")
		return true;
	
	//verifica o digito de controle
	soma = aiim.charAt(0)*9 +aiim.charAt(1)*8 +aiim.charAt(2)*7 +aiim.charAt(3)*6 +aiim.charAt(4)*5 +aiim.charAt(5)*4 +aiim.charAt(6)*3 +aiim.charAt(7)*2;
	dc=soma%11;
	if (dc == 10) dc=0;
	return (parseInt(aiim.charAt(8)) == dc);
}

function ValidaAiim6ou9(oSrc, args)// consiste o caso de 9 d�gitos
{
	if (args.length <= 6) return true;
	return ValidaAiim9(oSrc, args);
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

