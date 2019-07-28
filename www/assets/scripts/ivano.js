$(window).on('load', function () {


	 //Verifico si existe la URL de acceso al BackOffice

	 // Si hay datos localmente
	 //if (window.localStorage.getItem("server_url") != null) {
	 //	server_url = window.localStorage.getItem("server_url");
	 //}

	 app.initialize();

});

$(window).on('scroll', function (e) {

});

jQuery(document).ready(function () {
	 console.log('ready start');

	 $.support.cors = true;
	 //$.mobile.allowCrossDomainPages = true;

	 if ($('.home').length) {
		  isHome = true;
	 }

	 getMainData();

	 updateStoredLeadsCounter();

	 console.log('ready end');
});


$(document).on('click', '.caption .budget > a', function (e) {

	 e.preventDefault();

	 makeQuote({e: e});

});


// SET SERVER URL  ============================================================
$(document).on('click', '#change-server-url', function (e) {

	 e.preventDefault();

	 var current_server_url = window.localStorage.getItem("server_url");
	 var settings_server_url = $("#settings-server-url").val();
	 var msg = 'URL de servidor actualizada';
	 var statusClass = 'text-success';

	 //console.log(settings_server_url + ' == ' + current_server_url);

	 if (settings_server_url !== '' && settings_server_url !== undefined && urlCheck(settings_server_url)) {

		  if (settings_server_url == current_server_url) {
				return;
		  }

		  if(urlCheck(settings_server_url)){

				// Guardo los datos
				window.localStorage.setItem("server_url", settings_server_url);
				server_url = settings_server_url;

		  }else{
				msg = 'Ingresar una URL válida';
				statusClass = 'bg-danger';

		  }

	 } else {
		  msg = 'Error. Intentar nuevamente.';
		  statusClass = 'bg-danger';
	 }

	 showCpanelStatus({msg: msg, statusClass: statusClass});

});

// SET DATA URL  ==============================================================
$(document).on('click', '#change-data-url', function (e) {

	 e.preventDefault();

	 var current_data_url = window.localStorage.getItem("data_url");
	 var settings_data_url = $("#settings-data-url").val();
	 var msg = 'URL de datos actualizada';
	 var statusClass = 'bg-success';

	 //console.log(settings_data_url + ' == ' + current_data_url);

	 if (settings_data_url !== '' && settings_data_url !== undefined) {

		  if (settings_data_url == current_data_url) {
				return;
		  }

		  // Guardo los datos
		  window.localStorage.setItem("data_url", settings_data_url);
		  data_url = settings_data_url;

	 } else {
		  msg = 'Error. Intentar nuevamente.';
		  statusClass = 'bg-danger';
	 }

	 showCpanelStatus({msg: msg, statusClass: statusClass});

});

// SINCRONIZAR CONTACTOS ======================================================
$('#btn-pending-records').one('click', function (e) {
	 //console.log('click 1');
	 e.preventDefault();
	 sendToServer({e: e, el: $(this)});
});

// ACTUALIZAR CONTENIDO =======================================================
$(document).on('click', '#btn-update-content', function (e) {

	 e.preventDefault();

	 // Si no estoy conectado a Internet, cancelo
	 if (app.isConnected) {

		  showConfirm({
				body: 'Esta acción descargará y reemplazará todo el contenido de la aplicación. El proceso podría demorar unos minutos. ¿Desea continuar?',
				action: 'show',
				fn: function () {

					 if (window.localStorage.getItem("appJsonData")) {

						  // Hago backup del contenido actual;
						  appCurrentMainData = window.localStorage.getItem("appJsonData");

						  window.localStorage.removeItem("appJsonData");
					 }

					 getMainData({
						  btn: $('#btn-update-content'),
						  cb: function(response){
								var dataBody = 'Los contenidos se acutalizaron correctamente';
								var dataClass = 'success';
								var dataIcon = 'check';

								if(response == null || response == '' || response === undefined){
									 dataBody = 'No se pudo acceder a los contenidos. Intentar más tarde';
									 dataClass = 'danger';
									 dataIcon = 'exclamation';
								}

								showAlert({
									 body: dataBody,
									 action: 'show',
									 class: dataClass,
									 icon: dataIcon,
								});

								$('footer').removeClass('on');
						  }
					 });
				}
		  });

	 } else {
		  showAlert({
				body: 'Esta acción requiere conexión a internet.',
				class: 'danger',
				icon: 'exclamation',
				action: 'show'
		  });

		  return false;
	 }

});

// PREPARAR_COTIZACION ========================================================
$(document).on('click', '#btn-prepare-quote', function (e) {

	 e.preventDefault();

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

		  showAlert({
				body: 'Faltan elegir opciones para completar la cotización',
				class: 'danger',
				icon: 'exclamation',
				action: 'show'
		  });

		  return;
	 }

});

// ENVIAR_COTIZACION ==========================================================
$(document).on('click', '#btn-send-quote', function (e) {

	 e.preventDefault();

	 var isValid = false;
	 var aCurrentRecord = [];
	 var btn = $(e.currentTarget);
	 var f = btn.parents('form');

	 isValid = validator({
		  'el': btn,
		  'form': f,
		  'useBootstrapError': true, //useBootstrapError,
		  'useBootstrapDialog': false, //useBootstrapDialog,
		  'debug': false
	 });

	 if (!isValid) {
		  showAlert({
				body: 'Por favor, completar todos los campos',
				class: 'danger',
				icon: 'exclamation',
				action: 'show'
		  });

		  return false;
	 }

	 // Busco el registro actual desde la url
	 aCurrentRecord = filterVehicleArrayFromURL();

	 // Agrego los campos del formularo al registro
	 $('#form-quote').find(':input').not(':button').each(function (i, o) {
		  var attr = $(o).attr('name');

		  //console.log('#btn-send-quote -> Agrego input name: ' + $(o).attr('name') + ' - valor: ' + $(o).val());

		  aCurrentRecord[0][attr] = $(o).val();
	 });

	 // Cargo Loading...
	 $("#btn-send-quote").html("Enviando...").attr('disabled', 'disabled');

	 //console.log('#btn-send-quote -> aCurrentRecord reemplazo imagen base64 por nombre de archivo');

	 // Asigno el valor de imagen_lector (el archivo de la imagen) al campo imagen,
	 // sobrescribiendo el valor base64 y elimino el campo imagen_lector.
	 aCurrentRecord[0].imagen = aCurrentRecord[0].imagen_lector;
	 delete aCurrentRecord[0].imagen_lector;

	 //console.log(aCurrentRecord);

	 // Antes de enviar guardo el registro actual
	 storeLeadsLocal(aCurrentRecord);

	 // Si estoy conectado a Internet hago el envío
	 //if (app.isConnected) {

	 //sendRecord();

	 //}

	 $("#btn-send-quote").html("Enviar cotización").removeAttr('disabled');

});

// MUESTRO LA URL DEL SERVER ==================================================
$('#cpanel').on('show.bs.modal', function () {
	 $('#settings-server-url').val(server_url);
	 $('#settings-data-url').val(data_url);
});

// MUESTRO LLA TABLA DE CONTACTOS =============================================
$(document).on('click', '.leads-contacts-trigger', function(e){
	e.preventDefault();

	showleads();
});

// DETERMINO SI HAY O NO HAY DIALOGOS ABIERTOS ================================
$(document).on('show.bs.modal', function () {
	if($('.leads-holder').hasClass('on') ){
		$('.leads-holder').removeClass('on');
	}
});

$(document).on('hidden.bs.modal', function () {

	if($('.leads-holder').hasClass('on') ){
		$('.leads-holder').removeClass('on');
	}

	 if ($('.modal:visible').length > 0) {
		  // restore the modal-open class to the body element, so that scrolling works
		  // properly after de-stacking a modal.
		  setTimeout(function () {
				$(document.body).addClass('modal-open');
		  }, 0);
	 }
});

//// VEHICLES SELECTORS LISTENER ==============================================
// Paso a setVehiclePanels


// VARIABLES
var currentDate = new Date();
var server_url = ""; //"http://mercedesappcms.nmd";
var data_url = ""; //"/json/json_data/";
var json_data_url = '';
var loadingData = false;
var currentAjaxProcess = '';
var isHome = false;
var appMainData;
var appCurrentMainData;
var appvehiculos = {};
var appcontenido = {};
var appcategorias = {};
var appcampos = [];
var contactTimer;


// FUNCTIONS

function getMainData(data) {
	 console.log("getMainData start");

	 var d = data || {};
	 var mainDataRequest;
	 var btn = $(d.btn) || '';
	 var cb = d.cb || function () {
		  console.log('getMainData: empty callback')
	 };

	 //console.log(data);
	 //console.log(btn);

	 if (localStorage.getItem('appJsonData')) {
		  appMainData = JSON.parse(window.localStorage.getItem('appJsonData'));

		  console.log('getMainData from localstorage');
		  //console.log(appMainData);

		  setApp();

	 } else {

		  console.log('getMainData from server');

		  if (!json_data_url) {

				if (window.localStorage.getItem('server_url') && window.localStorage.getItem('data_url')) {
					 server_url = window.localStorage.getItem('server_url');
					 data_url = window.localStorage.getItem('data_url');
					 json_data_url = server_url + data_url + "?date=" + currentDate.ivTimeStamp();
				}

				$('footer').addClass('on');
		  }

		  if(!ajaxReadyCheck('getMainData')){
				return false;
		  }
		  loadingData = true;

		  btn.addClass('loading').attr('disabled');

		  mainDataRequest = $.getJSON(json_data_url, function (response, textStatus, jqXHR) {
				console.log("getMainData done");
				//console.log( response );
				//console.log('getMainData: ' + textStatus);

				loadingData = false;

				if (textStatus == 'success') {
					 appMainData = response;
					 window.localStorage.setItem("appJsonData", JSON.stringify(appMainData));
					 appCurrentMainData = '';

					 setApp();

				}

				cb(response);

		  })
		  .fail(function () {
				console.log("getMainData fail");

				//Fallo la actualizacion, vuelvo a guardar la data backapeada
				if (appCurrentMainData) {
					 appMainData = appCurrentMainData;
					 window.localStorage.setItem("appJsonData", JSON.stringify(appCurrentMainData));
					 appCurrentMainData = '';
				}

				loadingData = false;

		  })
		  .always(function () {
				console.log("getMainData always");

				btn.removeAttr('disabled').removeClass('loading');

				console.log("getMainData end");
		  });
	 }
}

function setApp() {
	 console.log('setApp: start ');

	 var vehiculos = appMainData.vehiculos;

	 appcategorias = appMainData.categorias;
	 appcontenido = appMainData.contenido;
	 appcampos = appMainData.campos;

	 if (vehiculos.length > 0) {

		  $(vehiculos).each(function (i, o) {
				var k = Object.keys(o);

				appvehiculos[k] = o[k];
		  });

		  //console.log(vehiculos.length); ;
		  //console.log(vehiculos); ;
		  //console.log(appvehiculos);
		  //console.log(appcontenido);
		  //console.log(appcategorias);

		  setContent();

		  if (isHome) {

				setVehiclePanels();

		  } else {
				// Analizo los parametros del Cotizador
				if (window.location.pathname.search("/cotizacion.html") != -1) {

					 showQuoteData('cotizador');

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

	 } else {
		  console.log('No vehicles data');
	 }

	 if (appcontenido.general_server_url !== undefined) {
		  server_url = appcontenido.general_server_url;
	 }
	 if (appcontenido.general_data_url !== undefined) {
		  data_url = appcontenido.general_data_url;
	 }

	 $('footer').removeClass('on');

	 console.log('setApp: end ');

}

function setVehiclePanels() {

	 if (!$('body.home').length) {
		  return;
	 }

	 console.log('setVehiclePanels: start ');

	 var templatePanel = vehicle_panel_template;
	 var templateOption = '<option value="{opcion_valor}">{opcion_nombre}</option>';
	 var panels = $('#panels').find('.row');

	 //console.log(Object.size(appcategorias));

	 panels.empty();

	 for (var cat in appcategorias) {

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

		  $(vehicleLines).each(function (j, e) {
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

	 panels.find('.selectpicker').selectpicker("refresh");

	 // VEHICLES SELECTORS ======================================================
	 $('.selector').on('changed.bs.select', function (e) {
		  refreshSelector($(this).data('selector'), $(this).data('vehicletype'), $(this).val());
	 });

	 console.log('setVehiclePanels: end ');

}

function setContent() {
	 console.log('setContent: start ');
	 var elements = $('.content-add');

	 for (var key in appcontenido) {
		  //console.log(key, jsonData[key]);
		  var el = $('#' + key);

		  if (el.length) {
				var htmltag = el.get(0).nodeName.toLowerCase();

				if (key == el.attr('id')) {

					 switch (htmltag) {
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
		  $('#preloader').css({
				'z-index': -1
		  })
		  $('html').removeClass('locked');
		  console.log('setContent: end ');
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

	 for (var s = 0; s < selectorsLength; s++) {
		  if (s > selectorIndex) {
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

	 fillSelector({
		  el: targetElement,
		  options: selectorOptions
	 });
}

function getSelectorData(data) {

	 console.log('getSelectorData');
	 //console.log(data);

	 if (data.vehicle === undefined) {
		  console.log('ERROR: getSelectorData: No vehicle type');
		  return;
	 }

	 if (data.value === undefined) {
		  console.log('ERROR: getSelectorData: No value selected');
		  return;
	 }

	 var vehicleType = data.vehicle;
	 var selectedValue = data.value;
	 var selector = data.selector;
	 var target = data.target || '';
	 var linea = data.linea || '';

	 //console.log('vehicleType: ' + vehicleType);
	 //console.log('linea: ' + linea);

	 var modelo = data.modelo || '';
	 var plan = data.plan || '';
	 var cuotas = data.cuotas || '';
	 var aMain = appvehiculos[vehicleType][(linea - 1)];
	 var aMatrix = [];
	 var aReturnData = [];
	 var aElements = [];


	 if (aMain === undefined) {
		  console.log('aMain is not defined');
		  return;
	 }

	 if (aMain[linea] !== undefined) {

		  aMatrix = loadMatrix(aMain[linea], appcampos);

		  for (var i = 0; i < Object.keys(aMatrix).length; i++) {
				var el = aMatrix[i];

				for (var prop in el) {

					 // skip loop if the property is from prototype
					 if (!el.hasOwnProperty(prop)) continue;

					 //console.log('selectedvalue: ' + selectedValue + ' | value: ' + el[prop] + ' | prop: ' + prop + ' | target: ' + target + ' | Selector: ' + selector);

					 if (selectedValue != 1 && (el[selector] == selectedValue)) {
						  if (el[target] !== undefined) {
								//console.log('coincide');
								//console.log(el);
								//console.log(el.modelo);
								//console.log($('#' + vehicleType + '-' + selector).val());
								//console.log(selectedValue);
								//console.log(el[target]);
								aElements.push(el[target]);
						  }
					 }

					 if (prop == target && selectedValue == linea) {
						  aElements.push(el[prop]);
					 }
				}
		  }

		  return aElements;
	 }
}

function fillSelector(data) {

	 //console.log(data);

	 var el = data.el;
	 var options = data.options;
	 var aUniqueValues = [];
	 var selectorOption = '';

	 $.each(options, function (i, option) {

		  if ($.inArray(option, aUniqueValues) === -1) {
				aUniqueValues.push(option);

				selectorOption = '<option value="' + option + '" >' + option + '</option>';

				el.append(selectorOption);
		  }

	 });

	 el.selectpicker("refresh");
}

function resetSelector(el) {

	 el.empty().append('<option value="" selected="">' + el.data('default') + '</option>');
	 el.selectpicker("refresh");

}


function loadMatrix(matrix, appcampos) {

	 console.log('loadMatrix: start');

	 //console.log(appcampos);
	 //console.log(matrix[0]);

	 //return;

	 var data = {};

	 for (var i = 0; i < matrix.length; i++) {
		  var filavalores = matrix[i];
		  var row = {};

		  //console.log('Filavalores #' + i);
		  //console.log(filavalores);
		  //console.log(appcampos);
		  //console.log('Recorro los campos: filavalores #' + i);
		  //console.log('filavalores #' + i + ' | campos de filavalores: ' + filavalores.length + ' - campos totales: ' + appcampos.length);

		  $(filavalores).each(function (j, val) {
				//console.log(j + ': ' + appcampos[j] + ': ' + val);
				row[appcampos[j]] = val;

		  });

		  data[i] = row;
	 }
	 //console.log('loadMatrix');
	 //console.log(data);
	 console.log('loadMatrix: end');

	 return data;

}

function filterVehicleArrayFromURL() {

	 console.log('filterVehicleArrayFromURL: start');

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

	 if (aMain === undefined) {
		  console.log('aMain is not defined');
		  return;
	 }

	 if (aMain[linea] === undefined) {
		  console.log('aMain[linea] is not defined');
		  return;
	 }

	 aMatrix = loadMatrix(aMain[linea], appcampos);

	 for (var i = 0; i < Object.keys(aMatrix).length; i++) {

		  var el = aMatrix[i];

		  for (var prop in el) {

				// skip loop if the property is from prototype
				if (!el.hasOwnProperty(prop)) {
					 continue;
				}

				//console.log('vehicleType: ' + el[vehicleType] + ' == ' + unescape(getParameters("vehicletype")) + ', modelo: ' + el[modelo] + ' == ' + el[modelo] + ', plan: ' + el[plan] + ' == '+ plan + ', cuotas: ' + el[cuotas] + '== ' + cuotas);

				if (el.modelo == modelo && el.plan == plan && el.cuotas == cuotas) {
					 aCurrentRecord.push(el);
					 break;
				}

		  }
	 }

	 console.log('aCurrentRecord: filtro y devuelvo el registro desde la coleccion de vehiculos');
	 console.log(aCurrentRecord);
	 console.log('filterVehicleArrayFromURL: end');

	 return aCurrentRecord;

}


function roundNumber(num, pos){
	 var tmp = [];
	 var dif = 0;
	 var r = '';
	 var pos = pos === undefined ? appcontenido.general_decimales : pos;
	 var n = num;
	 var multiplier = Math.pow(10, pos);

	 n = n.replace(/\./g, '').replace('\,', '.');
	 n = parseFloat(n);

	 r = (Math.round(n * multiplier) / multiplier);
	 r = r.toLocaleString('de-DE');

	 tmp = r.toString().split(',');

	 //console.log(r);
	 //console.log(tmp[tmp.length - 1].length);

	 if(tmp[tmp.length - 1].length < pos && tmp[0] > 0){
		  dif = pos - tmp[tmp.length - 1];

		  $(dif).each(function(i, o){
				tmp[tmp.length - 1] += '0';
		  });

		  r = tmp.join(',');
	 }

	 console.log('roundNumber: original: ' + num + ': parseFloat: ' + n + ' = Devolucion: ' + r);
	 return r;
}

function makeQuote(data) {

	 console.log('makeQuote: start');

	 var el = $(data.e.currentTarget);
	 var vehicleType = el.data('vehicle-type');
	 var linea = $('#' + vehicleType + '-linea').val();
	 var linea_nombre = $('#' + vehicleType + '-linea').val();
	 var modelo = $('#' + vehicleType + '-modelo').val();
	 var plan = $('#' + vehicleType + '-plan').val();
	 var cuotas = $('#' + vehicleType + '-cuotas').val();
	 var aMain = appvehiculos[vehicleType][(linea - 1)];

	 //console.log(el);
	 //console.log(vehicleType);
	 //console.log(linea);
	 //console.log(aMain);

	 // Armo la URL del Cotizador
	 var urlParameter = '';

	 if (linea !== '' && modelo !== '' && plan !== '' && cuotas !== '') {
		  urlParameter = 'vehicletype=' + escape(vehicleType) + '&linea=' + escape(linea) + '&modelo=' + escape(modelo) + '&plan=' + escape(plan) + '&cuotas=' + escape(cuotas);

		  window.location.href = "cotizacion.html?" + urlParameter;
	 } else {

		  showAlert({
				body: 'Faltan elegir opciones para completar la cotización',
				class: 'danger',
				icon: 'exclamation',
				action: 'show'
		  });

		  return;
	 }
}

function showQuoteData(mode) {
	console.log('showQuoteData: start');

	var pos = appcontenido.general_decimales;
	var aCurrentRecord = filterVehicleArrayFromURL();

	var carga_admin_suscripcion = parseFloat( aCurrentRecord[0]["carga_admin_suscripcion"] );
	var iva_21 = parseFloat( aCurrentRecord[0]["iva_21"] );
	var gastos_adm_suscrip = parseFloat(carga_admin_suscripcion) + parseFloat(iva_21);

	// Cargo el resultado del Cotizador
	$("#" + mode + "_cant_cuotas").html(getParameters("cuotas"));
	$("#" + mode + "_total_cuota_mensual").html('$' + parseFloat(aCurrentRecord[0]["cuota_mensual"]).ivFormatMoney(pos, ',', '.') );
	$("#" + mode + "_precio_vehiculo_iva").html( parseFloat( aCurrentRecord[0]["precio_publico"]).ivFormatMoney(pos, ',', '.') );
	$("#" + mode + "_tipo_plan").html(aCurrentRecord[0]["plan"]);
	$("#" + mode + "_cuota_pura").html(parseFloat( aCurrentRecord[0]["cuota_pura"] ).ivFormatMoney(pos, ',', '.') );
	$("#" + mode + "_gastos_adm_suscrip").html( gastos_adm_suscrip.ivFormatMoney(pos, ',', '.') );
	$("#" + mode + "_gastos_adm_suscrip_iva").html(parseFloat( aCurrentRecord[0]["iva_21"]).ivFormatMoney(pos, ',', '.') );
	$("#" + mode + "_alicuota").html(( parseFloat( aCurrentRecord[0]["pago_adjudicacion_30"] ).ivFormatMoney(pos, ',', '.') || '0'));
	$("#" + mode + "_modelo_cotizador").html(aCurrentRecord[0]["modelo"]);
	$("#" + mode + "_nombre_plan_cotizador").html(aCurrentRecord[0]["plan"]);

	//$("#" + mode + "_cant_cuotas").html(getParameters("cuotas"));
	//$("#" + mode + "_total_cuota_mensual").html('$' + roundNumber( aCurrentRecord[0]["cuota_mensual"]) );
	//$("#" + mode + "_precio_vehiculo_iva").html( roundNumber( aCurrentRecord[0]["precio_publico"]) );
	//$("#" + mode + "_tipo_plan").html(aCurrentRecord[0]["plan"]);
	//$("#" + mode + "_cuota_pura").html(roundNumber( aCurrentRecord[0]["cuota_pura"] ) );
	//$("#" + mode + "_gastos_adm_suscrip").html( gastos_adm_suscrip.toLocaleString('de-DE') );
	//$("#" + mode + "_gastos_adm_suscrip_iva").html(roundNumber( aCurrentRecord[0]["iva_21"]) );
	//$("#" + mode + "_alicuota").html(( roundNumber( aCurrentRecord[0]["pago_adjudicacion_30"] ) || '0'));
	//$("#" + mode + "_modelo_cotizador").html(aCurrentRecord[0]["modelo"]);
	//$("#" + mode + "_nombre_plan_cotizador").html(aCurrentRecord[0]["plan"]);
	//
	$("#" + mode + "_img_ppal").attr("src", aCurrentRecord[0]["imagen"]);

	console.log('showQuoteData: end');
}

function updateStoredLeadsCounter() {

	 var counter = 0;

	 //Si tengo los datos guardados localmente, los consulto directamente desde ahi
	 var stored_leads = JSON.parse(window.localStorage.getItem("stored_leads"));

	 // Si hay datos localmente
	 if (stored_leads != null) {
		  counter = stored_leads.length;
	 }

	 // ACtualizo el Bubble Count de Contactos Pendientes
	 $('#pending-records-total').html(counter);
}

function storeLeadsLocal(lead) {
	 console.log('storeLeadsLocal: start');

	window.clearTimeout(contactTimer);

	 var counter = $('#pending-records-total');
	 var aStoredLeads = [];

	 //Si tengo datos guardados localmente, los consulto directamente desde ahi
	 var stored_leads = JSON.parse(window.localStorage.getItem("stored_leads"));

	 // Si hay datos localmente
	 if (stored_leads !== null) {
		  // Obtengo los datos de los registros previos para no perderlos
		  aStoredLeads = stored_leads;
	 }

	 // Agrego el contacto actual
	 aStoredLeads.push(lead);

	 // Guardo los datos
	 window.localStorage.setItem("stored_leads", JSON.stringify(aStoredLeads));
	 console.log('storeLeadsLocal: Guardo el registro localmente');

	 // Actualizo Contador
	 updateStoredLeadsCounter();

	 counter.addClass('new');
	 contactTimer = window.setTimeout(function () {
		  counter.removeClass('new');
	 }, 3000);

	 // Inicializo el Formulario
	 $('#form-quote').trigger("reset");
	 $("#provincia").val('default');
	 $("#provincia").selectpicker("refresh");

	 console.log('storeLeadsLocal: end');
}


function sendToServer(data) {

	 console.log('sendToServer: start');

	 var e = data.e;
	 var el = data.el;
	 var counter = 0;

	 e.preventDefault();
	 e.stopPropagation();
	 e.stopImmediatePropagation();

	 //Si tengo datos guardados localmente, los consulto directamente desde ahi
	 var stored_leads = JSON.parse(window.localStorage.getItem("stored_leads"));

	 // Si estoy conectado a Internet y hay registros, envio
	 if (app.isConnected && stored_leads !== null && stored_leads.length > 0) {

		  counter = stored_leads.length;

		  // Muestro confirmación para el envío de los contactos al servidor
		  showConfirm({
				body: 'Exportar ' + counter +' ' + (counter > 1 ? 'registros' : 'registro') + ' al servidor.<br>Esta acción podría demorar unos minutos.<br>¿Desea continuar?',
				action: 'show',
				fn: function () {

					 sendRecord(el);

				}
		  });

		  // Si cancelo el envío, vuelvo a "bindear" el click del boton, sino, quedaría sin evento de click
		  el.off().one('click', function(e){
				//console.log('click 4');
				e.preventDefault();
				sendToServer( {"e": e, "el": $(this)} );
		  });


	 } else {

		  el.off().one('click', function(e){
				//console.log('click 3');
				e.preventDefault();
				sendToServer( {"e": e, "el": $(this)} );
		  });

		  if(!app.isConnected){

				showAlert({
					 body: 'Esta acción requiere conexión a internet.',
					 class: 'danger',
					 icon: 'exclamation',
					 action: 'show'
				});

		  }

		  if(stored_leads === null || stored_leads.length < 1){
				showAlert({
					 body: 'No hay registros para exportar.',
					 class: 'danger',
					 icon: 'exclamation',
					 action: 'show'
				});

		  }

	 }

}

function sendRecord(btn) {

	 console.log('sendRecord start');

	 //Levanto todos los registros guardados y los mando via post al server
	 var stored_leads = JSON.parse(window.localStorage.getItem("stored_leads"));

	 //console.log(stored_leads);

	 if(!ajaxReadyCheck('sendRecord')){
		  return false;
	 }
	 loadingData = true;


	 btn.addClass('loading').attr('disabled', 'disabled');

	 //console.log(btn);

	 $.ajax({
		  url: server_url + '/system/php/actions.php?action=store&time=' + currentDate.ivTimeStamp(),
		  async: false,
		  type: 'POST',
		  data: {'stored_leads': stored_leads},
		  dataType: 'json',
		  charset: 'UTF-8',
		  timeout: 10000
	 })
	 .done(function (response, status, xhr) {

		  loadingData = false;

		  // Si NO hubo respuesta del servidor
		  if(response === null || response === undefined){
				console.log(status);
		  }

		  // si HAY respuesta del servidor
		  if(response != null && response != ''){

				// Si no hubo error
				if (response.status) {

					 // Vacio los registros locales
					 window.localStorage.removeItem("stored_leads");

					 //Inicializo el Formulario
					 $('#form-quote').trigger("reset");
					 $("#provincia").val('default');
					 $("#provincia").selectpicker("refresh");

				} // Si HUBO error y vienen registros devueltos
				else if (!response.status && response.failed_records.length) {

					 // Vacio los registros locales
					 window.localStorage.removeItem("stored_leads");

					 // Guardo los registros devueltos
					 window.localStorage.setItem("stored_leads", JSON.stringify(response.failed_records));
				}

				// Actualizo el contador
				updateStoredLeadsCounter();

				window.setTimeout(function () {
					 showAlert({
						  body: response.msg,
						  class: 'success',
						  icon: 'check',
						  action: 'show'
					 });
					 console.log(response.msg);
				}, 1000);

		  }

	 })
	 .fail(function (jqXHR, status, errorThrown) {
		  console.log('sendRecord: Fail: ');
		  console.log(jqXHR);
		  console.log(status);
		  console.log(errorThrown);

		  loadingData = false;

		  //console.log('Fallo sendRecord(): lead: (ver siguiente)');
		  //console.log(lead);

	 })
	 .always(function (response, status, xhr) {
		  console.log('sendRecord always');

		  //$('#btn-pending-records').removeClass('loading').removeAttr('disabled');
		  btn.removeClass('loading').removeAttr('disabled');

		  btn.off().one('click', function(e){
		  //$('#btn-pending-records').off().one('click', function(e){
				//console.log('click 2');
				sendToServer( {"e": e, "el": $(this)} );
		  });

	 });

}

function showleads(){

	var leadsHolder = $('.leads-holder');
	var table = $('#leads-list').html('');
	var thead = $('<thead>').appendTo(table);
	var tbody = $('<tbody>');
	var data, headers = '';

	if(leadsHolder.hasClass('on')){
		leadsHolder.removeClass('on');
		return;
	}

	var leads = JSON.parse(window.localStorage.getItem("stored_leads"));
	var wanted_fields = ['nombre', 'apellido', 'email', 'telefono', 'ciudad', 'codigo_postal', 'provincia', 'categoria', 'producto', 'modelo', 'plan'];

	if(leads.length < 1){
		showAlert({
			 body: 'En este momento no hay contactos guardados',
			 action: 'show',
			 class: 'info',
			 icon: 'info',
		});

		return;
	}

	for (var i in leads) {

		var row = leads[i];

		data += '<tr>';

		//console.log(row);

		for (var n in row) {

			var cells = row[n];

			//console.log(cells);

			$(wanted_fields).each(function(p, th){

				if(i == 0){
					headers += '<th>' + th.replace('_', ' ').capitalize() + '</th>';
				}

				data += '<td>' + cells[th] +  '</td>';

			});

		}

		data += '</tr>';
	}

	thead.html(headers).appendTo(table);
	tbody.html(data).appendTo(table);

	var panelBody = leadsHolder.find('.panel-body');
	var panelHeading = leadsHolder.find('.panel-heading');
	var tableResponsive = leadsHolder.find('.table-responsive');

	var panelHeadingHeight = panelHeading.height() + parseInt(panelHeading.css('padding-top').replace('px', '')) + parseInt(panelHeading.css('padding-bottom').replace('px', ''));
	var panelBodyHeight = panelBody.height() + parseInt(panelBody.css('padding-top').replace('px', '')) + parseInt(panelBody.css('padding-bottom').replace('px', ''));

	var tableResponsiveHeight = parseInt( panelBodyHeight) + parseInt(panelHeadingHeight);

	//console.log(panelBodyHeight);
	//console.log(panelHeadingHeight);
	//console.log(tableResponsiveHeight);


	//tableResponsive.get(0).style.height = 'calc(100% - ' + tableResponsiveHeight +')';

	tableResponsive.attr('style','height: calc(100% - ' + tableResponsiveHeight + 'px)' );
	//tableResponsive.css('height', 'calc(100% - ' + tableResponsiveHeight + ')' );

	leadsHolder.addClass('on');

}

function getParameters(k) {
	 var p = {};
	 location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (s, k, v) {
		  p[k] = v
	 })
	 return k ? p[k] : p;
}

function urlCheck(url) {
	 var expression = /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/gi;
	 var regex = new RegExp(expression);

	 if (url.match(regex)) {
		  return true;
	 } else {
		  return false;
	 }
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


function showConfirm(data) {
	 var options = {
		  mode: 'confirm',
		  action: data.action || 'close',
		  title: data.title,
		  body: data.body,
		  btn1text: "Cancelar",
		  btn2text: "Aceptar",
		  fn: data.fn || function (e) {}
	 };

	 showAlert(options);
}

function showAlert(data) {
	 //console.log(data);
	 var el = data.el || $('#page-alert');
	 var alertMode = data.mode || 'alert';
	 var action = data.action || 'close';
	 var alertTitle = data.title || 'Atención';
	 var alertBody = data.body || '';
	 var alertClass = data.class || 'info';
	 var alertIcon = data.icon || 'info';
	 var alertButton1Text = data.btn1text || 'Cerrar';
	 var alertButton2Text = data.btn2text || '';
	 var btn2fn = data.fn || function (e) {};
	 var button1 = $('.alert-btn-1');
	 var button2 = $('.alert-btn-2');

	 if (!alertTitle) {
		  console.log('showAlert: No Title');
		  return;
	 }
	 if (!alertBody) {
		  console.log('showAlert: No body');
		  return;
	 }

	 //el.removeAttr('class');
	 //el.addClass('alert alert-dismissible fade show');

	 $('#modal-alert-title').html(alertTitle);
	 $('#alert-body').html(alertBody);
	 el.addClass(alertClass);

	 switch (alertClass) {
		  case 'danger':
				alertIcon = 'exclamation';
				break;
		  case 'success':
				alertIcon = 'check';
				break;
	 }

	 button1.html(alertButton1Text);
	 button2.addClass('hidden').html(alertButton2Text);

	 alertIcon = 'fa fa-' + alertIcon;
	 $('#alert-icon').removeAttr('class').addClass(alertIcon + ' ' + 'bg-' + alertClass);

	 button2.on('click', function (e) {

		  if (alertMode == 'confirm') {
				el.modal('hide');
				window.setTimeout(function () {
					 btn2fn(e);
				}, 1000);

				return;
		  }

	 });

	 if (alertMode == 'confirm') {
		  button2.removeClass('hidden').removeAttr('data-dismiss');
		  el.modal('hide');
	 }

	 if(action == 'show'){
		  el.modal('show');
	 }else if(action == 'close'){
		  el.modal('hide');
	 }

	 //el = '';
	 //el.modal('toggle');
}

function showCpanelStatus(data){
	 var statusClass = data.class === undefined ? 'bg-success' : data.class;
	 var msg = data.msg === undefined ? false : data.msg;
	 var status = $('#modal-status').removeAttr('class').text('');

	 if(!msg){
		  console.log('showCpanelStatus -> No hay mensaje para mostrar en status');
		  return;
	 }

	 status.addClass(statusClass).text(msg);

	 window.setTimeout(function () {
		  status.removeClass(statusClass).text('');
	 }, 4000);
}

function ajaxReadyCheck(request){
	 var r = true;

	 console.log('ajaxReadyCHeck -> loadingData value: ' + loadingData + ' (about to call ' + request + ')');

	 if(loadingData){
		  console.log('Can\'t perform the action ' + request + '. The erver is busy loading data' + (currentAjaxProcess ? ' for: ' + currentAjaxProcess : '.'));
		  r = false;
	 }
	 currentAjaxProcess = request;
	 return r;
}

function in_array(needle, haystack, argStrict) {
	 // Checks if the given value exists in the array
	 //
	 // version: 1003.2411
	 // discuss at: http://phpjs.org/functions/in_array    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	 // +   improved by: vlado houba
	 // +   input by: Billy
	 // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	 // *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);    // *     returns 1: true
	 // *     example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
	 // *     returns 2: false
	 // *     example 3: in_array(1, ['1', '2', '3']);
	 // *     returns 3: true    // *     example 3: in_array(1, ['1', '2', '3'], false);
	 // *     returns 3: true
	 // *     example 4: in_array(1, ['1', '2', '3'], true);
	 // *     returns 4: false
	 var key = '', strict = !!argStrict;
	 if (strict) {
		  for (key in haystack) {
				if (haystack[key] === needle) {
					 return true;
				}
		  }
	 } else {
		  for (key in haystack) {
				if (haystack[key] == needle) {
					 return true;
				}
		  }
	 }
	 return false;
}
