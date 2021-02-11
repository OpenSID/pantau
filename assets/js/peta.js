function getBaseLayers(peta, access_token)
{
	//Menampilkan BaseLayers Peta
	var defaultLayer = L.tileLayer.provider('OpenStreetMap.Mapnik', {attribution: '<a href="https://openstreetmap.org/copyright">© OpenStreetMap</a> | <a href="https://github.com/OpenSID/OpenSID">OpenSID</a>'}).addTo(peta);

  if (access_token)
  {
    mbGLstr = L.mapboxGL({
      accessToken: access_token,
      style: 'mapbox://styles/mapbox/streets-v11',
      attribution: '<a href="https://www.mapbox.com/about/maps">© Mapbox</a> | <a href="https://github.com/OpenSID/OpenSID">OpenSID</a>',
    });

    mbGLsat = L.mapboxGL({
  		accessToken: access_token,
  		style: 'mapbox://styles/mapbox/satellite-v9',
  		attribution: '<a href="https://www.mapbox.com/about/maps">© Mapbox</a> | <a href="https://github.com/OpenSID/OpenSID">OpenSID</a>',
  	});

  	mbGLstrsat = L.mapboxGL({
  		accessToken: access_token,
  		style: 'mapbox://styles/mapbox/satellite-streets-v11',
  		attribution: '<a href="https://www.mapbox.com/about/maps">© Mapbox</a> | <a href="https://github.com/OpenSID/OpenSID">OpenSID</a>',
  	});

  } else {
    mbGLstr = L.tileLayer.provider('OpenStreetMap.Mapnik', {attribution: '<a href="https://openstreetmap.org/copyright">© OpenStreetMap</a> | <a href="https://github.com/OpenSID/OpenSID">OpenSID</a>'}).addTo(peta);
    mbGLsat = L.tileLayer.provider('OpenStreetMap.Mapnik', {attribution: '<a href="https://openstreetmap.org/copyright">© OpenStreetMap</a> | <a href="https://github.com/OpenSID/OpenSID">OpenSID</a>'}).addTo(peta);
    mbGLstrsat = L.tileLayer.provider('OpenStreetMap.Mapnik', {attribution: '<a href="https://openstreetmap.org/copyright">© OpenStreetMap</a> | <a href="https://github.com/OpenSID/OpenSID">OpenSID</a>'}).addTo(peta);
  }

	var baseLayers = {
		'OpenStreetMap': defaultLayer,
		'OpenStreetMap H.O.T.': L.tileLayer.provider('OpenStreetMap.HOT', {attribution: '<a href="https://openstreetmap.org/copyright">© OpenStreetMap</a> | <a href="https://github.com/OpenSID/OpenSID">OpenSID</a>'}),
    'Mapbox Streets' : mbGLstr,
		'Mapbox Satellite' : mbGLsat,
		'Mapbox Satellite-Street' : mbGLstrsat
	};
	return baseLayers;
}

function geoLocation(layerpeta)
{
	var lc = L.control.locate({
		drawCircle: false,
		icon: 'fa fa-map-marker',
		locateOptions: {enableHighAccuracy: true},
		strings: {
				title: "Lokasi Saya",
				popup: "Anda berada di sekitar {distance} {unit} dari titik ini"
		}

	}).addTo(layerpeta);

	layerpeta.on('locationfound', function(e) {
			layerpeta.setView(e.latlng)
	});

	layerpeta.on('startfollowing', function() {
		layerpeta.on('dragstart', lc._stopFollowing, lc);
	}).on('stopfollowing', function() {
		layerpeta.off('dragstart', lc._stopFollowing, lc);
	});
	return lc;
}

//loading Peta Desa Pengguna OpenSID (Data dari API Server)
function pantau_desa(layer_desa, tracker_host, kode_desa, img, token)
{
  var pantau_desa = $.getJSON(tracker_host + 'index.php/api/wilayah/geoprov?token=' + token + '&kode_desa=' + kode_desa, function(data){
    var datalayer = L.geoJson(data ,{
      onEachFeature: function (feature, layer) {
        var custom_icon = L.icon({"iconSize": [16, 16], "iconUrl": img});
        layer.setIcon(custom_icon);
        var popup_0 = L.popup({"maxWidth": "100%"});
        var customOptions = {'maxWidth': '325', 'className' : 'covid_pop'};
        var html_a = $('<div id="html_a" style="width: 100.0%; height: 100.0%;">'
        + '<h4><b style="color:red">' + feature.properties.desa + '</b></h4>'
        + '<table>'
        + '<tr>'
        + '<td><b style="color:green">Alamat : ' + feature.properties.alamat + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Kecamatan : ' + feature.properties.kec + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Kab/Kota : ' + feature.properties.kab + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Provinsi : ' + feature.properties.prov + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Website : ' + '<a href="' + 'http://' + feature.properties.web + '" + " target=\"_blank\">' + 'http://' + feature.properties.web + '</a>' + '</b></td>'
        + '</tr>'
        + '</table></div>')[0];
        popup_0.setContent(html_a);
        layer.bindPopup(popup_0, customOptions);
        layer.bindTooltip(feature.properties.desa, {sticky: true, direction: 'top'});
      },
    });
    layer_desa.addLayer(datalayer);
    var infodesa = data;
    var nama_prov = infodesa.nama_provinsi;
    var jml_desa_prov = infodesa.jml_desa_prov;
    var lat = infodesa.lat;
    var lng = infodesa.lng;
    let attributes = ['nama_prov','jml_desa_prov'];
    attributes.forEach(function (attr) {
      $(`.${attr}`).html(eval(attr));
    })

    $.ajax({
        type: 'GET',
        url: tracker_host + 'index.php/api/wilayah/geokab?token=' + token + '&kode_desa=' + kode_desa,
        dataType: 'json',
        success: function(data) {
          var nama_kab = data.nama_kabupaten;
          var jml_desa_kab = data.jml_desa_kab;
          let attributes = ['nama_kab','jml_desa_kab'];
          attributes.forEach(function (attr) {
            $(`.${attr}`).html(eval(attr));
          })
        }
    });

    $.ajax({
        type: 'GET',
        url: tracker_host + 'index.php/api/wilayah/geokec?token=' + token + '&kode_desa=' + kode_desa,
        dataType: 'json',
        success: function(data) {
          var nama_kec = data.nama_kecamatan;
          var jml_desa_kec = data.jml_desa_kec;
          let attributes = ['nama_kec','jml_desa_kec'];
          attributes.forEach(function (attr) {
            $(`.${attr}`).html(eval(attr));
          })
        }
    });

    $.ajax({
        type: 'GET',
        url: tracker_host + 'index.php/api/wilayah/geoneg?token=' + token,
        dataType: 'json',
        success: function(data) {
          var nama_negara = data.nama_negara;
          var jml_desa = data.jml_desa;
          let attributes = ['nama_negara','jml_desa'];
          attributes.forEach(function (attr) {
            $(`.${attr}`).html(eval(attr));
          })
        }
    });

  });
  return pantau_desa;
}

function pantau_desa_negara(peta, layer_desa, tracker_host, img, token)
{
  var pantau_desa = $.getJSON(tracker_host + 'index.php/api/wilayah/geoneg_select?token=' + token, function(data){
    var datalayer = L.geoJson(data ,{
      onEachFeature: function (feature, layer) {
        var custom_icon = L.icon({"iconSize": [16, 16], "iconUrl": img});
        layer.setIcon(custom_icon);
      },
    });
		var markers = new L.MarkerClusterGroup();
		var markersList = [];
		markersList.push(datalayer);
		markers.addLayer(datalayer);
		layer_desa.addLayer(markers);

		var bounds = new L.LatLngBounds();
		if (layer_desa instanceof L.FeatureGroup) {
			bounds.extend(layer_desa.getBounds());
		}
		if (bounds.isValid()) {
			peta.fitBounds(bounds);
			peta._layersMaxZoom = 19;
		}

    var infodesa = data;
    var nama_negara = data.nama_negara;
    var jml_desa = data.jml_desa;
    var lat = infodesa.lat;
    var lng = infodesa.lng;
    let attributes = ['nama_negara','jml_desa'];
    attributes.forEach(function (attr) {
      $(`.${attr}`).html(eval(attr));
    })

  });

  return pantau_desa_negara;
}

//loading Peta Desa Pengguna OpenSID (Data dari API Server)
function pantau_desa_prov(peta, layer_desa, tracker_host, kode_desa, img, token)
{
  var pantau_desa = $.getJSON(tracker_host + 'index.php/api/wilayah/geoprov_select?token=' + token + '&kode_desa=' + kode_desa, function(data){
    var datalayer = L.geoJson(data ,{
      onEachFeature: function (feature, layer) {
        var custom_icon = L.icon({"iconSize": [16, 16], "iconUrl": img});
        layer.setIcon(custom_icon);
        var popup_0 = L.popup({"maxWidth": "100%"});
        var customOptions = {'maxWidth': '325', 'className' : 'covid_pop'};
        var html_a = $('<div id="html_a" style="width: 100.0%; height: 100.0%;">'
        + '<h4><b style="color:red">' + feature.properties.desa + '</b></h4>'
        + '<table>'
        + '<tr>'
        + '<td><b style="color:green">Alamat : ' + feature.properties.alamat + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Kecamatan : ' + feature.properties.kec + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Kab/Kota : ' + feature.properties.kab + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Provinsi : ' + feature.properties.prov + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Website : ' + '<a href="' + 'http://' + feature.properties.web + '" + " target=\"_blank\">' + 'http://' + feature.properties.web + '</a>' + '</b></td>'
        + '</tr>'
        + '</table></div>')[0];
        popup_0.setContent(html_a);
        layer.bindPopup(popup_0, customOptions);
        layer.bindTooltip(feature.properties.desa, {sticky: true, direction: 'top'});
      },
    });
		var markers = new L.MarkerClusterGroup();
		var markersList = [];
		markersList.push(datalayer);
		markers.addLayer(datalayer);
		layer_desa.addLayer(markers);

		var bounds = new L.LatLngBounds();
		if (layer_desa instanceof L.FeatureGroup) {
			bounds.extend(layer_desa.getBounds());
		}
		if (bounds.isValid()) {
			peta.fitBounds(bounds);
			peta._layersMaxZoom = 19;
		}

    var infodesa = data;
    var nama_prov = infodesa.nama_provinsi;
    var jml_desa_prov = infodesa.jml_desa_prov;
    var lat = infodesa.lat;
    var lng = infodesa.lng;
    let attributes = ['nama_prov','jml_desa_prov'];
    attributes.forEach(function (attr) {
      $(`.${attr}`).html(eval(attr));
    })

  });
  return pantau_desa_prov;
}

//loading Peta Desa Pengguna OpenSID (Data dari API Server)
function pantau_desa_kab(peta, layer_desa, tracker_host, kode_desa, img, token)
{
  var pantau_desa = $.getJSON(tracker_host + 'index.php/api/wilayah/geokab_select?token=' + token + '&kode_desa=' + kode_desa, function(data){
    var datalayer = L.geoJson(data ,{
      onEachFeature: function (feature, layer) {
        var custom_icon = L.icon({"iconSize": [16, 16], "iconUrl": img});
        layer.setIcon(custom_icon);
        var popup_0 = L.popup({"maxWidth": "100%"});
        var customOptions = {'maxWidth': '325', 'className' : 'covid_pop'};
        var html_a = $('<div id="html_a" style="width: 100.0%; height: 100.0%;">'
        + '<h4><b style="color:red">' + feature.properties.desa + '</b></h4>'
        + '<table>'
        + '<tr>'
        + '<td><b style="color:green">Alamat : ' + feature.properties.alamat + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Kecamatan : ' + feature.properties.kec + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Kab/Kota : ' + feature.properties.kab + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Provinsi : ' + feature.properties.prov + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Website : ' + '<a href="' + 'http://' + feature.properties.web + '" + " target=\"_blank\">' + 'http://' + feature.properties.web + '</a>' + '</b></td>'
        + '</tr>'
        + '</table></div>')[0];
        popup_0.setContent(html_a);
        layer.bindPopup(popup_0, customOptions);
        layer.bindTooltip(feature.properties.desa, {sticky: true, direction: 'top'});
      },
    });
		var markers = new L.MarkerClusterGroup();
		var markersList = [];
		markersList.push(datalayer);
		markers.addLayer(datalayer);
		layer_desa.addLayer(markers);

		var bounds = new L.LatLngBounds();
		if (layer_desa instanceof L.FeatureGroup) {
			bounds.extend(layer_desa.getBounds());
		}
		if (bounds.isValid()) {
			peta.fitBounds(bounds);
			peta._layersMaxZoom = 19;
		}

    var infodesa = data;
    var lat = infodesa.lat;
    var lng = infodesa.lng;
    var nama_kab = data.nama_kabupaten;
    var jml_desa_kab = data.jml_desa_kab;
    let attributes = ['nama_kab','jml_desa_kab'];
    attributes.forEach(function (attr) {
      $(`.${attr}`).html(eval(attr));
    })

  });
  return pantau_desa_kab;
}

//loading Peta Desa Pengguna OpenSID (Data dari API Server)
function pantau_desa_kec(peta, layer_desa, tracker_host, kode_desa, img, token)
{
  var pantau_desa = $.getJSON(tracker_host + 'index.php/api/wilayah/geokec_select?token=' + token + '&kode_desa=' + kode_desa, function(data){
    var datalayer = L.geoJson(data ,{
      onEachFeature: function (feature, layer) {
        var custom_icon = L.icon({"iconSize": [16, 16], "iconUrl": img});
        layer.setIcon(custom_icon);
        var popup_0 = L.popup({"maxWidth": "100%"});
        var customOptions = {'maxWidth': '325', 'className' : 'covid_pop'};
        var html_a = $('<div id="html_a" style="width: 100.0%; height: 100.0%;">'
        + '<h4><b style="color:red">' + feature.properties.desa + '</b></h4>'
        + '<table>'
        + '<tr>'
        + '<td><b style="color:green">Alamat : ' + feature.properties.alamat + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Kecamatan : ' + feature.properties.kec + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Kab/Kota : ' + feature.properties.kab + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Provinsi : ' + feature.properties.prov + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Website : ' + '<a href="' + 'http://' + feature.properties.web + '" + " target=\"_blank\">' + 'http://' + feature.properties.web + '</a>' + '</b></td>'
        + '</tr>'
        + '</table></div>')[0];
        popup_0.setContent(html_a);
        layer.bindPopup(popup_0, customOptions);
        layer.bindTooltip(feature.properties.desa, {sticky: true, direction: 'top'});
      },
    });
		var markers = new L.MarkerClusterGroup();
		var markersList = [];
		markersList.push(datalayer);
		markers.addLayer(datalayer);
		layer_desa.addLayer(markers);

		var bounds = new L.LatLngBounds();
		if (layer_desa instanceof L.FeatureGroup) {
			bounds.extend(layer_desa.getBounds());
		}
		if (bounds.isValid()) {
			peta.fitBounds(bounds);
			peta._layersMaxZoom = 19;
		}

    var infodesa = data;
    var lat = infodesa.lat;
    var lng = infodesa.lng;
    var nama_kec = data.nama_kecamatan;
    var jml_desa_kec = data.jml_desa_kec;
    let attributes = ['nama_kec','jml_desa_kec'];
    attributes.forEach(function (attr) {
      $(`.${attr}`).html(eval(attr));
    })

  });

  return pantau_desa_kec;
}

//loading Peta Desa Pengguna OpenSID (Data dari API Server)
function pantau_desa_desa(peta, layer_desa, tracker_host, kode_desa, img, token)
{
  var pantau_desa = $.getJSON(tracker_host + 'index.php/api/wilayah/geodesa_select?token=' + token + '&kode_desa=' + kode_desa, function(data){
    var datalayer = L.geoJson(data ,{
      onEachFeature: function (feature, layer) {
        var custom_icon = L.icon({"iconSize": [16, 16], "iconUrl": img});
        layer.setIcon(custom_icon);
        var popup_0 = L.popup({"maxWidth": "100%"});
        var customOptions = {'maxWidth': '325', 'className' : 'covid_pop'};
        var html_a = $('<div id="html_a" style="width: 100.0%; height: 100.0%;">'
        + '<h4><b style="color:red">' + feature.properties.desa + '</b></h4>'
        + '<table>'
        + '<tr>'
        + '<td><b style="color:green">Alamat : ' + feature.properties.alamat + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Kecamatan : ' + feature.properties.kec + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Kab/Kota : ' + feature.properties.kab + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Provinsi : ' + feature.properties.prov + '</b></td>'
        + '</tr>'
        + '<tr>'
        + '<td><b style="color:green">Website : ' + '<a href="' + 'http://' + feature.properties.web + '" + " target=\"_blank\">' + 'http://' + feature.properties.web + '</a>' + '</b></td>'
        + '</tr>'
        + '</table></div>')[0];
        popup_0.setContent(html_a);
        layer.bindPopup(popup_0, customOptions);
        layer.bindTooltip(feature.properties.desa, {sticky: true, direction: 'top'});
      },
    });
		var markers = new L.MarkerClusterGroup();
		var markersList = [];
		markersList.push(datalayer);
		markers.addLayer(datalayer);
		layer_desa.addLayer(markers);

		var bounds = new L.LatLngBounds();
		if (layer_desa instanceof L.FeatureGroup) {
			bounds.extend(layer_desa.getBounds());
		}
		if (bounds.isValid()) {
			peta.fitBounds(bounds);
			peta._layersMaxZoom = 19;
		}

    var infodesa = data;
    var lat = infodesa.lat;
    var lng = infodesa.lng;
    var nama_desa = data.nama_desa;
    var jml_desa_desa = data.jml_desa_desa;
    let attributes = ['nama_desa','jml_desa_desa'];
    attributes.forEach(function (attr) {
      $(`.${attr}`).html(eval(attr));
    })

  });

  return pantau_desa_kec;
}
