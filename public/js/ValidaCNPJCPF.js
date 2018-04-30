// valida o cpf e o cnpj deverá ser usado no custom DomValidator
// abaixo exemplo de utilização deste validador
//<cc1:CustomDomValidator id="CustomDomValidator2" runat="server" Width="231px" CssClass="validacao" Display="Dynamic" ControlToValidate="CPF" ErrorMessage="Digito de controle do CPF incorreto" ClientValidationFunction="ValidaCNPJCPF"></cc1:CustomDomValidator></asp:panel></TD>

function ValidaCNPJCPF(oSrc, args)
{
	valor = TiraMascara2(args);
	if (valor.length <= 11)valor = ("0000000000000000000").substring(0,11-valor.length) + valor;
	else valor = ("0000000000000000000").substring(0,14-valor.length) + valor;
	return (checa(valor))
}

function checa(CPF_CNPJ)
{
	if ((CPF_CNPJ.length != 14) && (CPF_CNPJ.length !=11))
	{
		return false;
	}

	if (parseInt(CPF_CNPJ,10) == 0)
	{
		return false;
	}

		if ((!(modulo(CPF_CNPJ.substring(0,CPF_CNPJ.length - 2)).toString()+modulo(CPF_CNPJ.substring(0,CPF_CNPJ.length -
1)).toString() == CPF_CNPJ.substring(CPF_CNPJ.length -
2,CPF_CNPJ.length))) && (modulo_cic(CPF_CNPJ.substring(0,CPF_CNPJ.length - 2)) + "" + modulo_cic(CPF_CNPJ.substring(0,CPF_CNPJ.length - 1)) != CPF_CNPJ.substring(CPF_CNPJ.length - 2,CPF_CNPJ.length))){
					return false;
				}
				return true;
			}

			function modulo(CPF_CNPJ){
				soma=0;
				ind=2;

				for(pos=CPF_CNPJ.length-1;pos>-1;pos=pos-1){
					soma = soma + (parseInt(CPF_CNPJ.charAt(pos)) * ind);
					ind++;

					if(CPF_CNPJ.length>11){
						if(ind>9) ind=2;
			        }
				}

				resto = soma - (Math.floor(soma / 11) * 11);

				if(resto < 2){
					return 0;
				}
				else{
					return (11 - resto);
				}
			}

			function modulo_cic(CPF_CNPJ){
			   	soma=0;
				ind=2;

				for(pos=CPF_CNPJ.length-1;pos>-1;pos=pos-1){
					 soma = soma + (parseInt(CPF_CNPJ.charAt(pos)) * ind);
					 ind++;

					 if(CPF_CNPJ.length>11){
					    if(ind>9) ind=2;
					 }
				}

				resto = soma - (Math.floor(soma / 11) * 11);

				if(resto < 2){
					return 0;
				}
				else{
					return 11 - resto;
				}
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

