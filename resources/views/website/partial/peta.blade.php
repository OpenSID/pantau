@include('layouts.components.assets_leaflet')

@push('css')
<style>
  #map {
      width: 100%;
      height: 80vh;
  }
</style>
@endpush

<h3>Peta Pengguna OpenDesa</h3>
<div class="row">
  <div class="col-lg-9">
      <div id="map"></div>
  </div>
  <div class="col-lg-3">
      <div class="d-flex align-items-center pb-2">
          <div class="apps-icon">
              <i class="fas pantau-icon fa-openkab"></i>
          </div>
          <div class="apps-name">
              OpenKab
          </div>
          <div class="apps-number bg-pink" id="app-openkab-count">
              0
          </div>
      </div>
      <div class="d-flex align-items-center pb-2">
          <div class="apps-icon">
              <i class="fas pantau-icon fa-opendk"></i>
          </div>
          <div class="apps-name">
              OpenDK
          </div>
          <div class="apps-number bg-green" id="app-opendk-count">
              0
          </div>
      </div>
      <div class="d-flex align-items-center pb-2">
          <div class="apps-icon">
              <i class="fas pantau-icon fa-opensid"></i>
          </div>
          <div class="apps-name">
              OpenSID
          </div>
          <div class="apps-number bg-orange" id="app-opensid-count">
              0
          </div>
      </div>
      <div class="d-flex align-items-center pb-2">
          <div class="apps-icon">
              <i class="fas pantau-icon fa-layanandesa"></i>
          </div>
          <div class="apps-name">
              LayananDesa
          </div>
          <div class="apps-number bg-red" id="app-layanandesa-count">
              0
          </div>
      </div>
      <div class="d-flex align-items-center pb-2">
          <div class="apps-icon">
              <i class="fas pantau-icon fa-keloladesa"></i>
          </div>
          <div class="apps-name">
              KelolaDesa
          </div>
          <div class="apps-number bg-blue" id="app-keloladesa-count">
              0
          </div>
      </div>
  </div>
</div>

@section('js')
<script>
    $(document).ready(function() {
        var mapbox_token = "{{ config('tracksid.sandi.mapbox_token') }}";
        var markersBar;
        var barLayer;

        var map = L.map('map', {
            fullscreenControl: {
                pseudoFullscreen: false
            }
        }).setView([{{ config('leaflet.map_center_latitude') }}, {{ config('leaflet.map_center_longitude') }}], {{ config('leaflet.zoom_level') }});

        var mbAttr =
            'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>';
        var mbUrl =
            'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapbox_token;

        var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

        var streets = L.tileLayer(mbUrl, {
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            attribution: mbAttr
        });

        var osm = L.tileLayer(osmUrl, {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var baseLayers = {
            'OpenStreetMap': osm,
            'Streets': streets
        };

        // Tambahkan jenis map
        var layerControl = L.control.layers(baseLayers).addTo(map);

        var satellite = L.tileLayer(mbUrl, {
            id: 'mapbox/satellite-v9',
            tileSize: 512,
            zoomOffset: -1,
            attribution: mbAttr
        });
        layerControl.addBaseLayer(satellite, 'Satellite');

        // Ubah icon
        var baseballIcon = L.icon({
            iconUrl: "{{ url('assets/img/opensid_logo.png') }}",
            iconSize: [20, 20],
        });

        function onEachFeature(feature, layer) {
            layer.bindPopup(feature.properties.popupContent);
        }

        loadData();

        // Deteksi perubahan nilai pada input periods
        $('input[name=periods]').on('change', function () {
            loadData(); // Panggil loadData setiap kali period berubah
        });

       

        function loadData() {
            $.ajax({
                url: "{{ url('web/data-peta') }}",
                contentType: "application/json; charset=utf-8",
                cache: false,
                dataType: "json",
                data: {
                    period: $('input[name=periods]').val(),
                },
                responseType: "json",
                success: function(response) {

                    // Hapus marker cluster lama jika ada
                    if (markersBar) {
                        map.removeLayer(markersBar);
                    }

                    // Buat Marker Cluster Group
                    markersBar = L.markerClusterGroup();

                     // Simpan Data geoJSON
                     barLayer = new L.geoJSON(response, {
                        pointToLayer: function (feature, latlng) {
                            // Validasi koordinat sebelum membuat marker
                            if (isValidCoordinate(latlng.lat) && isValidCoordinate(latlng.lng)) {
                                return L.marker(latlng, {
                                    icon: baseballIcon
                                });
                            } else {
                                return null; // Jangan buat marker jika koordinat tidak valid
                            }
                        },
                        onEachFeature: onEachFeature
                    });
                    // Tambahkan Marker dan Marker Cluster Group pada Map
                    markersBar.addLayer(barLayer);
                    map.addLayer(markersBar);
                },
                error: function() {
                    alert('Gagal mengambil data');
                },
            });
        }

        function isValidCoordinate(value) {
            return !isNaN(value) && value !== null && value !== '' && parseFloat(value) <= 180 && parseFloat(value) >= -180;
        }

    });
</script>
@endsection