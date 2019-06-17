/*
<option value="1">Chasis Cabina</option>
<option value="2">Combi</option>
<option value="3">Furgón</option>
<option value="4">Pasajeros</option>
*/

var vehiclePanelTemplate = '<div class="col-sm-6">' +
'	<div class="thumbnail">' +
'		<h3>{categoria_nombre}</h3>' +
'		<figure>' +
'			<img src="{categoria_imagen}" >' +
'		</figure>' +
'		<div class="caption">' +

'			<div class="linea">' +
'				<select id="{categoria_url}-linea" class="selector selectpicker" data-selector="linea" data-vehicletype="{categoria_url}" data-default="Línea" data-target="modelo">' +
'					<option value="">Línea</option>' +
'					{linea_opciones}' +
'				</select>' +
'			</div>' +

'			<div class="modelo">' +
'				<select id="{categoria_url}-modelo" class="selector selectpicker" data-selector="modelo" data-vehicletype="{categoria_url}" data-default="Modelo" data-target="plan">' +
'					<option value="">Modelo</option>' +
'				</select>' +
'			</div>' +

'			<div class="plan">' +
'				<select id="{categoria_url}-plan" class="selector selectpicker" data-selector="plan" data-vehicletype="{categoria_url}" data-default="Plan" data-target="cuotas">' +
'					<option value="">Plan</option>' +
'				</select>' +
'			</div>' +

'			<div class="cuotas">' +
'				<select id="{categoria_url}-cuotas" class="selector selectpicker" data-selector="cuotas" data-vehicletype="{categoria_url}" data-default="Cuotas">' +
'					<option value="">Cuotas</option>' +
'				</select>' +
'			</div>' +

'			<div class="budget">' +
'				<a href="javascript:void(0)"  data-vehicle-type="{categoria_url}" class="btn btn-primary" role="button">Cotizar</a>' +
'			</div>' +

'		</div>' +
'	</div>' +
'</div>';
