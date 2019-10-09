<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid mt--7">
         
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">

                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Map Analytics Report</h3>
                                </div>
                            </div>
                        </div>
                        <div class="mb-12">
                            <Mapbox 
                                :accessToken="accessToken" 
                                :map-options="{
                                    style: 'mapbox://styles/mapbox/outdoors-v10',
                                    center: [124,11], // starting position
                                    minzoom:23,
                                    maxZoom:10,
                                    zoom: 5,
                                    maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]]
                                }"



                                :scale-control="{
                                    show: true,
                                    position: 'top-left'
                                }"
                                :fullscreen-control="{
                                    show: true,
                                    position: 'top-right'
                                }"

                                @map-load="mapLoad"
                                 @map-init="addMarkers"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

       
</template>

<script>
    import Mapbox from 'mapbox-gl-vue';
    import mapboxgl from 'mapbox-gl';
    export default {
        components: {
            Mapbox
        },
        data() {
            return {
                accessToken: 'pk.eyJ1IjoiY2hhcmxpZWRvdGF1IiwiYSI6ImNpazlpdzh0ZTA5d3Z2Y200emhqbml1OGEifQ.uoA6t5rO18m0BgNGPXsm5A',
                mapStyle: 'mapbox://styles/mapbox/streets-v9',
                draw: new MapboxDraw({
                        displayControlsDefault: false,
						controls: {
							polygon: true,
							trash: true
						}
                }),
                mapDefaultLayer: [],
                selectedProvinces: [],
                map_ids : '1'
            };
        },
        created() {
            this.mapbox = Mapbox;
        },
        methods: {
            mapLoad(map) {
                map.addControl(this.draw);

                fetch("/json/provinces.json")
                    .then(r => r.json())
                    .then(json => {
                        this.mapDefaultLayer = json;
                        var arr = [];
                        this.mapDefaultLayer.features.forEach((data) => {
                          
                            map.addLayer({
                                'id': 'M' + data.properties.ID_1,
                                'type': 'fill',
                                'source': {
                                    'type': 'geojson',
                                    'data': data
                                },
                                'layout': {},
                                'paint': {
                                    'fill-color': '#2DCE89',
                                    'fill-opacity': 0.1,
                                    'fill-outline-color': 'red'
                                }
                            });

                            map.on('click', 'M' + data.properties.ID_1, function (e) {
                                var map_id = 'M' + data.properties.ID_1;
                                selectedProvince(map_id);
                            });
   
                        }); 
                        
                        function selectedProvince(map_id){
                            var index = arr.findIndex(item => item == map_id);
                            if(index > -1){
                                map.setPaintProperty(map_id, 'fill-color', '#2DCE89');
                                map.setPaintProperty(map_id, 'fill-opacity', 0.1);
                               
                                arr.splice(index,1);
                            }else{
                                arr.push(map_id);  
                                map.setPaintProperty(map_id, 'fill-color', 'red'); 
                                map.setPaintProperty(map_id, 'fill-opacity', 0.3);
                            } 
                            console.log(arr);
                        }
                    },
                response => {
                    console.log('Error loading json:', response)
                });

            },
            addMarkers(map) {
                const geojson = {
                    type: 'FeatureCollection',
                    features: [
                    {
                        type: 'Feature',
                        geometry: {
                            type: 'Point',
                            coordinates: [121.070995, 14.860257]
                        },
                        properties: {
                            title: 'Arjay Lumagdong',
                            position: 'Bulacan'
                        }
                    },
                    {
                        type: 'Feature',
                        geometry: {
                        type: 'Point',
                            coordinates: [121.035249, 14.675647]
                        },
                        properties: {
                            title: 'Renz Cabatao',
                            position: 'Manila'
                        }
                    }
                    ]
                }

                geojson.features.forEach((marker) => {
                    // create a HTML element for each feature
                    const el = document.createElement('div')
                    el.className = 'employee'

                    // make a marker for each feature and add to the map
                    new mapboxgl.Marker(el)
                    .setLngLat(marker.geometry.coordinates)
                    .addTo(map)

                     el.addEventListener('click', e => {
                        e.stopPropagation();
                       new mapboxgl.Popup()
                        .setLngLat(marker.geometry.coordinates)
                        .setHTML('<div><h3 class="map-pop-up-text">' + 'Name: ' + marker.properties.title + '</h3><p class="map-pop-up-text">' + 'Position: ' + marker.properties.position + '</p></div>')
                        .addTo(map);
                    }, true);

                })
            }
    
        }
    }

</script>

<style>
    #map{
        height: 1100px;
        width: 70%;
        top: 10%;
        margin: 0px 10px 10px 10px;
        background-color:#006994!important;
        border-radius:10px;
    }

    .employee {
        background-image: url('/img/map/red-pin.png');
        background-size: cover;
        width: 30px;
        height: 30px;
        /* border-radius: 50%; */
        cursor: pointer;
    }

    .mapboxgl-ctrl-logo{
        display:none!important;
    }
    .mapboxgl-ctrl.mapboxgl-ctrl-attrib{
        display:none!important;
    }
    
</style>
