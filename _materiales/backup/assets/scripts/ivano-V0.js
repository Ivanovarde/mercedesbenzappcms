$(window).on('load', function () {

	$('#preloader').addClass('off');
	window.setTimeout(function () {
		$('#preloader').remove();
	}, 1500);

	//Verifico si existe la URL de acceso al BackOffice
	var url_backoffice = window.localStorage.getItem("url_backoffice");

	// Si hay datos localmente
	if (url_backoffice != null && url_backoffice !== '') {
		var syncURL = url_backoffice;
	}

	// Analizo los parametros del Cotizador
	if (window.location.pathname.search("/cotizacion.html") != -1) {

		showQuoteData('cotizador');

		// Actulizacion de las imagenes del Pie
		$("#cotizador_img1_cotizador").attr("src", "images/vehicles/450x270-" + getParameters("vehicletype") + "1.jpg");
		$("#cotizador_img2_cotizador").attr("src", "images/vehicles/450x270-" + getParameters("vehicletype") + "2.jpg");
	}

	// Analizo los parametros del Formulario de Cotizador
	if (window.location.pathname.search("/formulario.html") != -1) {

		showQuoteData('form');

		// Si es el caso de Plan 84, agrego una clase para ocultar encabezado
		if (getParameters("vehicletype") == "pickup") {
			$("#encabezado_formulario").addClass("no-info");
		} else { // Saco la clase no-info
			$("#encabezado_formulario").removeClass("no-info");
		}

	}

	app.initialize();

});

$(window).on('scroll', function (e) {

});

jQuery(document).ready(function () {
	//console.log(arrayvan);
	updatePendingRecordsCounter();

	//console.log(window['arrayvan'][1]);

});


var aKeys = ['payment', 'line', 'model', 'precioPublico', 'precioPublico100', 'derechoSuscripcion', 'iva', 'totalSuscripcion', 'cuotaPura', 'cargaAdminSuscripcion', 'iva21', 'cuotaMensual', 'pagoAdjudicacion30', 'plan'];

$(document).on('click', '.caption .budget > a', function(e){ makeQuote({e: e}); });

// SET URL BACKOFFICE ======================================================
$("#btn-backoffice").click(function () {

	var url_ingresada = prompt("Ingrese la URL del BackOffice", syncURL);

	if (url_ingresada != null) {

		// Guardo los datos
		window.localStorage.setItem("url_backoffice", url_ingresada);
		syncURL = url_ingresada;
	}

});

// SINCRONIZAR CONTACTOS ======================================================
$("#btn-pending-records").click(function () {

	var counter = 0;

	//Si tengo datos guardados localmente, los consulto directamente desde ahi
	var storedRecords = JSON.parse(window.localStorage.getItem("storedRecords"));

	console.log('ivano.js app.isConnnected: ' + app.isConnected);
	// Si no estoy conectado a Internet, cancelo
	if (app.isConnected) {

		// Si hay datos localmente
		if (storedRecords !== null && storedRecords.length > 0) {
			counter = storedRecords.length;

			if (confirm('¿Desea sincronizar los contactos locales?')) {
				syncPendingRecords();
			}

		} else {
			alert('No hay contactos para sincronizar.');
		}
	} else {
		alert('No se puede iniciar la sincronización porque no hay conexión a Internet.');
	}

});

// ENVIAR_COTIZACION ======================================================
$("#btn-send-quote").click(function (event) {
	event.preventDefault();

	var aCurrentRecord = [];
	var recordStore = [];

	// Valido que el formulario no este vacio
	if ($("#nombre").val() === "" || $("#apellido").val() === "" || $("#email").val() === "" || $("#telefono").val() === "" || $("#provincia").val() === "" || $("#ciudad").val() === "") {
		alert('Por favor, completar todos los campos');

		return false;
	}

	// Cargo Loading...
	$("#btn-send-quote").html("Enviando...");

	aCurrentRecord = filterVehicleArrayFromURL();

	// Obtengo los datos de la cotización
	recordStore["0"] = getParameters("payment");
	recordStore["1"] = aCurrentRecord[0].cuotaMensual.replace(/,/g, "|");
	recordStore["2"] = aCurrentRecord[0].precioPublico.replace(/,/g, "|");
	recordStore["3"] = aCurrentRecord[0].plan;
	recordStore["4"] = aCurrentRecord[0].cuotaPura.replace(/,/g, "|");
	recordStore["5"] = aCurrentRecord[0].cargaAdminSuscripcion.replace(/,/g, "|");
	recordStore["6"] = aCurrentRecord[0].iva21.replace(/,/g, "|");
	recordStore["7"] = aCurrentRecord[0].pagoAdjudicacion30.replace(/,/g, "|");
	recordStore["8"] = aCurrentRecord[0].model;
	//recordStore["9"] = aCurrentRecord[0]["plan"];
	recordStore["16"] = getParameters("line");
	recordStore["17"] = getParameters("vehicletype");

	// Obtengo los datos del Formulario
	recordStore["10"] = $("#nombre").val();
	recordStore["11"] = $("#apellido").val();
	recordStore["12"] = $("#email").val();
	recordStore["13"] = $("#telefono").val();
	recordStore["14"] = $("#provincia").val();
	recordStore["15"] = $("#ciudad").val();

	// Si estoy conectado a Internet, guardo los datos en BD
	if (app.isConnected) {

		// Si falla el envio, lo guardo localmente
		sendRecord(recordStore);

	} else { // Si no estoy conectado a Internet, guardo los datos localmente para sync posteriormente

		storeRecordsLocal(recordStore);
	}

	$("#btn-send-quote").html("Enviar cotización");

});

// PREPARAR_COTIZACION ======================================================
$("#btn-prepare-quote").click(function (event) {
	event.preventDefault();

	var aCurrentRecord = filterVehicleArrayFromURL();

	//console.log(aCurrentRecord);
	var line = getParameters("line");
	var model = aCurrentRecord[0]["model"];
	var plan = aCurrentRecord[0]["plan"];
	var payment = aCurrentRecord[0]["payment"];

	// Armo la URL del Formulario Cotizador
	var urlParameter = '';

	if (line != '' && model != '' && plan != '' && payment != '') {
		urlParameter = 'vehicletype=' + escape(getParameters("vehicletype")) + '&line=' + escape(line) + '&model=' + escape(model) + '&plan=' + escape(plan) + '&payment=' + escape(payment);

		window.location.href = "formulario.html?" + urlParameter;
	} else {
		alert('Faltan elegir opciones para la cotización');

		return;
	}

});

// VEHICLES SELECTORS ======================================================
$('.selector').on('changed.bs.select', function (e) {
	refreshSelector( $(this).data('selector'), $(this).data('vehicletype'), $(this).val() );
});


// VARIABLES
var syncURL = "http://mercedesappcms.nmd";


// FUNCTIONS
function refreshSelector(selector, vehicleType, value) {
	var lineValue, modelValue, planValue, paymentValue = null;
	var el = $("#" + vehicleType + "-" + selector);
	var target = el.data('target');
	var targetElement = $("#" + vehicleType + "-" + target);
	var selectedValue = value;
	var selectors = el.parents('.thumbnail').find('select');
	var selectorsLength = selectors.length;
	var selectorIndex = selectors.index(el);
	var params = {};

	for(var s = 0; s < selectorsLength; s++){
		if(s > selectorIndex){
			resetSelector(selectors.eq(s));
		}
	}

	lineValue = el.parents('.thumbnail').find('#' + vehicleType + '-line').val();
	modelValue = el.parents('.thumbnail').find('#' + vehicleType + '-model').val();
	planValue = el.parents('.thumbnail').find('#' + vehicleType + '-plan').val();
	paymentValue = el.parents('.thumbnail').find('#' + vehicleType + '-payment').val();

	// Obtengo los datos del array
	params = {
		vehicle: vehicleType,
		selector: selector,
		value: selectedValue,
		target: target,
		line: lineValue,
		model: modelValue,
		plan: planValue,
		payment: paymentValue
	}

	//console.log(params);

	var selectorOptions = getSelectorData(params);

	fillSelector({el: targetElement, options: selectorOptions});
}

function getSelectorData(data) {
	//console.log(data);

	if(data.vehicle === undefined ){
		console.log('ERROR: getSelectorData: No vehicle type');
		return;
	}

	if(data.value === undefined ){
		console.log('ERROR: getSelectorData: No value selected');
		return;
	}

	var vehicleType = data.vehicle;
	var selectedValue = data.value;
	var selector = data.selector;
	var target = data.target || '';
	var line = data.line || '';
	var model = data.model || '';
	var plan = data.plan || '';
	var payment = data.payment || '';
	var aMain = window['array' + vehicleType][(line - 1)];
	var aMatrix = [];
	var aReturnData = [];
	var aElements = [];

	if(aMain === undefined){
		console.log('aMain is not defined');
		return;
	}

	if(aMain[line] !== undefined){

		aMatrix = loadMatrix(aMain[line], aKeys);

		for(var i = 0; i < Object.keys(aMatrix).length; i++){
			var el = aMatrix[i];

			for (var prop in el) {

				// skip loop if the property is from prototype
				if(!el.hasOwnProperty(prop)) continue;

				//console.log('selectedvalue: ' + selectedValue + ' | value: ' + el[prop] + ' | prop: ' + prop + ' | target: ' + target + ' | Selector: ' + selector);

				if(selectedValue != 1 && (el[selector] == selectedValue)){
					if(el[target] !== undefined){
						//console.log('coincide');
						//console.log(el);
						//console.log(el.model);
						//console.log($('#' + vehicleType + '-' + selector).val());
						//console.log(selectedValue);
						//console.log(el[target]);
						aElements.push(el[target]);
					}
				}

				if( prop == target && selectedValue == line ){
					aElements.push(el[prop]);
				}
			}
		}
		return aElements;
	}
}

function fillSelector(data){
	//console.log(data);
	var el = data.el;
	var options = data.options;
	var aUniqueValues = [];
	var selectorOption = '';

	$.each(options, function(i, option){

		if($.inArray(option, aUniqueValues) === -1) {
			aUniqueValues.push(option);

			selectorOption = '<option value="' + option + '" >' + option + '</option>';

			el.append(selectorOption);
		}

	});

	el.selectpicker("refresh");
}

function resetSelector(el){
	el.empty().append('<option value="" selected="">' + el.data('default') + '</option>');
	el.selectpicker("refresh");
}

function loadMatrix(matrix, aKeys){
	var data = {};

	for(var i = 0; i < matrix.length; i++){
		var r = matrix[i];
		var row = {};

		$(r).each(function(j, o){
			row[aKeys[j]] = o;
		});

		data[i] = row;
	}

	return data;
}

function filterVehicleArrayFromURL(){
	var vehicleType = unescape(getParameters("vehicletype"));
	var line = unescape(getParameters("line"));
	var model = unescape(getParameters("model"));
	var plan = unescape(getParameters("plan"));
	var payment = unescape(getParameters("payment"));

	console.log(vehicleType);

	var aMain = window['array' + vehicleType][(line - 1)];
	var aMatrix = [];
	var aCurrentRecord = [];

	if(aMain === undefined){
		console.log('aMain is not defined');
		return;
	}

	if(aMain[line] === undefined){
		console.log();
		return;
	}

	aMatrix = loadMatrix(aMain[line], aKeys);

	for(var i = 0; i < Object.keys(aMatrix).length; i++){
		var el = aMatrix[i];

		for (var prop in el) {

			// skip loop if the property is from prototype
			if(!el.hasOwnProperty(prop)){ continue;}

			//console.log('vehicleType: ' + el[vehicleType] + ' == ' + unescape(getParameters("vehicletype")) + ', model: ' + el[model] + ' == ' + el[model] + ', plan: ' + el[plan] + ' == '+ plan + ', payment: ' + el[payment] + '== ' + payment);

			if(model == el.model && el.plan == plan && el.payment == payment){
				aCurrentRecord.push(el);
				break;
			}

		}
	}
	//console.log(aCurrentRecord);
	return aCurrentRecord;
}

function sendRecord(recordStore){

	console.log('sendRecord start');
	console.log(recordStore);

	var aRecordTemp = [];

	$.ajax({
		url: syncURL + '/system/php/actions.php?action=store&storedRecords=' + JSON.stringify(recordStore),
		type: 'GET',
		//data: d,
		dataType: 'json',
		charset: 'UTF-8',
		timeout: 10000
	})
	.done(function (response, status, xhr) {

		// Si no hubo error
		if (response.status) {

			// Vacio los registros offline
			window.localStorage.removeItem("storedRecords");

			//Inicializo el Formulario
			$('#form-quote').trigger("reset");
			$("#provincia").val('default');
			$("#provincia").selectpicker("refresh");

		}

		alert(response.msg);
		console.log(response.msg);

	})
	.fail(function (jqXHR, status, errorThrown) {
		console.log('getEditForm: Fail: ');
		console.log(jqXHR);
		console.log(status);
		console.log(errorThrown);

		aRecordTemp.push(recordStore);
		console.log('Fallo sendRecord(): recordStore: ' + recordStore);

		storeRecordsLocal(recordStore);

	})
	.always(function (response, status, xhr) {

		$("#btn-send-quote").html("Enviar cotización");

	});
}

function makeQuote(data) {
	var el = $(data.e.currentTarget);
	var vehicleType = el.data('vehicle-type');
	var line = $('#' + vehicleType + '-line').val();
	var model = $('#' + vehicleType + '-model').val();
	var plan = $('#' + vehicleType + '-plan').val();
	var payment = $('#' + vehicleType + '-payment').val();

	//arrayAux = eval('array' + selector);
	var aMain = window['array' + vehicleType][(line - 1)];

	console.log(el);
	console.log(vehicleType);
	console.log(line);
	console.log(aMain);

	//return;

	// Armo la URL del Cotizador
	var urlParameter = '';

	if (line !== '' && model !== '' && plan !== '' && payment !== '') {
		urlParameter = 'vehicletype=' + escape(vehicleType) + '&line=' + escape(line) + '&model=' + escape(model) + '&plan=' + escape(plan) + '&payment=' + escape(payment);

		window.location.href = "cotizacion.html?" + urlParameter;
	} else {
		alert('Faltan elegir opciones para la cotización');
		return;
	}
}

function showQuoteData(mode) {

	var aCurrentRecord = filterVehicleArrayFromURL();

	//console.log(aCurrentRecord);

	var casuscripcion = aCurrentRecord[0]["cargaAdminSuscripcion"].replace('\.', '').replace('\,', '.');
	var iva21 = aCurrentRecord[0]["iva21"].replace('\.', '').replace('\,', '.');
	var gastos_adm_suscrip = parseFloat(casuscripcion) + parseFloat(iva21);
	var gastos_adm_suscrip = parseFloat(gastos_adm_suscrip).toLocaleString('de-DE');

	// Cargo el resultado del Cotizador
	$("#" + mode + "_cant_cuotas").html(getParameters("payment"));
	$("#" + mode + "_total_cuota_mensual").html('$' + aCurrentRecord[0]["cuotaMensual"]);
	$("#" + mode + "_precio_vehiculo_iva").html(aCurrentRecord[0]["precioPublico"]);
	$("#" + mode + "_tipo_plan").html(aCurrentRecord[0]["plan"]);
	$("#" + mode + "_cuota_pura").html(aCurrentRecord[0]["cuotaPura"]);
	$("#" + mode + "_gastos_adm_suscrip").html(gastos_adm_suscrip);
	$("#" + mode + "_gastos_adm_suscrip_iva").html(aCurrentRecord[0]["iva21"]);
	$("#" + mode + "_alicuota").html((aCurrentRecord[0]["pagoAdjudicacion30"] || '0'));
	$("#" + mode + "_modelo_cotizador").html(aCurrentRecord[0]["model"]);
	$("#" + mode + "_nombre_plan_cotizador").html(aCurrentRecord[0]["plan"]);

	// Actualizacion de la imagen principal
	$("#" + mode + "_img_ppal").attr("src", "images/vehicles/" + getParameters('vehicletype') + "-line" + getParameters('line') + ".jpg");

}

function updatePendingRecordsCounter() {

	var counter = 0;

	//Si tengo los datos guardados localmente, los consulto directamente desde ahi
	var storedRecords = JSON.parse(window.localStorage.getItem("storedRecords"));

	// Si hay datos localmente
	if (storedRecords != null) {
		counter = storedRecords.length;
	}

	// ACtualizo el Bubble Count de Contactos Pendientes
	$('#pendingRecordsTotal').html(counter);
}

function storeRecordsLocal(recordStore) {

	var aRecords = [];

	//Si tengo los datos guardados localmente, los consulto directamente desde ahi
	var storedRecords = JSON.parse(window.localStorage.getItem("storedRecords"));

	// Si hay datos localmente
	if (storedRecords !== null) {
		// Obtengo los datos de los registros previos para no perderlos
		aRecords = storedRecords;
	}

	// Agrego el contacto actual al arreglo
	aRecords.push(recordStore);

	// Guardo los datos
	window.localStorage.setItem("storedRecords", JSON.stringify(aRecords));

	// Inicializo el Formulario
	$('#form-quote').trigger("reset");
	$("#provincia").val('default');
	$("#provincia").selectpicker("refresh");

	// Alert
	alert('Los datos se guardaron localmente para posterior sincronización.');

	// Actualizo Contador
	updatePendingRecordsCounter();
}

function syncPendingRecords() {

	// Para generar la cola de pendientes
	var aRecordTemp = [];

	//Si tengo los datos guardados localmente, los consulto directamente desde ahi
	var storedRecords = JSON.parse(window.localStorage.getItem("storedRecords"));

	// Si hay datos localmente
	if (storedRecords !== null && storedRecords.length > 0) {

		$.each(storedRecords, function (key, value) {

			sendRecord(value);

		});

	}
}


function getParameters(k) {
	var p = {};
	location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (s, k, v) {
		p[k] = v
	})
	return k ? p[k] : p;
}

function doScroll(event) {
	var el = $(event.currentTarget);
	var fullUrl = el.attr('href') !== undefined ? el.attr('href') : '';
	var parts, targetEl, trgt, targetOffset, targetTop;
	event.preventDefault();

	targetTop = 0;

	if (fullUrl) {
		parts = fullUrl.split("#");
		trgt = parts[1];
		targetEl = $("#" + trgt);
		targetOffset = targetEl.offset();
		targetTop = targetOffset.top;
	}

	$('html, body').animate({
		scrollTop: targetTop
	}, 800);
}


String.prototype.float = function () {
	return parseFloat(this.replace(',', ''));
};

$.fn.wait = function (time, type) {
	time = time || 1000;
	type = type || "fx";
	return this.queue(type, function () {
		var self = this;
		setTimeout(function () {
			$(self).dequeue();
		}, time);
	});
};
