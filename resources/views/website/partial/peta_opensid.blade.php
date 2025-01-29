@include('layouts.components.assets_leaflet')

@push('css')
<style>
#map {
    width: 100%;
    height: 80vh;
}

@media (max-width: 980px) {
    #map {
        height: 40vh;
    }
}

@media (max-width: 480px) {
    #map {
        height: 20vh;
    }
}
</style>
@endpush
<div class="bg-blue p-2 text-bold"><h5>Sebaran Pengguna Baru OpenSID Periode <span id="judul_range_periode"></span></h5></div>
<div id="map"></div>

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

            $('#filter').click(function() {
                loadDataPeta();
            });
            $('input[name=periods]').change(function () {
                loadDataPeta();
            })
            $('#reset').click(function() {
                $('#provinsi').val('').trigger('change');
                $('#kabupaten').val('').trigger('change');
                $('#kecamatan').val('').trigger('change');

                loadDataPeta();
            });
            function loadDataPeta(){
                var kode_provinsi = $('#provinsi').val();
                var kode_kabupaten = $('#kabupaten').val();
                var kode_kecamatan = $('#kecamatan').val();
                var period = $('input[name=periods]').val();
                $('#judul_range_periode').html(period);
                
                map.removeLayer(markersBar);
                loadData(kode_provinsi, kode_kabupaten, kode_kecamatan, period);
            }
            function loadData(kode_provinsi = null, kode_kabupaten = null, kode_kecamatan = null, period = null, status = null) {

                $.ajax({
                    url: "{{ url('web/opensid/peta') }}",
                    contentType: "application/json; charset=utf-8",
                    cache: false,
                    dataType: "json",
                    data: {
                        kode_provinsi: kode_provinsi,
                        kode_kabupaten: kode_kabupaten,
                        kode_kecamatan: kode_kecamatan,
                        status: status,
                        period: period
                    },
                    responseType: "json",
                    success: function(response) {

                        // Buat Marker Cluster Group
                        markersBar = L.markerClusterGroup();

                        // Simpan Data geoJSON
                        barLayer = new L.geoJSON(response, {
                            pointToLayer: function(feature, latlng) {
                                return L.marker(latlng, {
                                    icon: baseballIcon
                                });
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

            $('input[name=periods]').trigger('change');
        });
    </script>
@endsection