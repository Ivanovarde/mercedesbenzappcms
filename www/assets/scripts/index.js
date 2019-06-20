/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
var app = {

	debug: true,
	isConnected: false,
	connectionTimer: null,
	imgUrl: 'https://ssl.gstatic.com/gb/images/v1_76783e20.png?',

	// Bind Event Listeners
	// Bind any events that are required on startup. Common events are:
	// 'load', 'deviceready', 'offline', and 'online'.
	bindEvents: function () {
		console.log('start: bindEvents');

		document.addEventListener('deviceready', this.onDeviceReady, false);

		// Detecto si estoy online o offline
		document.addEventListener("offline", app.checkConnection, false);
		document.addEventListener("online", app.checkConnection, false);
	},

	// Update DOM on a Received Event
	receivedEvent: function(id) {
		 console.log('Received Event: ' + id);
/*
		  var parentElement = document.getElementById(id);
		  var listeningElement = parentElement.querySelector('.listening');
		  var receivedElement = parentElement.querySelector('.received');

		  listeningElement.setAttribute('style', 'display:none;');
		  receivedElement.setAttribute('style', 'display:block;');


*/
	},

	// deviceready Event Handler
	// Bind any cordova events here. Common events are:
	// 'pause', 'resume', etc.
	onDeviceReady: function() {
		 this.receivedEvent('deviceready');

		app.initialize();
	},

	// Application Constructor
	initialize: function() {
		//document.addEventListener('deviceready', this.onDeviceReady.bind(this), false);

		// Detecto si estoy online u offline
		//document.addEventListener("offline", estoyConectado , false);
		//document.addEventListener("online", estoyConectado , false);

		app.checkConnection();

	},


	checkConnection: function(){

		if(app.debug){
			app.isConnected = true;
			return app.isConnected;
		}

		console.log('app > checkConnection: start');

		if(app.connectionTimer != null){
			window.clearTimeout(app.connectionTimer);
		}


		// Navigator connection is a apache cordova plugin
		if(navigator.connection !== undefined){

			try{
				var networkState = navigator.connection.type;
				var states = {};

				states[Connection.UNKNOWN]  = 'Unknown connection';
				states[Connection.ETHERNET] = 'Ethernet connection';
				states[Connection.WIFI]     = 'WiFi connection';
				states[Connection.CELL_2G]  = 'Cell 2G connection';
				states[Connection.CELL_3G]  = 'Cell 3G connection';
				states[Connection.CELL_4G]  = 'Cell 4G connection';
				states[Connection.NONE]     = 'No network connection';

				if(networkState == Connection.NONE){
					app.isConnected = false;
				}else{
					app.isConnected = true;
				}

				console.log('app > checkConnection: ' + states[networkState]);
			}
			catch(err) {

				app.isConnected = false;
				console.log('app > checkConnection: ' + err.message + '. - isConnected: ' + app.isConnected);

			}



		// Else block with native but not so reliable method navigator.online (check with caniuse.com)
		} else if (typeof navigator === "object" && typeof navigator.onLine === "boolean") {

			app.isConnected = navigator.onLine;
			console.log('checkConnection: Navigator Online');

		} else {
			//hack for older bizzare browsers
			var i = new Image();

			i.onerror = function () {
				console.log('checkConnection: Offline');

				app.showConnectionStatus();
				app.isConnected = false;

				return app.isConnected;
			}

			i.onload = function () {
				console.log('checkConnection: Online');

				app.showConnectionStatus();
				app.isConnected = true;

				return app.isConnected;
			};

			i.src = app.imgUrl + new Date().getTime();
		}

		app.showConnectionStatus();

		app.connectionTimer = window.setTimeout(app.checkConnection, 10000); //10 seg

		return app.isConnected;

	},

	showConnectionStatus: function(){
		var netstatus = $('.net-status');

		if(app.isConnected){
			//netstatus.removeClass('offline');
			//netstatus.text('online').addClass('online');
			netstatus.addClass('online');
			//app.getStoredRecords();
		}else{
			netstatus.removeClass('online');
			//netstatus.text('offline').addClass('offline');
		}

	},
};



// Declaro las variables globales
var isConnected = false;


//function estoyConectado(){
//	 var networkState = navigator.connection.type;
//
//	 var states = {};
//	 states[Connection.UNKNOWN]  = 'Unknown connection';
//	 states[Connection.ETHERNET] = 'Ethernet connection';
//	 states[Connection.WIFI]     = 'WiFi connection';
//	 states[Connection.CELL_2G]  = 'Cell 2G connection';
//	 states[Connection.CELL_3G]  = 'Cell 3G connection';
//	 states[Connection.CELL_4G]  = 'Cell 4G connection';
//	 states[Connection.NONE]     = 'No network connection';
//
//	 if(networkState == Connection.NONE){
//		  isConnected = false;
//	 }else{
//		  isConnected = true;
//	 }
//}


Date.prototype.dateFront = function() {
	//console.log('1: ' + this.toUTCString() );
	var newDate = this.toUTCString().split(' ')[2] + ' ' + this.toUTCString().split(' ')[1] + ' ' + this.getFullYear();
	return newDate;
};

/**
 * Devuelve un Unix timespamp
 * @param mode: s for seconds / m for miliseconds. Default s
 * @return timestamp
 */
Date.prototype.ivTimeStamp = function(mode){
	var m = mode || 's';
	var divisor = 1000;
	var r;
	if (!this.now) {
		 this.now = function() { return new Date().getTime(); }
	}

	switch(m){
		case 'm':
			divisor = 1;
		break;
	}

	r = Math.floor(this.now() / divisor);
	//console.log(r);
	return r;
};

String.prototype.float = function () {
	return parseFloat(this.replace(',', ''));
};

String.prototype.replaceAll = function (find, replace) {
	 var str = this;
	 return str.replace(new RegExp(find.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g'), replace);
};

String.prototype.capitalize = function() {
	return this.charAt(0).toUpperCase() + this.slice(1);
}

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


