$(window).on('load', function () {


	//Verifico si existe la URL de acceso al BackOffice
	var url_backoffice = window.localStorage.getItem("url_backoffice");

	// Si hay datos localmente
	if (url_backoffice != null) {
		var syncURL = url_backoffice;
	}

	app.initialize();

});

$(window).on('scroll', function (e) {

});

jQuery(document).ready(function () {
	console.log('ready start');

	if($('.home').length){
		isHome = true;
	}

	getMainData();

	updatePendingRecordsCounter();

	//console.log(window['arrayvan'][1]);

	console.log('ready end');
});


var aKeys = ['cuotas', 'linea', 'modelo', 'precio_publico', 'precio_publico_100', 'derecho_suscripcion', 'iva', 'total_suscripcion', 'cuota_pura', 'carga_admin_suscripcion', 'iva_21', 'cuota_mensual', 'pago_adjudicacion_30', 'plan', 'tipo_vehiculo_url', 'tipo_vehiculo'];

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

	$('#form-quote').find(':input').each(function(i, o){
		var attr = $(o).attr('name');

		console.log('input name: ' + $(o).attr('name') + ' - valor: ' + $(o).val());

		aCurrentRecord[0][attr] = $(o).val();
	});

	console.log('#btn-send-quote -> aCurrentRecord');
	console.log(aCurrentRecord);
	console.log(aCurrentRecord[0]);

	// Si estoy conectado a Internet, guardo los datos en BD
	if (app.isConnected) {

		// Si falla el envio, lo guardo localmente
		//sendRecord(recordStore);
		sendRecord(aCurrentRecord);

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
	var linea = getParameters("linea");
	var modelo = aCurrentRecord[0]["modelo"];
	var plan = aCurrentRecord[0]["plan"];
	var cuotas = aCurrentRecord[0]["cuotas"];

	// Armo la URL del Formulario Cotizador
	var urlParameter = '';

	if (linea != '' && modelo != '' && plan != '' && cuotas != '') {
		urlParameter = 'vehicletype=' + escape(getParameters("vehicletype")) + '&linea=' + escape(linea) + '&modelo=' + escape(modelo) + '&plan=' + escape(plan) + '&cuotas=' + escape(cuotas);

		window.location.href = "formulario.html?" + urlParameter;
	} else {
		alert('Faltan elegir opciones para la cotización');

		return;
	}

});

//// VEHICLES SELECTORS ======================================================
//$('.selector').on('changed.bs.select', function (e) {
//	refreshSelector( $(this).data('selector'), $(this).data('vehicletype'), $(this).val() );
//});


// VARIABLES
//var syncURL = "http://expoagroapp2019.neomedia.com.ar/cms";
var isHome = false;
var appMainData;
var appvehiculos = {};
var appcontenido = {};
var appcategorias = {};

// FUNCTIONS

function getMainData(){
	console.log( "getMainData start" );
	var mainDataRequest;

	if(localStorage.getItem('appJsonData')){
		appMainData = JSON.parse(window.localStorage.getItem('appJsonData'));

		console.log('getMainData from localstorage');
		//console.log(appMainData);

		setApp();
	}else{

		mainDataRequest = $.getJSON( syncVehicles, function(data, textStatus, jqXHR) {
			console.log( "getMainData done" );

			appMainData = data;
			window.localStorage.setItem("appJsonData", JSON.stringify(appMainData));

			console.log('getMainData from server');
			//console.log(appMainData);

			setApp();
		})
		.fail(function() {
			console.log( "getMainData fail" );
		})
		.always(function() {
			console.log( "getMainData always" );
		});

	}

}

function setApp(){
	var vehiculos = appMainData.vehiculos;

	appcategorias = appMainData.categorias;
	appcontenido = appMainData.contenido;

	if(vehiculos.length > 0){
		$(vehiculos).each(function(i, o){
			var k = Object.keys(o);

			appvehiculos[k] = o[k];
		});

		//console.log(vehiculos.length); ;
		//console.log(vehiculos); ;
		//console.log(appvehiculos);
		//console.log(appcontenido);
		//console.log(appcategorias);

		setContent();

		if(isHome){

			setVehiclePanels();

		}else{
			// Analizo los parametros del Cotizador
			if (window.location.pathname.search("/cotizacion.html") != -1) {

				showQuoteData('cotizador');

				// Actulizacion de las imagenes del Pie
				//$("#cotizador_img1_cotizador").attr("src", "images/vehicles/450x270-" + getParameters("vehicletype") + "1.jpg");
				//$("#cotizador_img2_cotizador").attr("src", "images/vehicles/450x270-" + getParameters("vehicletype") + "2.jpg");
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
		}

	}else{
		console.log('No vehicles data');
	}

}

function setVehiclePanels(){

	if(! $('body.home').length){
		return;
	}

	var templatePanel = vehiclePanelTemplate;
	var templateOption = '<option value="{opcion_valor}">{opcion_nombre}</option>';
	var panels = $('#panels').find('.row');

	//console.log(Object.size(appcategorias));

	for(var cat in appcategorias){

		//console.log(cat);
		//console.log(appcategorias[cat]);
		//console.log(appvehiculos);
		//console.log(appvehiculos[o.url]);
		//console.log(o);
		//console.log(o.nombre);

		var c = appcategorias[cat];
		var categoria_imagen = c.imagen;
		var categoria_nombre = c.nombre;
		var categoria_url = c.url;
		var vehicleLines = appvehiculos[categoria_url];
		var panelInner = templatePanel;
		var linea_opciones = '';

		//console.log(vehicleLines);

		$(vehicleLines).each(function(j, e){
			var k = Object.keys(e);
			var opcion_nombre = e[k][0][1]; // Saco el nombre de la linea desde el primer vehiculo, elemento 2

			linea_opciones += templateOption.replace('{opcion_valor}', k).replace('{opcion_nombre}', opcion_nombre);
		});

		panelInner = panelInner.replaceAll('{categoria_nombre}', categoria_nombre);
		panelInner = panelInner.replaceAll('{categoria_url}', categoria_url);
		panelInner = panelInner.replaceAll('{linea_opciones}', linea_opciones);
		panelInner = panelInner.replaceAll('{categoria_imagen}', categoria_imagen);

		//console.log(vehicleLines);
		//console.log(panelInner);

		panels.append(panelInner);

	}
	//});

	panels.find('.selectpicker').selectpicker("refresh");

	// VEHICLES SELECTORS ======================================================
	$('.selector').on('changed.bs.select', function (e) {
		refreshSelector( $(this).data('selector'), $(this).data('vehicletype'), $(this).val() );
	});

}

function setContent(){
	var elements = $('.content-add');

	for (var key in appcontenido) {
		//console.log(key, jsonData[key]);
		var el = $('#' + key);

		if(el.length){
			var htmltag = el.get(0).nodeName.toLowerCase();

			if(key == el.attr('id')){

				switch(htmltag){
					case 'img':
						el.attr('src', appcontenido[key]);
					break;

					default:
						el.html(appcontenido[key]);
				}

			}
		}
	}

	$('#preloader').addClass('off');
	window.setTimeout(function () {
		//$('#preloader').remove();
		$('#preloader').css({'z-index': -1});
	}, 1500);

}


function refreshSelector(selector, vehicleType, value) {
	console.log('refreshSelector');
	//console.log(selector);
	//console.log(vehicleType);
	//console.log(value);

	var linea_value, modelo_value, plan_value, cuotas_value = null;
	var el = $("#" + vehicleType + "-" + selector);
	var target = el.data('target');
	var targetElement = $("#" + vehicleType + "-" + target);
	var selectedValue = value;
	var selectors = el.parents('.thumbnail').find('select');
	var selectorsLength = selectors.length;
	var selectorIndex = selectors.index(el);
	var params = {};

	//console.log("#" + vehicleType + "-" + selector);
	//console.log(el);
	//console.log(selectorsLength);
	//console.log(selectorIndex);

	for(var s = 0; s < selectorsLength; s++){
		if(s > selectorIndex){
			resetSelector(selectors.eq(s));
		}
	}

	linea_value = el.parents('.thumbnail').find('#' + vehicleType + '-linea').val();
	modelo_value = el.parents('.thumbnail').find('#' + vehicleType + '-modelo').val();
	plan_value = el.parents('.thumbnail').find('#' + vehicleType + '-plan').val();
	cuotas_value = el.parents('.thumbnail').find('#' + vehicleType + '-cuotas').val();

	// Obtengo los datos del array
	params = {
		vehicle: vehicleType,
		selector: selector,
		value: selectedValue,
		target: target,
		linea: linea_value,
		modelo: modelo_value,
		plan: plan_value,
		cuotas: cuotas_value
	};

	//console.log(params);

	var selectorOptions = getSelectorData(params);

	fillSelector({el: targetElement, options: selectorOptions});
}

function getSelectorData(data) {
	console.log('getSelectorData');
	console.log(data);

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
	var linea = data.linea || '';

	console.log('vehicleType: ' + vehicleType);
	console.log('linea: ' + linea);

	var modelo = data.modelo || '';
	var plan = data.plan || '';
	var cuotas = data.cuotas || '';
	var aMain = appvehiculos[vehicleType][(linea - 1)];
	var aMatrix = [];
	var aReturnData = [];
	var aElements = [];


	if(aMain === undefined){
		console.log('aMain is not defined');
		return;
	}

	if(aMain[linea] !== undefined){

		aMatrix = loadMatrix(aMain[linea], aKeys);

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
						//console.log(el.modelo);
						//console.log($('#' + vehicleType + '-' + selector).val());
						//console.log(selectedValue);
						//console.log(el[target]);
						aElements.push(el[target]);
					}
				}

				if( prop == target && selectedValue == linea ){
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
	console.log('filterVehicleArrayFromURL');

	var vehicleType = unescape(getParameters("vehicletype"));
	var linea = unescape(getParameters("linea"));
	var modelo = unescape(getParameters("modelo"));
	var plan = unescape(getParameters("plan"));
	var cuotas = unescape(getParameters("cuotas"));

	//console.log(appvehiculos);
	//console.log(vehicleType);

	var aMain = appvehiculos[vehicleType][(linea - 1)];
	var aMatrix = [];
	var aCurrentRecord = [];

	if(aMain === undefined){
		console.log('aMain is not defined');
		return;
	}

	if(aMain[linea] === undefined){
		console.log('aMain[linea] is not defined');
		return;
	}

	aMatrix = loadMatrix(aMain[linea], aKeys);

	for(var i = 0; i < Object.keys(aMatrix).length; i++){
		var el = aMatrix[i];

		for (var prop in el) {

			// skip loop if the property is from prototype
			if(!el.hasOwnProperty(prop)){ continue;}

			//console.log('vehicleType: ' + el[vehicleType] + ' == ' + unescape(getParameters("vehicletype")) + ', modelo: ' + el[modelo] + ' == ' + el[modelo] + ', plan: ' + el[plan] + ' == '+ plan + ', cuotas: ' + el[cuotas] + '== ' + cuotas);

			if(el.modelo == modelo && el.plan == plan && el.cuotas == cuotas){
				aCurrentRecord.push(el);
				break;
			}

		}
	}

	console.log('aCurrentRecord: filtro y devuelvo el registro desde la coleccion de vehiculos');
	console.log(aCurrentRecord);

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

			// Vacio los registros offlinea
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
		console.log('sendRecord: Fail: ');
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
	var linea = $('#' + vehicleType + '-linea').val();
	var linea_nombre = $('#' + vehicleType + '-linea').val();
	var modelo = $('#' + vehicleType + '-modelo').val();
	var plan = $('#' + vehicleType + '-plan').val();
	var cuotas = $('#' + vehicleType + '-cuotas').val();
	var aMain = appvehiculos[vehicleType][(linea - 1)];

	console.log(el);
	console.log(vehicleType);
	console.log(linea);
	console.log(aMain);

	//return;

	// Armo la URL del Cotizador
	var urlParameter = '';

	if (linea !== '' && modelo !== '' && plan !== '' && cuotas !== '') {
		urlParameter = 'vehicletype=' + escape(vehicleType) + '&linea=' + escape(linea) + '&modelo=' + escape(modelo) + '&plan=' + escape(plan) + '&cuotas=' + escape(cuotas);

		window.location.href = "cotizacion.html?" + urlParameter;
	} else {
		alert('Faltan elegir opciones para la cotización');
		return;
	}
}

function showQuoteData(mode) {

	var aCurrentRecord = filterVehicleArrayFromURL();

	console.log(aCurrentRecord);

	var carga_admin_suscripcion = aCurrentRecord[0]["carga_admin_suscripcion"].replace('\.', '').replace('\,', '.');
	var iva_21 = aCurrentRecord[0]["iva_21"].replace('\.', '').replace('\,', '.');
	var gastos_adm_suscrip = parseFloat(carga_admin_suscripcion) + parseFloat(iva_21);
	var gastos_adm_suscrip = parseFloat(gastos_adm_suscrip).toLocaleString('de-DE');

	// Cargo el resultado del Cotizador
	$("#" + mode + "_cant_cuotas").html(getParameters("cuotas"));
	$("#" + mode + "_total_cuota_mensual").html('$' + aCurrentRecord[0]["cuota_mensual"]);
	$("#" + mode + "_precio_vehiculo_iva").html(aCurrentRecord[0]["precio_publico"]);
	$("#" + mode + "_tipo_plan").html(aCurrentRecord[0]["plan"]);
	$("#" + mode + "_cuota_pura").html(aCurrentRecord[0]["cuota_pura"]);
	$("#" + mode + "_gastos_adm_suscrip").html(gastos_adm_suscrip);
	$("#" + mode + "_gastos_adm_suscrip_iva").html(aCurrentRecord[0]["iva_21"]);
	$("#" + mode + "_alicuota").html((aCurrentRecord[0]["pago_adjudicacion_30"] || '0'));
	$("#" + mode + "_modelo_cotizador").html(aCurrentRecord[0]["modelo"]);
	$("#" + mode + "_nombre_plan_cotizador").html(aCurrentRecord[0]["plan"]);

	// Actualizacion de la imagen de la linea del vehiculo en la cotizacion
	//$("#" + mode + "_img_ppal").attr("src", "images/vehicles/" + getParameters('vehicletype') + "-linea" + getParameters('linea_nombre') + ".jpg");

	$("#" + mode + "_img_ppal").attr("src", appcategorias[getParameters('vehicletype')].imagen);

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

	// Para generar la cola de pensaje pendientes
	var aRecordTemp = [];

	//Si tengo los datos guardados localmente, los consulto directamente desde ahi
	var storedRecords = JSON.parse(window.localStorage.getItem("storedRecords"));

	// Si hay datos localmente
	if (storedRecords !== null) {

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

String.prototype.replaceAll = function (find, replace) {
	 var str = this;
	 return str.replace(new RegExp(find.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g'), replace);
};

Object.size = function(obj) {
	 var size = 0, key;
	 for (key in obj) {
		  if (obj.hasOwnProperty(key)) {size++;}
	 }
	 return size;
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


