
jQuery(document).ready(function($) {

	$('.delete_item').click(function(event) {
		event.preventDefault();
		var user = confirm("Tem certeza que deseja excluir esse registro?");

		if (user === true) {
			window.location.href = $(this).data('url');
		}
	});

	$("input.telefone")
	.mask("(99) 9999-9999?9")
	.focusout(function (event) {
		var target, phone, element;
		target = (event.currentTarget) ? event.currentTarget : event.srcElement;
		phone = target.value.replace(/\D/g, '');
		element = $(target);
		element.unmask();
		if(phone.length > 10) {
			element.mask("(99) 99999-999?9");
		} else {
			element.mask("(99) 9999-9999?9");
		}
	});

	$("input.cep")
	.mask("99999-999");

	$("input.cnpj").mask("99.999.999/9999-99")
	.focusout(function (event) {
		var target, phone, element;
		target = (event.currentTarget) ? event.currentTarget : event.srcElement;
		cnpj = target.value.replace(/\D/g, '');
		element = $(target);
		if(cnpj == ""){
			return true;
		}
					// Elimina CNPJs invalidos conhecidos
					    if (cnpj == "00000000000000" ||
						        cnpj == "11111111111111" ||
						        cnpj == "22222222222222" ||
						        cnpj == "33333333333333" ||
						        cnpj == "44444444444444" ||
						        cnpj == "55555555555555" ||
						        cnpj == "66666666666666" ||
						        cnpj == "77777777777777" ||
						        cnpj == "88888888888888" ||
						        cnpj == "99999999999999"){
						alert("CNPJ inválido");
								//$(target).focus();
								//$(target).val("");
								document.getElementById("appbundle_company_cnpj").value = "";
								return false;
								
							}else{
						// Valida DVs
						    tamanho = cnpj.length - 2;
						    numeros = cnpj.substring(0,tamanho);
						    digitos = cnpj.substring(tamanho);

						    soma = 0;
						    pos = tamanho - 7;
						    for (i = tamanho; i >= 1; i--) {
							      soma += numeros.charAt(tamanho - i) * pos--;
							      if (pos < 2)
								            pos = 9;
						    }

						    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
						    if (resultado != digitos.charAt(0)){
							alert("CNPJ inválido");
							//$(target).focus();
							//$(target).val("");
							document.getElementById("appbundle_company_cnpj").value = "";
							return false;
						}
						         
						    tamanho = tamanho + 1;
						    numeros = cnpj.substring(0,tamanho);
						    soma = 0;
						    pos = tamanho - 7;

						    for (i = tamanho; i >= 1; i--) {
							      soma += numeros.charAt(tamanho - i) * pos--;
							      if (pos < 2){
								pos = 9;
							}
						    }
						    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
						    if (resultado != digitos.charAt(1)){
							alert("CNPJ inválido");
							//$(target).focus();
							//$(target).val("");
							document.getElementById("appbundle_company_cnpj").value = "";
							return false;

							
						}
					}
					    
				});

	$("input.cpf").mask("999.999.999-99")
	.focusout(function (event) {
		var target, cpf, element;
		target = (event.currentTarget) ? event.currentTarget : event.srcElement;
		cpf = target.value.replace(/\D/g, '');
		element = $(target);
		if(cpf == ""){
			return true;
		}
				//Elimina CPFs invalidos conhecidos
				if (cpf.length != 11 ||
					cpf == "00000000000" ||
					cpf == "11111111111" ||
					cpf == "22222222222" ||
					cpf == "33333333333" ||
					cpf == "44444444444" ||
					cpf == "55555555555" ||
					cpf == "66666666666" ||
					cpf == "77777777777" ||
					cpf == "88888888888" ||
					cpf == "99999999999"){
					alert("CPF inválido");
				element.val("");
				return false;
			}
				// Valida 1o digito

				add = 0;
				for (i=0; i < 9; i ++)
					add += parseInt(cpf.charAt(i)) * (10 - i);
				rev = 11 - (add % 11);
				if (rev == 10 || rev == 11)
					rev = 0;
				if (rev != parseInt(cpf.charAt(9))){
					$("input.cpf").val("");
					alert("CPF inválido");
					element.val("");
					return false;
				}

				// Valida 2o digito
				add = 0;
				for (i = 0; i < 10; i ++)
					add += parseInt(cpf.charAt(i)) * (11 - i);
				rev = 11 - (add % 11);
				if (rev == 10 || rev == 11)
					rev = 0;
				if (rev != parseInt(cpf.charAt(10))){
					$("input.cpf").val("");
					alert("CPF inválido");
					element.val("");
					return false;
				}
				return true;
				    
			});
});
