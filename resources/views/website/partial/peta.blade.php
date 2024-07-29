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
  <div class="col-lg-10">
      <div id="map"></div>
  </div>
  <div class="col-lg-2">
      <div class="d-flex align-items-center pb-2">
          <div class="apps-icon">
              <i class="fas pantau-icon fa-openkab"></i>
          </div>
          <div class="apps-name">
              OpenKab
          </div>
          <div class="apps-number bg-pink">
              {{$jml_openkab}}
          </div>
      </div>
      <div class="d-flex align-items-center pb-2">
          <div class="apps-icon">
              <i class="fas pantau-icon fa-opendk"></i>
          </div>
          <div class="apps-name">
              OpenDK
          </div>
          <div class="apps-number bg-green">
            {{$jml_opendk}}
          </div>
      </div>
      <div class="d-flex align-items-center pb-2">
          <div class="apps-icon">
              <i class="fas pantau-icon fa-opensid"></i>
          </div>
          <div class="apps-name">
              OpenSID
          </div>
          <div class="apps-number bg-orange">
            {{$jml_opensid}}
          </div>
      </div>
      <div class="d-flex align-items-center pb-2">
          <div class="apps-icon">
              <i class="fas pantau-icon fa-layanandesa"></i>
          </div>
          <div class="apps-name">
              LayananDesa
          </div>
          <div class="apps-number bg-red">
           {{$jml_layanandesa}}
          </div>
      </div>
      <div class="d-flex align-items-center pb-2">
          <div class="apps-icon">
              <i class="fas pantau-icon fa-keloladesa"></i>
          </div>
          <div class="apps-name">
              KelolaDesa
          </div>
          <div class="apps-number bg-blue">
              {{$jml_keloladesa}}
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
        var iconUrl = "{{ url('assets/img/opensid_logo.png') }}";

        function createIcon(color) {
            return L.icon({
                iconUrl: iconUrl,
                iconSize: [20, 20],
                iconAnchor: [10, 10],
                popupAnchor: [0, -10],
                className: color
            });
        }

        // Mendapatkan data marker dari API Laravel
        $.getJSON('/web/data-peta', function(data) {
            data.forEach(function(marker) {
                L.marker([marker.lat, marker.lng], {icon: createIcon(marker.color)}).addTo(map)
                    .bindPopup(marker.popup);
            });
        });
    });
</script>
@endsection