@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
    <link rel="stylesheet" href="https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="https://leaflet.github.io/Leaflet.fullscreen/dist/leaflet.fullscreen.css" />
@endpush()

@push('js')
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
        integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
        crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

    <script src="https://leaflet.github.io/Leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>
    <script src="https://leaflet.github.io/Leaflet.fullscreen/dist/Leaflet.fullscreen.min.js"></script>

    <script>
        var layers = {};
        var overlayLayers = {};
         // Ubah icon
         var baseballIcon = L.icon({
                iconUrl: "{{ url('assets/img/opensid_logo.png') }}",
                iconSize: [20, 20],
            });


        function showPolygon(wilayah, layerpeta, warna = '#ffffff') {
            var area_wilayah = JSON.parse(JSON.stringify(wilayah));
            var bounds = new Array();

            var path = new Array();
            for (var i = 0; i < wilayah.length; i++) {
                var daerah_wilayah = area_wilayah[i];
                daerah_wilayah[0].push(daerah_wilayah[0][0]);
                var poligon_wilayah = L.polygon(daerah_wilayah, {
                    showMeasurements: true,
                    measurementOptions: {
                        showSegmentLength: false
                    },
                }).addTo(layerpeta);
                layers[poligon_wilayah._leaflet_id] = wilayah[i];
                poligon_wilayah.on("pm:edit", function(e) {
                    var old_path = getLatLong("Poly", {
                        _latlngs: layers[e.target._leaflet_id],
                    }).toString();
                    var new_path = getLatLong("Poly", e.target).toString();
                    var value_path = document.getElementById("path").value; //ambil value pada input

                    document.getElementById("path").value = value_path.replace(
                        old_path,
                        new_path
                    );
                    layers[e.target._leaflet_id] = JSON.parse(
                        JSON.stringify(e.target._latlngs)
                    ); // update value layers
                });
                var layer = poligon_wilayah;
                var geojson = layer.toGeoJSON();
                var shape_for_db = JSON.stringify(geojson);
                var gpxData = togpx(JSON.parse(shape_for_db));

                $("#exportGPX").on("click", function(event) {
                    data = "data:text/xml;charset=utf-8," + encodeURIComponent(gpxData);
                    $(this).attr({
                        href: data,
                        target: "_blank",
                    });
                });

                bounds.push(poligon_wilayah.getBounds());
                // set value setelah create masing2 polygon
                path.push(layer._latlngs);
            }

            layerpeta.fitBounds(bounds);
            document.getElementById("path").value = getLatLong("multi", path).toString();

        }

        function wilayah_property(set_marker, set_content = false) {
            var wilayah_property = L.geoJSON(turf.featureCollection(set_marker), {
                pmIgnore: true,
                showMeasurements: false,
                measurementOptions: {
                    showSegmentLength: false,
                },
                onEachFeature: function(feature, layer) {
                    if (set_content === true) {
                        layer.bindPopup(feature.properties.content);
                    }
                    layer.bindTooltip(feature.properties.content, {
                        sticky: true,
                        direction: "top",
                    });
                    feature.properties.style;
                },
                style: function(feature) {
                    if (feature.properties.style) {
                        return feature.properties.style;
                    }
                },
                pointToLayer: function(feature, latlng) {
                    console.log('point')
                    console.log(feature)
                    if (feature.properties.style) {
                        return L.marker(latlng, {
                            icon: feature.properties.style
                        });
                    } else {
                        return L.marker(latlng);
                    }
                },
            });

            return wilayah_property;
        }

        function set_marker(data, judul , contents,color) {
            marker = new Array();
            var area = JSON.parse(data);
            if (area == undefined) {
                return null;
            }
             var jml = area.length;
            content = $(contents).html();
            var style_polygon = {
                stroke: true,
                color: color.line,
                opacity: 1,
                weight: 3,
                fillColor: color.fill,
                fillOpacity: 0.8,
                dashArray: 4,
            };

            for (var x = 0; x < jml; x++) {
                for (var i = 0; i < area[x][0].length; i++) {
                    area[x][0][i].reverse();
                }
                area[x][0].push(area[x][0][0]);
                marker.push(
                turf.polygon(area[x], { content: contents, style: style_polygon })
                );
                var center = turf.centerOfMass(turf.polygon(area[x]));

            }

            console.log(center)
            return marker;
        }

        function poly_to_point(data){
            marker = new Array();
            var area = JSON.parse(data);
            if (area == undefined) {
                return null;
            }

            var jml = area.length;

            var point_style = {
                iconSize: [1, 1],
                iconAnchor: [0.5, 0.5],
                labelAnchor: [0.3, 0],
                iconUrl: baseballIcon,
            };

            for (var x = 0; x < jml; x++) {
                for (var i = 0; i < area[x][0].length; i++) {
                    area[x][0][i].reverse();
                }
                area[x][0].push(area[x][0][0]);
                marker=turf.centerOfMass(turf.polygon(area[x]),  {name: 'poly1' , style: L.icon(point_style)})
                marker.properties.style = baseballIcon;

            }
            console.log(L.icon(point_style))

            return marker;

        }
    </script>
@endpush()
