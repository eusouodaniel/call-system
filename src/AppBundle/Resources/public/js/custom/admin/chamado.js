$(document).ready(function () {

	var routeUrl = Routing.generate('backend_get_contract_type');

	$.ajax({
		url: routeUrl,
		type: 'GET',
		dataType: 'json',
		success: function(data){

			console.log(data.contract);
			lockDtLimit(data.contract);
			        
		}
	});

});

function lockDtLimit(contract) {

	if (contract == 'Contrato') {
		$('#appbundle_chamado_dtLimit').attr('readonly', 'readonly');
	}

}