$(document).ready(function () {

	$(".offer-for-client").click(function(){
	
		id = $(this).attr('id');
		
		
		
			$.ajax({			
			type: "POST",			
			data:{
				id:id
			},			
			url: "/jezzy-business/portal/clientReport/offerByUser",
			success: function(result){	
				
				//MUDA LOCAL
				window.location="/jezzy-business/portal/product/productManipulation"; 
				
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert("Houve algume erro no processamento dos dados desse usuário, atualize a página e tente novamente!");
		}
	  });
		
	});
	
	
	$(".offer-for-profile").click(function(){
		
		var id = $(this).attr('id');
			
			
		$.ajax({			
			type: "POST",			
			data:{
				id:id
			},			
			url: "/jezzy-business/portal/clientReport/getProfileByUser",
			success: function(result){	
				
				//MUDA LOCAL
				window.location="/jezzy-business/portal/product/productManipulation"; 
				
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert("Houve algume erro no processamento dos dados desse usuário, atualize a página e tente novamente!");
		}
	  });
	  
	});

});

function showUserDetail(id){
		
	$.ajax({			
			type: "POST",			
			data:{
				userId:id
			},			
			url: "/jezzy-business/portal/clientReport/getClienteDetail",
			success: function(result){	
				
			$("#recebe").html(result);
			$('#myModalSchedulesRequisitions').modal('toggle');
			 $('#myModalSchedulesRequisitions').modal('show');
			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert("Houve algume erro no processamento dos dados desse usuário, atualize a página e tente novamente!");
		}
	  });
	
}