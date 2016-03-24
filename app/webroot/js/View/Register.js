$(function(){
/*
	$("#companyForm").submit(function(event){
		
		event.preventDefault();
		var form = $(this);
		 var url = form.attr("action");
			var formData = {};
			$(form).find("input[name]").each(function (index, node) {
				formData[node.name] = node.value;
			});
			$.post(url, formData).done(function (data) {
				alert(data);
			});
		
	});*/
	
	
	$(".phone").mask("(00) 0000-00009");
	
	$(".cnpj").mask("99.999.999/9999-99");
	
	$(".cpf").mask("99.999.999/9999-99");
	
	$("#cep").keyup(function(){
		var zipcode = $("#cep").val();
		
		if(zipcode.length == 8){
		alert('chamou');
			$.ajax({			
			type: "POST",			
			data:{
				cep:zipcode
			},			
			url: "/jezzy-business/portal/company/searchAddressByZipcode",
			success: function(result){	
				
				 var objReturn = JSON.parse(result);
				 console.log(objReturn);
				 
				 $("#bairro").val(objReturn.bairro);
				 $("#localidade").val(objReturn.localidade);
				 $("#logradouro").val(objReturn.logradouro);
				 $("#uf").val(objReturn.uf);
			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert("Houve algume erro no processamento dos dados desse usuário, atualize a página e tente novamente!");
		}
	  });
		}
	});
});

function bomDia($cafe){
	
	if($cafe == "MUITO"){
		$dia = "Otimo";
	}else if($cafe == "MAIS OU MENOS"){
		$dia = "Legalzinho";
	}else if($cafe == "NADA"){
		$dia = false;
	}
	
	return $dia;
	
}