<?php $titrecarte = 'Le Bocage sur le territoire du Parc' ?>

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



    <title><?php echo $titrecarte ; ?></title>
    
    <link rel="icon" href="favicon.ico" />
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://ghinda.net/css-toggle-switch/dist/toggle-switch.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    
    <script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"></script>
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="pnrnm/Leaflet.snogylop/0.3.1/leaflet.snogylop.js"></script>
    <script src="pnrnm/Leaflet.Control.Custom/Leaflet.Control.Custom.js"></script>
    <script src="http://ismyrnow.github.io/leaflet-groupedlayercontrol/src/leaflet.groupedlayercontrol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.js"></script>
    <script src="pnrnm/plugins/searchcontrol/leaflet-search.js"></script>
    
    <link rel="stylesheet" href="pnrnm/styles/style_obs.css">
    <link rel="stylesheet" href="pnrnm/styles/style_bocage_communes.css">
    <link rel="stylesheet" href="pnrnm/plugins/searchcontrol/leaflet-search.css">
    
  </head>
<body>

<?php $show_help = true // détermine si on affiche l'aide ou pas dans le bandeau observatoire ?>

<?php include('pnrnm/includesobs/bandeau_observatoire.php'); ?>
<?php include('pnrnm/includesobs/apropos_observatoire.php'); ?>
<?php include('bocage/info_bocage.php'); ?>
<?php include('bocage/help_bocage_densite.php'); ?>


<div class="container fill">
  <div id="map"></div>
</div>


<script type="text/javascript">

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


// affichage de la fenêtre "Aide" (pas actif si $show_help n'est pas défini)
$("#about-help").click(function() {
  $("#aboutModalHelp").modal("show");
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

// définitions des variables par défaut du type de mailles / date
var currentDate = "Carte2010"; // soit "Carte1950", soit "Carte2010", Soit "CarteEvol"
var currentMaillesTyp = "maillescomnouv"; // soit "mailles1000m" / "maillescom" / "maillescomnouv" / "maillesdept" / "maillesepci"


var IGNAPIKEY = 'alffi2npllzjvo962g945txo';

var CartoDB_Positron = L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
        subdomains: 'abcd',
        maxZoom: 17
        }),
    CarteIGN = L.tileLayer('https://gpp3-wxs.ign.fr/'+IGNAPIKEY+'/geoportail/wmts?LAYER=GEOGRAPHICALGRIDSYSTEMS.MAPS&EXCEPTIONS=text/xml&FORMAT=image/jpeg&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=normal&TILEMATRIXSET=PM&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.ign.fr">IGN-F Geoportail</a>'
        }),
    IGNOrthoHisto = L.tileLayer('https://carto.geonormandie.fr/wmts?LAYER=bd_ortho_histo_pnrnm&EXCEPTIONS=text/xml&FORMAT=image/png&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=default&TILEMATRIXSET=GoogleMapsCompatible&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.ign.fr">IGN-F Geoportail</a> | &copy; GeoNormandie'
        }),
    IGNOrtho = L.tileLayer('https://gpp3-wxs.ign.fr/'+IGNAPIKEY+'/geoportail/wmts?LAYER=ORTHOIMAGERY.ORTHOPHOTOS&EXCEPTIONS=text/xml&FORMAT=image/jpeg&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=normal&TILEMATRIXSET=PM&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.ign.fr">IGN-F Geoportail</a>'
        });


var baseMaps = {
    "Carte OSM" : CartoDB_Positron,
    "Photo aérienne actuelle": IGNOrtho,
    "Photo aérienne historique": IGNOrthoHisto,
    "Carte IGN": CarteIGN
};

var map = L.map('map', {layers: [CartoDB_Positron], zoomControl:false });

var layersControl = L.control.layers(baseMaps, {}).addTo(map);

map.setView([48.56317164, -0.15068650], 9);

var searchControl = new L.Control.Search();

// ajout du contrôle InfoZoom // TO DO : masquer ce contrôle quand on survole une commune
var infozoom = L.control.custom({
    position: 'topright',

/* ANCIEN CONTENU
    content : '<div class="info">' +
              '<h4><span class="glyphicon glyphicon-zoom-in"></span> Le Bocage<br>sur ma commune ?</h4>' +
              '<br>Survollez les communes / mailles<br>avec la souris pour consulter<br>les statistiques locales<br>' +
              '<div id="div-search" style="min-height:3em"></div>' +
              '</div>',

*/

/* NOUVEAU CONTENU */
    content :
    '<div class="panel-group">' +
        '<div class="info panel panel-default">' +
            '<div class="panel-heading collapsed" data-toggle="collapse" data-target="#infozoomcontent"' +
               'href="#collapseThree" class="collapsed">' +
                 '<h4 class="panel-title">' +
                   '<p style="margin:0;">' +
                     '<span class="glyphicon glyphicon-zoom-in"></span> Le Bocage&nbsp;<br>sur ma commune&nbsp;&nbsp;' +
                   '</p>' +
                 '</h4>' +
            '</div>' +
            '<div id="infozoomcontent" class="panel-collapse collapse">' +
                '<div class="panel-body">' +
                '<p>Survollez les ' +
                (currentMaillesTyp == "mailles1000m" ? "mailles" :
                 currentMaillesTyp == "maillescom" ? "communes (2008)" :
                 currentMaillesTyp == "maillescomnouv" ? "communes" :
                 currentMaillesTyp == "maillesepci" ? "EPCI" :
                 currentMaillesTyp == "maillesdept" ? "départements" : "") +
                ' avec la souris pour consulter les statistiques locales</p>' +
                '<p>Vous pouvez également utiliser le moteur de recherche ci-dessous :</p>' +
                    '<div id="div-search" style="min-height:3em">' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>',

    classes : 'btn-group dropup',
    style   :
    {
        margin: '10px',
        padding: '0px 0 0 0',
        cursor: 'pointer',
    }
}).addTo(map);

var info = L.control({position:'topleft'});

info.onAdd = function (map) {
    this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
    this.update();
    return this._div;
};

var def_densite = 'La <b>densité bocagère</b> est le rapport entre le linéaire de haies et la surface du territoire.<br>Elle est généralement exprimée en m/ha';
var def_dens_brute = 'Le calcul de <b>densité brute</b> se base sur l ensemble de la surface communale';
var def_dens_nette = 'Le calcul de la <b>densité nette</b> exclue les surfaces non concernées par le bocage (forêts, zones urbaines, plans d eau, ...)';

// mise à jour du control info en fonction des propriétés de l'entité
info.update = function (props) {
    this._div.innerHTML =   (props ?
        //-<<<--------Quand une commune est pointée-------
        '<h4>Le Bocage sur' +
        (currentMaillesTyp == "mailles1000m" ? " la maille" :
         currentMaillesTyp == "maillescom" ? " la commune de" :
         currentMaillesTyp == "maillescomnouv" ? " la commune de" :
         currentMaillesTyp == "maillesepci" ? " l'EPCI" :
         currentMaillesTyp == "maillesdept" ? " le département" : "") +
        '</h4>' +
        (currentMaillesTyp != "mailles1000m" ?
              (props.nom_evolution.split(' (').length > 1 ?
                        ("<h3>" + props.nom_evolution.split(' (')[0] + "</h3><h4>(" + props.nom_evolution.split(' (')[1].replace(')','') + ")</h4>") :
                        ("<h3>" + props.nom_evolution + "</h3>") ) :
              ("<h5>référence " + props.nom_evolution + "</h5>")
         ) + '<br>' +
        '<h5>Linéaire de haies</h5>' +
        '<p><b>2010</b> : <span class="badge badge-info">' + Number((props.l_2010/1000).toFixed(0)) + '&nbsp;Km</span> de haies recencés</p>' +
        '<p><b>1950</b> : <span class="badge badge-info">' + Number((props.l_1945/1000).toFixed(0)) + '&nbsp;Km</span> de haies recencés</p>' +
        '<p>Soit une différence de <span class="badge badge-info" style="background-color: ' + getColorDiff((props.l_2010 - props.l_1945)/1000) + '!important ;">' + Number(((props.l_2010 - props.l_1945)/1000).toFixed(0)) + '&nbsp;Km</span></p><br>' +
        (typeof props.plantation != "undefined" ? (props.plantation != 0 ? '<h5>Opérations de plantations (Parc) :</h5><span class="badge badge-info">' + Number((props.plantation).toFixed(2)) + '&nbsp;ml</span> de haies plantées depuis 2008<br><br>' :  '<h5>Opérations de plantations (Parc) :</h5>Pas de plantations recensées depuis 2008<br><br>' ) :  "" ) +
        '<h5>Densité bocagère</h5>' +
        '<table class="table table-condensed">' +
          '<tbody class="small" style="text-align:right">' +
            '<tr><td></td><td><font color="grey"><small>Brute <sup>(1)</sup></small></font></td><td>Nette <sup>(2)</sup></td><td></td><td></td></tr>' +
            '<tr height="5px"><td><b>2010</b></td><td><font color="grey"><small><b>' + Number((props.db_2010).toFixed(0)) + '</b> m/ha<small></font></td><td><b>' + Number((props.dn_2010).toFixed(0)) + '</b> m/ha</td><td style="width: 10px;"></td><td><b class="cercles" style="background-color: ' + getColor(props.dn_2010) + ';"></b></td></tr>' +
            '<tr height="5px"><td><b>2000</b></td><td><font color="grey"><small><b>' + Number((props.db_2000).toFixed(0)) + '</b> m/ha<small></font></td><td><b>' + Number((props.dn_2000).toFixed(0)) + '</b> m/ha</td><td style="width: 10px;"></td><td><b class="cercles" style="background-color: ' + getColor(props.dn_2000) + ';"></b></td></tr>' +
            '<tr height="5px"><td><b>1950</b></td><td><font color="grey"><small><b>' + Number((props.db_1945).toFixed(0)) + '</b> m/ha<small></font></td><td><b>' + Number((props.dn_1945).toFixed(0)) + '</b> m/ha</td><td style="width: 10px;"></td><td><b class="cercles" style="background-color: ' + getColor(props.dn_1945) + ';"></b></td></tr>' +
          '</tbody>' +
        '</table>' +
              //-<<<--------création d'un graphique dynamique avec Google Chart API-------
        '<div style="margin: 0 auto; align: center; text-align: center;">' +
        '<img alt="Graph Evol Densité" src="https://chart.googleapis.com/chart?'+
        'cht=lxy' + // type de graphique (ligne xy)
        '&chs=200x200' + // dimension du graphique
        '&chd=t:12.5,75,87.5|'+ (props.dn_1945)/3 +','+ (props.dn_2000)/3 +','+ (props.dn_2010)/3 +'|12.5,75,87.5|56.76,28.63,28.48' + // séries
        '&chco=ba3d59,bfbfbf' + // couleurs des séries
        '&chls=3.0,6.0,3.0|2.0,4.0,2.0' + // style des lignes (épaisseur + largeur tirets + largeurs espaces)
        '&chxt=x,y' +
        '&chxl=0:||1950|||||||2010||1:|0||100m/ha||200m/ha||300m/ha' +
        '&chm=o,800000,0,-1,8|o,808080,1,-1,5' +
        '&chdl=' +
        (currentMaillesTyp != "mailles1000m" ? (props.nom_evolution.split(' (').length > 1 ? props.nom_evolution.split(' (')[0] : props.nom_evolution) : 'Maille') +
        '|Moyenne+du+Parc'+ // Chart legend text
        '&chdlp=tv'+ // Chart legend position
        '&chg=12.6,16.6,1,5">'+ // ajout des "grid lines"
        '</div>'
              //-----------Fin du graphique Google Chart API -----------------------------
        
        :
        //-<<<--------Quand aucune commune n'est pointée-------
        '<h4>Le Bocage sur le territoire du</h4>' +
        '<h3>Parc Normandie-Maine</h3>' + '<br>' +
        '<h5>Linéaire de haies</h5>' +
        '<p><b>2010</b> : <span class="badge badge-info">' + '17 387' + '&nbsp;Km</span> de haies recencés</p>' +
        '<p><b>1950</b> : <span class="badge badge-info">' + '34 650' + '&nbsp;Km</span> de haies recencés</p>' +
        '<p>Soit une différence de <span class="badge badge-info" style="background-color: #ba3d59 !important ;">' + '-17 263' + '&nbsp;Km</span></p><br>' +
        '<h5>Densité bocagère <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-html="true" data-placement="top" title="' + def_densite + '"></span></h5>' +
        '<table class="table table-condensed">' +
          '<tbody class="small" style="text-align:right">' +
            '<tr><td></td><td><font color="grey"><small>Brute <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-html="true" data-placement="top" title="' + def_dens_brute + '"></span></small></font></td><td>Nette <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-html="true" data-placement="top" title="' + def_dens_nette + '"></span></td><td></td><td></td></tr>' +
            '<tr height="5px"><td><b>2010</b></td><td><font color="grey"><small><b>' + '67.68' + '</b> m/ha<small></font></td><td><b>' + '85.44' + '</b> m/ha</td><td style="width: 10px;"></td><td><b class="cercles" style="background-color: ' + 'white' + ';"></b></td></tr>' +
            '<tr height="5px"><td><b>2000</b></td><td><font color="grey"><small><b>' + '68.04' + '</b> m/ha<small></font></td><td><b>' + '85.90' + '</b> m/ha</td><td style="width: 10px;"></td><td><b class="cercles" style="background-color: ' + 'white' + ';"></b></td></tr>' +
            '<tr height="5px"><td><b>1950</b></td><td><font color="grey"><small><b>' + '134.87' + '</b> m/ha<small></font></td><td><b>' + '170.27' + '</b> m/ha</td><td style="width: 10px;"></td><td><b class="cercles" style="background-color: ' + 'white' + ';"></b></td></tr>' +
          '</tbody>' +
        '</table>' +
        '<div style="margin: 0 auto; align: center; text-align: center;">' +
        '<img alt="Graph Evol Densité" src="https://chart.googleapis.com/chart?' +
        'cht=lxy' + // type de graphique (ligne xy)
        '&chs=200x200' +  // dimension du graphique
        '&chd=t:12.5,75,87.5|56.76,28.63,28.48' +  // séries
        '&chco=ba3d59' + // couleurs des séries
        '&chls=3.0,6.0,3.0' +  // style des lignes (épaisseur + largeur tirets + largeurs espaces)
        '&chxt=x,y' +
        '&chxl=0:||1950|||||||2010||1:|0||100m/ha||200m/ha||300m/ha' +
        '&chm=o,800000,0,-1,8' +
        //'&chdl=Moyenne+du+Parc' + // Chart legend text
        //'&chdlp=bv' +  // Chart legend position
        '&chg=12.6,16.6,1,5">'+ // ajout des "grid lines"
        '</div>');
		
        // réactivation des Tooltips
        $('[data-toggle="tooltip"]').tooltip();
};

// ajout du contrôle "info" à la carte
info.addTo(map);

// ajout de la barre d'échelle
L.control.scale({
    imperial: false   
}).addTo(map);


// ajout du logo Parc
    var logoparc = L.control({position: 'topleft'});
    logoparc.onAdd = function(map){
        var div = L.DomUtil.create('div', 'logoparcclass');
        div.innerHTML= "<a href='http://www.parc-naturel-normandie-maine.fr'><img alt='Parc Normandie-Maine' src='http://www.parc-naturel-normandie-maine.fr/web/images/logo.png'></a>";
        return div;
    }
    logoparc.addTo(map);


// fonction qui retourne une couleur en fonction d'une valeur
function getColor(d) {
    return d > 180 ? '#041109' :
           d > 165  ? '#03190c' :
           d > 150  ? '#042611' :
           d > 145  ? '#00441b' :
           d > 130  ? '#00441b' :
           d > 115  ? '#137e3a' :
           d > 100  ? '#3da75a' :
           d >  85  ? '#7bc87c' :
           d >  70  ? '#b2e0ab' :
           d >  55  ? '#ddf2d7' :
           d >  40  ? '#f7fcf5' :
                      '#ffffff';
};

function getColorEvol(d) {
    return  d < -150  ? '#d7191c' :
             d < -130  ? '#f69053' :
             d < -100  ? '#fff69a' :
             d <  -50  ? '#fff6d3' :
             d <    0  ? '#b7eb9a' :
             d >=   0  ? '#1a9641' :
                         '#ffffff';
};


function getColorDiff(d) {
    return  d <  0 ? '#ba3d59' :
             d >= 0 ? '#58a374' :
                      '#ffffff';
};


function styleDN2010(feature) {
    return {
        fillColor: getColor(feature.properties.dn_2010),
        weight: (currentMaillesTyp == 'maillescom' ? 2 :
                 currentMaillesTyp == 'maillescomnouv'? 2 :
                 currentMaillesTyp == 'maillesdept'? 2 :
                 currentMaillesTyp == 'maillesepci'? 2 :
                 currentMaillesTyp == 'mailles1000m' ? 0.5 : 2),
        opacity: 1,
        color: 'white',
        dashArray: '3',
        fillOpacity: 0.7
    };
};

function styleDN1950(feature) {
    return {
        fillColor: getColor(feature.properties.dn_1945),
        weight: (currentMaillesTyp == 'maillescom' ? 2 :
                 currentMaillesTyp == 'maillescomnouv'? 2 :
                 currentMaillesTyp == 'maillesdept'? 2 :
                 currentMaillesTyp == 'maillesepci'? 2 :
                 currentMaillesTyp == 'mailles1000m' ? 0.5 : 2),
        opacity: 1,
        color: 'white',
        dashArray: '3',
        fillOpacity: 0.7
    };
};

function style_evol_DN(feature) {
    return {
        fillColor: getColorEvol(feature.properties.dn_2010 - feature.properties.dn_1945),
        weight: (currentMaillesTyp == 'maillescom' ? 2 :
                 currentMaillesTyp == 'maillescomnouv'? 2 :
                 currentMaillesTyp == 'maillesdept'? 2 :
                 currentMaillesTyp == 'maillesepci'? 2 :
                 currentMaillesTyp == 'mailles1000m' ? 0.5 : 2),
        opacity: 1,
        color: 'white',
        dashArray: '3',
        fillOpacity: 0.7
    };
};

function highlightFeature(e) {
    var layer = e.target;

    layer.setStyle({
        weight: 3,
        color: '#000',
        dashArray: '',
        fillOpacity: 0.7
    });

    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
        layer.bringToFront();
    }
    info.update(layer.feature.properties);
};

function resetHighlight(e) {
    currentDate == "Carte1950" ? (style_mailles = styleDN1950) :
    currentDate == "Carte2010" ? (style_mailles = styleDN2010) :
    currentDate == "CarteEvol" ? (style_mailles = style_evol_DN) :
                                 (style_mailles = styleDN2010) ;
    mailleslayer.resetStyle(e.target);
    mailleslayer.setStyle(style_mailles);
    info.update();
};

function zoomToFeature(e) {
    map.fitBounds(e.target.getBounds());
};

function onEachFeature(feature, layer) {
    layer.on({
        mouseover: highlightFeature,
        mouseout: resetHighlight,
        click: zoomToFeature
    });
};

var stylemask = {
        fillColor: 'white',
        opacity: 0.8,
        color: "#999999",  //Outline color
        weight: 2,
        dashArray: "5 5",
        fillOpacity: 0.5
};

var stylepnrnm = {
        fillColor: 'white',
        opacity: 1,
        color: "black",  //Outline color
        weight: 4,
        fillOpacity: 0
};

var stylecommunes = {
        fillColor: 'yellow',
        opacity: 0.8,
        color: "#999999",  //Outline color
        weight: 2,
        fillOpacity: 0.3
};

/*
function addMaskToMap(pnrnm, map) {
		masklayer = L.geoJson(pnrnm, {
			style: stylemask,
			invert: true
		});
		map.addLayer(masklayer);
		layersControl.addOverlay(masklayer , "Masque Parc");
};

function addPnrnmToMap(pnrnm, map) {
		pnrnmlayer = L.geoJson(pnrnm, {
			style: stylepnrnm
		});
		map.addLayer(pnrnmlayer);
		map.fitBounds(pnrnmlayer.getBounds());
		layersControl.addOverlay(pnrnmlayer , "Périmètre Parc");
};

*/

function addPnrnmToMap(pnrnm, map) {
		masklayer = L.geoJson(pnrnm, {
			style: stylemask,
			invert: true
		});
		pnrnmlayer = L.geoJson(pnrnm, {
			style: stylepnrnm
		});
		pnrnmgroup = L.layerGroup([masklayer, pnrnmlayer]);
		map.addLayer(pnrnmgroup);
		map.fitBounds(pnrnmlayer.getBounds());
		layersControl.addOverlay(pnrnmgroup , "Périmètre Parc");
};

function addmaillesToMap(mailles, map) {
    currentDate == "Carte1950" ? (style_mailles = styleDN1950) :
    currentDate == "Carte2010" ? (style_mailles = styleDN2010) :
    currentDate == "CarteEvol" ? (style_mailles = style_evol_DN) :
                                 (style_mailles = styleDN2010) ;
		mailleslayer = L.geoJson(mailles, {style: style_mailles, onEachFeature: onEachFeature} );
		mailleslayer.getAttribution = function() { return '&copy; <a href="http://metadata.carmencarto.fr/geosource/139/fre/find?uuid=c0b788e5-1a4c-4ec0-9041-9523e282972d">Bd Bocage - PNR Normands</a>'; };
		map.addLayer(mailleslayer);
		layersControl.addOverlay(mailleslayer , "Densité bocagère");
		
		
		
		
// Bouton de recherche :

// TODO : ajouter un moyen pour retirer le contrôle s'il existe déjà, sinon, ça se duplique
// if (typeof searchControl != "undefined") {removeControl(searchControl)} ;

searchControl = new L.Control.Search({
		layer: mailleslayer,
		//position: 'topright',
		container: "div-search",
		textPlaceholder: (currentMaillesTyp == 'mailles1000m' ? 'Maille ?' :
                      currentMaillesTyp == 'maillesdept' ? 'Num Département ?' :
                      currentMaillesTyp == 'maillesepci' ? 'EPCI ?' :
                      currentMaillesTyp == 'maillescom' ? 'Commune (2008) ?' :
                      currentMaillesTyp == 'maillescomnouv' ? 'Commune ?' : ''),
		propertyName: 'nom_evolution',
		marker: false,
		moveToLocation: function(latlng, title, map) {
			//map.fitBounds( latlng.layer.getBounds() );
			var zoom = map.getBoundsZoom(latlng.layer.getBounds());
  			map.setView(latlng, zoom); // access the zoom
		}
	});

	searchControl.on('search:locationfound', function(e) {
		
		//console.log('search:locationfound', );

		//map.removeLayer(this._markerSearch)

		//e.layer.setStyle({fillColor: '#3f0', color: '#0f0'});
		if(e.layer._popup)
			e.layer.openPopup();

	}).on('search:collapsed', function(e) {

		mailleslayer.eachLayer(function(layer) {	//restore feature color
			mailleslayer.resetStyle(layer);
		});	
	});
	
	map.addControl( searchControl );  //inizialize search control
		
};

function getMaillesPhP(currentMaillesTyp) {
    return currentMaillesTyp == "mailles1000m" ? "bocage/geojson_mailles1km_stats_bdbocage.php" :
            currentMaillesTyp == "maillesdept" ? "bocage/geojson_dept_stats_bdbocage.php" :
            currentMaillesTyp == "maillesepci" ? "bocage/geojson_epci_stats_bdbocage.php" :
            currentMaillesTyp == "maillescom" ? "bocage/geojson_communes_stats_bdbocage.php" :
            currentMaillesTyp == "maillescomnouv" ? "bocage/geojson_communes_nouv_stats_bdbocage.php" :
                                "bocage/geojson_communes_stats_bdbocage.php";
                                // etc...
}


$.getJSON("bocage/geojson_pnrnm.php", function(pnrnm) { addPnrnmToMap(pnrnm, map) });

$.getJSON(getMaillesPhP(currentMaillesTyp), function(mailles) { addmaillesToMap(mailles, map); });




// Ajout des couches de haies depuis le Serveur Carmen

		var bdbocage1945 = new L.LayerGroup();
      L.tileLayer.wms("http://ws.carmencarto.fr/WMS/139/pnrnm_bocage", {
    	layers: 'Haies_1945',
    	srs: 'EPSG:3857',
        format: 'image/png',
        transparent: true,
        attribution: '&copy; <a href="http://metadata.carmencarto.fr/geosource/139/fre/find?uuid=c0b788e5-1a4c-4ec0-9041-9523e282972d">Bd Bocage</a>'
        }).addTo(bdbocage1945);
    layersControl.addOverlay(bdbocage1945 , "Haies 1945");
    
		var bdbocage2000 = new L.LayerGroup();
      L.tileLayer.wms("http://ws.carmencarto.fr/WMS/139/pnrnm_bocage", {
    	layers: 'Haies_2000',
    	srs: 'EPSG:3857',
        format: 'image/png',
        transparent: true,
        attribution: '&copy; <a href="http://metadata.carmencarto.fr/geosource/139/fre/find?uuid=c0b788e5-1a4c-4ec0-9041-9523e282972d">Bd Bocage</a>'
        }).addTo(bdbocage2000);
    layersControl.addOverlay(bdbocage2000 , "Haies 2000");
    
 		var bdbocage2010 = new L.LayerGroup();
      L.tileLayer.wms("http://ws.carmencarto.fr/WMS/139/pnrnm_bocage", {
    	layers: 'Haies_2010',
    	srs: 'EPSG:3857',
        format: 'image/png',
        transparent: true,
        attribution: '&copy; <a href="http://metadata.carmencarto.fr/geosource/139/fre/find?uuid=c0b788e5-1a4c-4ec0-9041-9523e282972d">Bd Bocage</a>'
        }).addTo(bdbocage2010);
    layersControl.addOverlay(bdbocage2010 , "Haies 2010");



// création des légendes

var legendDN = L.control({position: 'bottomright'});

legendDN.onAdd = function (map) {

    var div = L.DomUtil.create('div', 'legendDN'),
        grades = [20, 40, 60, 80, 100, 120, 140, 160, 180],
        labels = ["<strong>Densité bocagère <span class='glyphicon glyphicon-question-sign' data-toggle='tooltip' data-html='true' data-placement='top' title='" + def_densite + "'></span><br>nette <span class='glyphicon glyphicon-question-sign' data-toggle='tooltip' data-html='true' data-placement='top' title='" + def_dens_nette + "'></span></strong><br>"];

    // loop through our density intervals and generate a label with a colored square for each interval
    for (var i = 0; i < grades.length; i++) {
        labels.push(
            '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
            grades[i] + (grades[i + 1] ? '&nbsp;à&nbsp;' + grades[i + 1] + ' m/ha<br>' : ' m/ha et +'));
    }
        div.innerHTML = labels.join('');
    return div;
};

var legendEvol = L.control({position: 'bottomright'});

legendEvol.onAdd = function (map) {

    var div = L.DomUtil.create('div', 'legendEvol'),
        grades = [-200, -150, -130, -100, -50, 1],
        labels = ["<strong>Evolution de la<br>densité bocagère</strong> <br>"];

    // loop through our density intervals and generate a label with a colored square for each interval
    for (var i = 0; i < grades.length; i++) {
        labels.push(
            '<i style="background:' + getColorEvol(grades[i] + 1) + '"></i> ' +
            grades[i] + (grades[i + 1] ? '&nbsp;à&nbsp;' + grades[i + 1] + ' m/ha<br>' : ' m/ha et +'));
    }
        div.innerHTML = labels.join('');

        return div;
};




// ----------------------------------------------------
// AJOUTS DES CONTROLES (plugin Leaflet.control.custom)
// https://yigityuce.github.io/Leaflet.Control.Custom/
// ----------------------------------------------------


// CHANGEMENT TYPE MAILLE -----------------------------

// fonction du bouton du contrôle de type de maille
$(function(){
  
  $(".dropdown-menu li a").click(function(){
    if (currentMaillesTyp == $(this).attr("class")) {} else {
    layersControl.removeLayer(mailleslayer);
    map.removeLayer(mailleslayer);
    map.removeControl(searchControl);
    currentMaillesTyp = $(this).attr("class");
    $.getJSON(getMaillesPhP(currentMaillesTyp), function(mailles) { addmaillesToMap(mailles, map); });
    $(".dropdown-toggle .listname").text($(this).text());
    info.update();
  } 
  });

});

// ajout du contrôle Type de maille
L.control.custom({
    position: 'bottomright',
    content : '<div id="select-option" class="btn-group dropup" data-toggle="tooltip" data-placement="right" title="Changer le type de mailles">' +
              '<button type="button" class="btn btn-default btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="min-width:150px;">' +
                  '<span class="glyphicon glyphicon-th" aria-hidden="true"></span>' +
                  '<span class="listname">&nbsp;Type Maille </span><span class="caret"></span>' +
              '</button>' +
              '<ul class="dropdown-menu" role="menu" style="min-width:150px;">' +
                  '<li><a href="#" class="mailles1000m"><span class="badge badge-info"><span class="glyphicon glyphicon-th" aria-hidden="true"></span>&nbsp;&nbsp;Mailles 1 Km</span></a></li>' +
                  '<li><a href="#" class="maillesdept"><span class="badge badge-info"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;&nbsp;Départements&nbsp;</span></a></li>'+
                  '<li><a href="#" class="maillesepci"><span class="badge badge-info"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;&nbsp;EPCI&nbsp;</span></a></li>'+
                  '<li><a href="#" class="maillescom"><span class="badge badge-info"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;&nbsp;Communes 2008&nbsp;</span></a></li>'+
                  '<li><a href="#" class="maillescomnouv"><span class="badge badge-info"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;&nbsp;Communes&nbsp;</span></a></li>'+
              '</ul>' +
              '</div>',

    classes : 'btn-group dropup',
    style   :
    {
        margin: '10px',
        padding: '0px 0 0 0',
        cursor: 'pointer',
    }
})
.addTo(map);





// CHANGEMENT TYPE ANALYSE -----------------------------

// fonctions du Toggle Densité 1950 / 2010 / EVolution

function changeDateTo1950() {
		mailleslayer.setStyle(styleDN1950);
		map.removeControl(legendEvol);
		map.removeControl(legendDN);
    legendDN.addTo(map);
    currentDate = "Carte1950";
	// réactivation des Tooltips
	$('[data-toggle="tooltip"]').tooltip();
};
function changeDateTo2010() {
		mailleslayer.setStyle(styleDN2010);
		map.removeControl(legendEvol);
		map.removeControl(legendDN);
    legendDN.addTo(map);
    currentDate = "Carte2010";
	// réactivation des Tooltips
	$('[data-toggle="tooltip"]').tooltip();
};
function changeDateToEvol() {
		mailleslayer.setStyle(style_evol_DN);
		map.removeControl(legendEvol);
		map.removeControl(legendDN);
    legendEvol.addTo(map);
    currentDate = "CarteEvol";
	// réactivation des Tooltips
	$('[data-toggle="tooltip"]').tooltip();
};


// ajout du contrôle Toggle 1950 / 2010 / Evolution

L.control.custom({
    position: 'bottomright',
    content : '<div class="switch-toggle well">' +
                  '<input id="Carte1950" name="view3" type="radio">' +
                  '<label for="Carte1950" onclick="changeDateTo1950()">' +
                  '<span data-toggle="tooltip" data-placement="top" title="Densité en 1950"><span style="font-size:2em; vertical-align: middle; display: block; color:#58a374;" class="glyphicon glyphicon-tree-deciduous"></span>' +
                  '<span>Densité <span class="badge badge-info">1950</span></span></span></label>' +
                  '<input id="Carte2010" name="view3" type="radio" checked>' +
                  '<label for="Carte2010" onclick="changeDateTo2010()">' +
                  '<span data-toggle="tooltip" data-placement="top" title="Densité en 2010"><span style="font-size:2em; vertical-align: middle; display: block; color:#58a374;" class="glyphicon glyphicon-tree-deciduous"></span>' +
                  '<span>Densité <span class="badge badge-info">2010</span></span></span></label>' +
                  '<input id="CarteEvol" name="view3" type="radio">' +
                  '<label for="CarteEvol" onclick="changeDateToEvol()">' +
                  '<span data-toggle="tooltip" data-placement="top" title="Evolution Densité entre 1950 et 2010"><span style="font-size:2em; vertical-align: middle; display: block; color:#58a374;" class="glyphicon glyphicon-transfer"></span>' +
                  '<span>Evolution <span class="badge badge-info">1950-2010</span></span></span></label>' +
                  '<a style="background-color: #c6e26a !important" class="btn"></a>' +
               '</div>',

    classes : 'switch-toggle well',
    style   :
    {
        margin: '10px',
        height: '60px',
        width: '300px',
        padding: '0px 0 0 0',
        cursor: 'pointer',
    }
})
.addTo(map);


legendDN.addTo(map);



</script>
</body>
</html>
