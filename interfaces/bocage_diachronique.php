<!doctype html>
<html>
<head>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-127420066-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-127420066-1');
    </script>



    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="description" content="Observatoire du Bocage du Parc Normandie-Maine" />
    <meta name="author" content="SMo - PNR Normandie-Maine" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="pnrnm/leaflet/leaflet.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="pnrnm/styles/style_obs.css">
    <link rel="stylesheet" href="pnrnm/styles/style_vergers.css">
    <link rel="stylesheet" href="pnrnm/plugins/searchcontrol/leaflet-search.css">
    <style type="text/css">
        #maps { position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; margin: 0px; z-index:1;}
        #map1cadre, #map2cadre { position:relative; width: 50%; height: 100%; margin: 0px; padding: 0px; }
        #map1cadre { float: left; }
        #map2cadre { float: right; }
        #map1, #map2 { position:absolute; top:2px; bottom: 2px; border-radius: 10px; -moz-box-shadow: inset 0 0 10px #000000; -webkit-box-shadow: inset 0 0 10px #000000; box-shadow: inset 0 0 10px #000000;}
        #map1 { left: 2px; right: 1px; }
        #map2 { left: 1px; right: 2px; }
        .noCursor { cursor:none; }
    </style>

    <title>Observatoire du Bocage</title>
    <script src="pnrnm/leaflet/leaflet-src.js"></script>
    <script src="pnrnm/Leaflet.Control.Custom/Leaflet.Control.Custom.js"></script>
    <script src="pnrnm/plugins/searchcontrol/leaflet-search.js"></script>
    <script src="pnrnm/data/pnrnm_mask.js"></script>
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="pnrnm/plugins/sync/L.Map.Sync.js"></script>

</head>

<body>

<?php
$titrecarte = 'Comparaison Diachronique Bocage'
?>

<?php include('pnrnm/includesobs/bandeau_observatoire.php'); ?>
<?php include('pnrnm/includesobs/apropos_observatoire.php'); ?>
<?php include('bocage/info_bocage_diachro.php'); ?>


  <div class="container fill">
    <div id="maps">
    <div id="map1cadre"><div id="map1"></div></div>
    <div id="map2cadre"><div id="map2"></div></div>
    </div>
  </div>


    <script type="text/javascript">
    
/*
Cette page et ces scripts on été écrits par le PNR Normandie-Maine (Sylvain M.)
grace à l'aide précieuse des communautés d'entraide géomatique,
notamment le Forum SIG (http://www.forumsig.org) et Georezo (http://georezo.net/),
mais aussi http://www.developpez.com/ http://gis.stackexchange.com
Ils reposent sur des outils libres illustrent l'intérêt des outils OpenSource.
Les données sont hébergées :
- sur Carmen (http://carmen.naturefrance.fr/) : Bd-Bocage (1945/2000/2010)
- sur Geonormandie (http://www.geonormandie.fr/accueil) : Bd-Ortho Historique IGN (photographie 1950)
- sur le Geoportail de l'IGN (http://www.geoportail.gouv.fr) : Bd-Ortho IGN (photographies actuelles)
- sur GeoBretagne : Fond Carto OSM
*/    

// Affichage de la fenêtre "Plus d'infos Carte"
$("#about-btn-map").click(function() {
  $("#aboutModalMap").modal("show");
  $(".navbar-collapse.in").collapse("hide");
  return false;
});

// Affichage de la fenêtre "Plus d'infos Observatoire"
$("#about-btn-obs").click(function() {
  $("#aboutModalObs").modal("show");
  $(".navbar-collapse.in").collapse("hide");
  return false;
});

$(document).ready(function() {
  $("#aboutModalMap").modal("show");
  $(".navbar-collapse.in").collapse("hide");
  return false;
});

// Activation des tooltips
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});

        var IGNAPIKEY = 'alffi2npllzjvo962g945txo';

        var center = [48.56317164, -0.15068650];
        var scan25map1 = L.tileLayer('http://gpp3-wxs.ign.fr/' + IGNAPIKEY + '/geoportail/wmts?LAYER=GEOGRAPHICALGRIDSYSTEMS.MAPS&EXCEPTIONS=text/xml&FORMAT=image/jpeg&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=normal&TILEMATRIXSET=PM&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {attribution: '&copy; IGN'});
        var scan25map2 = L.tileLayer('http://gpp3-wxs.ign.fr/' + IGNAPIKEY + '/geoportail/wmts?LAYER=GEOGRAPHICALGRIDSYSTEMS.MAPS&EXCEPTIONS=text/xml&FORMAT=image/jpeg&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=normal&TILEMATRIXSET=PM&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {attribution: '&copy; IGN'});
        
        var orthohistomap1 = L.tileLayer('https://carto.geonormandie.fr/wmts?LAYER=bd_ortho_histo_pnrnm&EXCEPTIONS=text/xml&FORMAT=image/png&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=default&TILEMATRIXSET=GoogleMapsCompatible&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {attribution: '&copy; IGN | &copy; GeoNormandie'});
        var orthohistomap2 = L.tileLayer('https://carto.geonormandie.fr/wmts?LAYER=bd_ortho_histo_pnrnm&EXCEPTIONS=text/xml&FORMAT=image/png&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=default&TILEMATRIXSET=GoogleMapsCompatible&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {attribution: '&copy; IGN | &copy; GeoNormandie'});



        var orthomap1 = L.tileLayer('https://gpp3-wxs.ign.fr/'+IGNAPIKEY+'/geoportail/wmts?LAYER=ORTHOIMAGERY.ORTHOPHOTOS&EXCEPTIONS=text/xml&FORMAT=image/jpeg&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=normal&TILEMATRIXSET=PM&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.ign.fr">IGN-F Geoportail</a>'
        });
        var orthomap2 = L.tileLayer('https://gpp3-wxs.ign.fr/'+IGNAPIKEY+'/geoportail/wmts?LAYER=ORTHOIMAGERY.ORTHOPHOTOS&EXCEPTIONS=text/xml&FORMAT=image/jpeg&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=normal&TILEMATRIXSET=PM&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.ign.fr">IGN-F Geoportail</a>'
        });
        
        /*
        var orthomap1 = L.tileLayer('http://gpp3-wxs.ign.fr/' + IGNAPIKEY + '/geoportail/wmts?LAYER=ORTHOIMAGERY.ORTHOPHOTOS&EXCEPTIONS=text/xml&FORMAT=image/jpeg&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=normal&TILEMATRIXSET=PM&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {attribution: '&copy; IGN'});
        var orthomap2 = L.tileLayer('http://gpp3-wxs.ign.fr/' + IGNAPIKEY + '/geoportail/wmts?LAYER=ORTHOIMAGERY.ORTHOPHOTOS&EXCEPTIONS=text/xml&FORMAT=image/jpeg&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=normal&TILEMATRIXSET=PM&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {attribution: '&copy; IGN'});
        */
        
		var bdbocage1945map1 = new L.LayerGroup();
      L.tileLayer.wms("http://ws.carmencarto.fr/WMS/139/pnrnm_bocage", {
    	layers: 'Haies_1945',
    	srs: 'EPSG:3857',
        format: 'image/png',
        transparent: true,
        attribution: "PNRNM"
        }).addTo(bdbocage1945map1);

		var bdbocage2000map1 = new L.LayerGroup();
      L.tileLayer.wms("http://ws.carmencarto.fr/WMS/139/pnrnm_bocage", {
    	layers: 'Haies_2000',
    	srs: 'EPSG:3857',
        format: 'image/png',
        transparent: true,
        attribution: "PNRNM"
        }).addTo(bdbocage2000map1);

 		var bdbocage2010map1 = new L.LayerGroup();
      L.tileLayer.wms("http://ws.carmencarto.fr/WMS/139/pnrnm_bocage", {
    	layers: 'Haies_2010',
    	srs: 'EPSG:3857',
        format: 'image/png',
        transparent: true,
        attribution: "PNRNM"
        }).addTo(bdbocage2010map1);


		var bdbocage1945map2 = new L.LayerGroup();
      L.tileLayer.wms("http://ws.carmencarto.fr/WMS/139/pnrnm_bocage", {
    	layers: 'Haies_1945',
    	srs: 'EPSG:3857',
        format: 'image/png',
        transparent: true,
        attribution: "PNRNM"
        }).addTo(bdbocage1945map2);

		var bdbocage2000map2 = new L.LayerGroup();
      L.tileLayer.wms("http://ws.carmencarto.fr/WMS/139/pnrnm_bocage", {
    	layers: 'Haies_2000',
    	srs: 'EPSG:3857',
        format: 'image/png',
        transparent: true,
        attribution: "PNRNM"
        }).addTo(bdbocage2000map2);
     
 		var bdbocage2010map2 = new L.LayerGroup();
      L.tileLayer.wms("http://ws.carmencarto.fr/WMS/139/pnrnm_bocage", {
    	layers: 'Haies_2010',
    	srs: 'EPSG:3857',
        format: 'image/png',
        transparent: true,
        attribution: "PNRNM"
        }).addTo(bdbocage2010map2);


 		var osmgeobmap1 = new L.LayerGroup();
      L.tileLayer.wms("http://osm.geobretagne.fr/gwc01/service/wms", {
    	layers: 'osm:roads',
    	srs: 'EPSG:2154',
        format: 'image/png',
        transparent: true,
        attribution: "OSM / GeoBretagne"
        }).addTo(osmgeobmap1);

 		var osmgeobmap2 = new L.LayerGroup();
      L.tileLayer.wms("http://osm.geobretagne.fr/gwc01/service/wms", {
    	layers: 'osm:roads',
    	srs: 'EPSG:2154',
        format: 'image/png',
        transparent: true,
        attribution: "OSM / GeoBretagne"
        }).addTo(osmgeobmap2);

        
        
        var map1 = L.map('map1', {
            layers: [orthohistomap1, bdbocage1945map1, osmgeobmap1],
            center: center,
            zoom: 16,
            zoomControl: false
        });
        map1.attributionControl.setPrefix('');
        
       
        
        var map2 = L.map('map2', {
            layers: [orthomap2, bdbocage2010map2, osmgeobmap2],
            center: center,
            zoom: 16,
            zoomControl: false
        });


        var baseLayersmap1 = {
      "Photographies a&eacute;riennes 1950": orthohistomap1,
			"Photographies a&eacute;riennes actuelles": orthomap1
		};

        var baseLayersmap2 = {
      "Photographies a&eacute;riennes 1950": orthohistomap2,
			"Photographies a&eacute;riennes actuelles": orthomap2
		};

				var overlaysmap1 = {
			"Haies 1945": bdbocage1945map1,
			"Haies 2000": bdbocage2000map1,
			"Haies 2010": bdbocage2010map1,
			"Carte OSM": osmgeobmap1
		};

				var overlaysmap2 = {
			"Haies 1945": bdbocage1945map2,
			"Haies 2000": bdbocage2000map2,
			"Haies 2010": bdbocage2010map2,
			"Carte OSM": osmgeobmap2
		};

		var layersControl1 = L.control.layers(baseLayersmap1, overlaysmap1).addTo(map1);
		var layersControl2 = L.control.layers(baseLayersmap2, overlaysmap2).addTo(map2);

// Ajout du masque
// credits: https://github.com/turban/Leaflet.Mask
L.Mask = L.Polygon.extend({
	options: {
		stroke: false,
		color: '#333',
		fillOpacity: 0.9,
		clickable: true,

		outerBounds: new L.LatLngBounds([-90, -360], [90, 360])
	},

	initialize: function (latLngs, options) {
        
         var outerBoundsLatLngs = [
			this.options.outerBounds.getSouthWest(),
			this.options.outerBounds.getNorthWest(),
			this.options.outerBounds.getNorthEast(),
			this.options.outerBounds.getSouthEast()
		];
        L.Polygon.prototype.initialize.call(this, [outerBoundsLatLngs, latLngs], options);	
	},

});
L.mask = function (latLngs, options) {
	return new L.Mask(latLngs, options);
};


// transform geojson coordinates into an array of L.LatLng
var coordinates = masquepnrnm.features[0].geometry.coordinates[0];
var latLngs = [];
for (i=0; i<coordinates.length; i++) {
    latLngs.push(new L.LatLng(coordinates[i][1], coordinates[i][0]));
}

L.mask(latLngs).addTo(map1);
L.mask(latLngs).addTo(map2);




// Gestion des curseurs

// avec curseur sur la carte :
      cursor1 = L.circleMarker([0,0], {radius:20, weight: 8, fillOpacity: 0.1, color: '#FFFF00', fillColor: '#FFFF00'}).addTo(map1);
      cursor2 = L.circleMarker([0,0], {radius:20, weight: 8, fillOpacity: 0.1, color: '#FFFF00', fillColor: '#FFFF00'}).addTo(map2);

// sans curseur sur la carte :
      //cursor1 = L.circleMarker([0,0], {radius:20, weight: 8, fillOpacity: 0.1, color: '#FFFF00', fillColor: '#FFFF00', className: 'noCursor'}).addTo(map1);
      //cursor2 = L.circleMarker([0,0], {radius:20, weight: 8, fillOpacity: 0.1, color: '#FFFF00', fillColor: '#FFFF00', className: 'noCursor'}).addTo(map2);

      map1.on('mousemove', function (e) { 
          cursor2.setLatLng(e.latlng); 
          cursor1.setLatLng(e.latlng);
      });

      map2.on('mousemove', function (e) { 
          cursor1.setLatLng(e.latlng); 
          cursor2.setLatLng(e.latlng);
       });



        map1.sync(map2);
        map2.sync(map1);
        

// ajout du logo Parc
    var logoparc = L.control({position: 'topleft'});
    logoparc.onAdd = function(map1){
        var div = L.DomUtil.create('div', 'logoparcclass');
        div.innerHTML= "<a href='http://www.parc-naturel-normandie-maine.fr'><img alt='Parc Normandie-Maine' src='http://www.parc-naturel-normandie-maine.fr/web/images/logo.png'></a>";
        return div;
        }
    logoparc.addTo(map1);


var stylecommunes = {
        fillColor: 'white',
        opacity: 0,
        color: "white",  //Outline color
        weight: 2,
        fillOpacity: 0
};

function addCommunesToMap(communes, map1) {
		var communeslayer = L.geoJson(communes, {style: stylecommunes} );
		map1.addLayer(communeslayer);
		map2.addLayer(communeslayer);
		
		// Bouton de recherche :

searchControl = new L.Control.Search({
		layer: communeslayer,
		//position: 'topright',
		container: "div-search",
		textPlaceholder: 'Commune ?  ',
		propertyName: 'nom_evolution',
		marker: false,
		moveToLocation: function(latlng, title, map2) {
			//map.fitBounds( latlng.layer.getBounds() );
			var zoom = map2.getBoundsZoom(latlng.layer.getBounds());
  			map2.setView(latlng, zoom); // access the zoom
		}
	});

	searchControl.on('search:locationfound', function(e) {
		
		//console.log('search:locationfound', );

		//map.removeLayer(this._markerSearch)

		e.layer.setStyle({color: 'red', opacity: 1});
		if(e.layer._popup)
			e.layer.openPopup();

	}).on('search:collapsed', function(e) {

		communeslayer.eachLayer(function(layer) {	//restore feature color
			communeslayer.resetStyle(layer);
		});	
	});
	
	map2.addControl( searchControl );  //inizialize search control
};

$.getJSON("bocage/geojson_communes_stats_bdbocage.php", function(communes) { addCommunesToMap(communes, map1) });



// Ajout du contrôle "Zoom sur Commune"
var searchControl = new L.Control.Search();

var infozoom = L.control.custom({
    position: 'topright',


/* CONTENU */
    content :
    '<div id="div-search" style="min-height:3em">',

    classes : 'btn-group dropup',
    style   :
    {
        margin: '10px',
        padding: '0px 0 0 0',
        cursor: 'pointer',
    }
}).addTo(map2);



    </script>
</body>
</html>
