<!-- OpenStreetMap Css -->
<link rel="stylesheet" href="<?= base_url()?>assets/css/leaflet.css" />
<link rel="stylesheet" href="<?= base_url()?>assets/css/L.Control.Locate.min.css" />
<link rel="stylesheet" href="<?= base_url()?>assets/css/mapbox-gl.css" />
<link rel="stylesheet" href="<?= base_url()?>assets/css/peta.css">
<link rel="stylesheet" href="<?= base_url()?>assets/css/MarkerCluster.css" />
<link rel="stylesheet" href="<?= base_url()?>assets/css/MarkerCluster.Default.css" />

<style>
	#map
	{
		width:100%;
		height:70vh
	}
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Desa Pengguna OpenSID
      <small>Pengguna aktif dalam 2 bulan terakhir</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?= site_url()?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Peta Pengguna OpenSID</li>
    </ol>
  </section>
  <section class="content container-fluid" id="main">
    <div class="panel panel-default">
      <div class="panel-body">
        <form id="form-filter">
          <div class="col-sm-2">
            <div class="form-group">
              <label> Provinsi </label>
              <select id="provinsi" name="provinsi" class="form-control input-sm required">
                <option value="">Pilih Provinsi</option>
                <?php foreach ($provinsi as $data): ?>
                  <option <?php selected($provinsi, $data['nama_prov']) ?> value="<?= $data['nama_prov']?>"> <?= set_ucwords($data['nama_prov'])?> </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <label>Kabupaten</label>
              <select id="kabupaten" class="form-control input-sm required" name="kabupaten" data-source="<?= site_url()?>/peta/list_kab/" data-valueKey="nama_kab" data-displayKey="nama_kab" >
                <option class="placeholder" value="">Pilih Kabupaten</option>
                <?php foreach ($kabupaten as $data): ?>
                  <option <?php selected($kabupaten, $data['nama_kab']) ?> value="<?= $data['nama_kab']?>"> <?= set_ucwords($data['nama_kab'])?> </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <div id='isi_kecamatan' class="form-group">
              <label>Kecamatan</label>
              <select id="kecamatan" class="form-control input-sm required" name="kecamatan" data-source="<?= site_url()?>/peta/list_kec/" data-valueKey="nama_kec" data-displayKey="nama_kec">
                <option class="placeholder" value="">Pilih Kecamatan</option>
                <?php foreach ($kecamatan as $data): ?>
                  <option <?php selected($kecamatan, $data['nama_kec']) ?> value="<?= $data['nama_kec']?>"> <?= set_ucwords($data['nama_kec'])?> </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <div id='isi_desa' class="form-group">
              <label>Desa</label>
              <select id="desa" class="form-control input-sm required" name="desa" data-source="<?= site_url()?>/peta/list_desa/" data-valueKey="nama_desa" data-displayKey="nama_desa">
                <option class="placeholder" value="">Pilih Desa</option>
                <?php foreach ($desa as $data): ?>
                  <option <?php selected($desa, $data['nama_desa']) ?> value="<?= $data['nama_desa']?>"> <?= set_ucwords($data['nama_desa'])?> </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <button type="button" id="btn-reset" class="btn btn-default">Reset</button>
            </div>
          </div>
        </form>
      </div>
      <div id="map">
        <div class="leaflet-top leaflet-right">
          <section class="content">
            <div class="info-box bg-yellow">
              <span class="info-box-icon"><i class="fa fa-map-marker"><H5 class="info legend">NEGARA</H5></i></span>
              <div id="desa_online" class="info-box-content">
                <span class="info-box-text nama_negara"></span>
                <div class="progress">
                  <div class="progress-bar" style="width: 100%"></div>
                </div>
                <span class="info-box-number jml_desa"></span>
                <span class="progress-description"><i>Desa OpenSID Aktif</i></span>
              </div>
            </div>
            <div id="desa_online1" style="display: none;" class="info-box bg-red">
              <span class="info-box-icon"><i class="fa fa-map-marker"><h5 class="info legend">PROV.</h5></i></span>
              <div class="info-box-content">
                <span class="info-box-text nama_prov"></span>
                <div class="progress">
                  <div class="progress-bar" style="width: 100%"></div>
                </div>
                <span class="info-box-number jml_desa_prov"></span>
                <span class="progress-description"><i>Desa OpenSID Aktif</i></span>
              </div>
            </div>
            <div id="desa_online2" style="display: none;" class="info-box bg-green">
              <span class="info-box-icon"><i class="fa fa-map-marker"><h5 class="info legend">KAB.</h5></i></span>
              <div class="info-box-content">
                <span class="info-box-text nama_kab"></span>
                <div class="progress">
                  <div class="progress-bar" style="width: 100%"></div>
                </div>
                <span class="info-box-number jml_desa_kab"></span>
                <span class="progress-description"><i>Desa OpenSID Aktif</i></span>
              </div>
            </div>
            <div id="desa_online3" style="display: none;" class="info-box bg-blue">
              <span class="info-box-icon"><i class="fa fa-map-marker"><h5 class="info legend">KEC.</h5></i></span>
              <div class="info-box-content">
                <span class="info-box-text nama_kec"></span>
                <div class="progress">
                  <div class="progress-bar" style="width: 100%"></div>
                </div>
                <span class="info-box-number jml_desa_kec"></span>
                <span class="progress-description"><i>Desa OpenSID Aktif</i></span>
              </div>
            </div>
          </section>
        </div>
        <div class="leaflet-top leaflet-left">
          <div id="desa_online4" style="display: none;" class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-map-marker"><h5 class="info legend">DESA</h5></i></span>
            <div class="info-box-content">
              <span class="info-box-text nama_desa"></span>
              <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>
              <span class="info-box-number jml_desa_desa"></span>
              <span class="progress-description"><i>Desa OpenSID Aktif</i></span>
            </div>
          </div>
        </div>
        <div class="leaflet-bottom leaflet-left">
          <div id="qrcode">
            <div class="panel-body-lg">
              <a href="https://github.com/OpenSID/OpenSID">
                <img src="<?= base_url()?>assets/images/opensid.png" alt="OpenSID">
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
function select_options(select, params)
{
	var url_data = select.attr('data-source') + params;
	select
	.find('option').not('.placeholder')
	.remove()
	.end();
	$.ajax({
		url: url_data,
	}).then(function(options) {
		JSON.parse(options).forEach((option) => {
			var option_elem = $('<option>');
			option_elem
			.val(option[select.attr('data-valueKey')])
			.text(option[select.attr('data-displayKey')]);
			select.append(option_elem);
		});
	});
}

(function()
{
  var infoWindow;
  window.onload = function()
  {
		//Inisialisasi tampilan peta
    var posisi = [-1.0546279422758742,116.71875000000001];
    var zoom   = 10;

    var mymap = L.map('map').setView(posisi, zoom);;
    var bound = [[-10.3599874813, 95.2930261576], [5.47982086834,141.03385176]];
    mymap.fitBounds(bound);

    //Menampilkan BaseLayers Peta
    var baseLayers = getBaseLayers(mymap, '<?= $this->config->item('mapbox_token')?>');

    //Menambahkan zoom scale ke peta
    L.control.scale().addTo(mymap);

    //Geolocation IP Route/GPS
  	geoLocation(mymap);

    L.control.layers(baseLayers, null, {position: 'topleft', collapsed: true}).addTo(mymap);

		var layer_desa = L.featureGroup();

    //loading Peta Desa Pengguna OpenSID (Negara)
    layer_desa.clearLayers();
    pantau_desa_negara(mymap, layer_desa, '<?= base_url()?>', "<?= base_url()?>favicon.ico", "<?= $this->config->item('dev_token')?>");
    mymap.addLayer(layer_desa);

    //loading Peta Desa Pengguna OpenSID (Provinsi)
    $("#provinsi").change(function()
    {
      let provinsi = $(this).val();
      $('#isi_kecamatan').hide();
      $('#isi_desa').hide();
      var kabupaten = $('#kabupaten');
      select_options(kabupaten, provinsi);

      layer_desa.clearLayers();
      pantau_desa_prov(mymap, layer_desa, '<?= base_url()?>', provinsi, "<?= base_url()?>favicon.ico", "<?= $this->config->item('dev_token')?>");
      mymap.addLayer(layer_desa);

			setTimeout(function () {
				$('#desa_online1').show();
				$('#desa_online2').hide();
				$('#desa_online3').hide();
				$('#desa_online4').hide();
			});

    });

    //loading Peta Desa Pengguna OpenSID (Kabupaten)
    $("#kabupaten").change(function()
    {
      let provinsi = $("#provinsi").val();
      let kabupaten = $(this).val();
      $('#isi_desa').hide();

      $('#isi_kecamatan').show();
      var kecamatan = $('#kecamatan');
      var params = provinsi + '/' + kabupaten;
      select_options(kecamatan, params);

      layer_desa.clearLayers();
      pantau_desa_kab(mymap, layer_desa, '<?= base_url()?>', kabupaten, "<?= base_url()?>favicon.ico", "<?= $this->config->item('dev_token')?>");
      mymap.addLayer(layer_desa);

			setTimeout(function () {
				$('#desa_online2').show();
				$('#desa_online3').hide();
				$('#desa_online4').hide();
			});
    });

    //loading Peta Desa Pengguna OpenSID (Kecamatan)
    $("#kecamatan").change(function()
    {
      let provinsi = $("#provinsi").val();
      let kabupaten = $("#kabupaten").val();
      let kecamatan = $(this).val();

      $('#isi_desa').show();
      var desa = $('#desa');
      var params = provinsi + '/' + kabupaten + '/' + kecamatan;
      select_options(desa, params);

      layer_desa.clearLayers();
      pantau_desa_kec(mymap, layer_desa, '<?= base_url()?>', kecamatan, "<?= base_url()?>favicon.ico", "<?= $this->config->item('dev_token')?>");
      mymap.addLayer(layer_desa);

			setTimeout(function () {
				$('#desa_online3').show();
				$('#desa_online4').hide();
			});
    });

    //loading Peta Desa Pengguna OpenSID (Desa)
    $("#desa").change(function()
    {
      let provinsi = $("#provinsi").val();
      let kabupaten = $("#kabupaten").val();
      let kecamatan = $("#kecamatan").val();
      let desa = $(this).val();

      layer_desa.clearLayers();
      pantau_desa_desa(mymap, layer_desa, '<?= base_url()?>', desa, "<?= base_url()?>favicon.ico", "<?= $this->config->item('dev_token')?>");
      mymap.addLayer(layer_desa);

			setTimeout(function () {
				$('#desa_online4').show();
			});
    });

		$('#btn-reset').click(function(){
			$('#form-filter')[0].reset();
			location.reload();
		});

		

  }; //EOF window.onload

  })();
</script>

<!-- OpenStreetMap Js-->
<script src="<?= base_url()?>assets/js/leaflet.js"></script>
<script src="<?= base_url()?>assets/js/leaflet-providers.js"></script>
<script src="<?= base_url()?>assets/js/L.Control.Locate.min.js"></script>
<script src="<?= base_url()?>assets/js/peta.js"></script>
<script src="<?= base_url()?>assets/js/mapbox-gl.js"></script>
<script src="<?= base_url()?>assets/js/leaflet-mapbox-gl.js"></script>
<script src="<?= base_url()?>assets/js/leaflet.markercluster.js"></script>
<script src="<?= base_url()?>assets/js/turf.min.js"></script>
