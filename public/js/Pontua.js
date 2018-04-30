// adiciona ponto de milhar e virgual na segunda casa, usar no onkeyup=fPontua(this).			
function fPontua(obj,e){
	if(e.keyCode==9|e.keyCode==37|e.keyCode==39)return;
	if(obj.value=='')return;
	zeros = "000";
	wvalor = limpaString2(obj.value);
	wvalor = zeros.substring(0,(3-wvalor.length)) + wvalor;
	comp=wvalor.length;
	result="";
	for(var i=0; i< (comp - 2)/3-1; i++){
		result= "." + wvalor.substring(comp - 2 -(i+1)*3,comp - 2 - i*3) + result;
	}
	obj.value = wvalor.substring(0,comp-2-i*3) + result + "," + wvalor.substring((comp-2),comp);
}
function limpaString2(S){
	var digitos = "0123456789";
	var temp = "";
	var Digito = "";
	for(var i=0; i<S.length; i++){
		Digito = S.charAt(i);
		if(digitos.indexOf(Digito) >= 0 && (Digito!=0 || temp != "")){ // esta linha tira os zeros a esquerda
			temp = temp + Digito
		}
	}
	return temp
}
function limpaString(S){
	var digitos = ",0123456789.";
	var temp = "";
	var Digito = "";
	for(var i=0; i<S.length; i++){
		Digito = S.charAt(i);
		if(digitos.indexOf(Digito) >= 0 && (Digito!=0 || temp != "")){ // esta linha tira os zeros a esquerda
			if (Digito == ",") {Digito="."};
			if (Digito == ".") {Digito= (i==(S.length-3)?".":"")}
			temp = temp + Digito;
		}
	}
	if (temp.length ==3) temp= "0" + temp;
	return temp
}


			

