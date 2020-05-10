$(function () {
	$("#appbundle_chamado_attendance, #appbundle_chamado_item").change(function() {
		var attendanceId = $("#appbundle_chamado_attendance").val();
		var itemId = $("#appbundle_chamado_item").val();

		generateSlaHours(attendanceId, itemId);
	});
});

function generateSlaHours(attendanceId, itemId) {

	if (attendanceId != "" && itemId != "") {
		getSlaHours(attendanceId, itemId);
	}
}

function getSlaHours(attendanceId, itemId) {

	var slaHours = null;

	var routeUrl = Routing.generate('backend_sla_find', {attendanceId: attendanceId, itemId: itemId});

	$.ajax({
		url: routeUrl,
		type: 'GET',
		dataType: 'json',
		success: function(data){
			dtLimit = data.dados;
			$("#prazo-atendimento").text(dtLimit);
		}
	});
}