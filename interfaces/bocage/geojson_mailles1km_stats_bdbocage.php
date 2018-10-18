<?php
/*
 * Title:   PostGIS to GeoJSON
 * Notes:   Query a PostGIS table or view and return the results in GeoJSON format, suitable for use in OpenLayers, Leaflet, etc.
 * Author:  Bryan R. McBride, GISP
 * Contact: bryanmcbride.com
 * GitHub:  https://github.com/bmcbride/PHP-Database-GeoJSON
 */
# Connect to PostgreSQL database
$conn = new PDO('pgsql:host=localhost;dbname=pnrnm','user	','motdepasse');
# Build SQL SELECT statement and return the geometry as a GeoJSON element
$sql = "SELECT id_maille, nom_maille as nom_evolution, surf_pnr_ha, geojson, surf_hydro_ha, surf_forets_ha, surf_bati_ha, l_1945, 
       l_2000, l_2010, db_1945, db_2000, db_2010, dn_1945, dn_2000, 
       dn_2010
  FROM bocage.vm_grid_1000m_pnrnm_stats_bdbocage;";
/*
* If bbox variable is set, only return records that are within the bounding box
* bbox should be a string in the form of 'southwest_lng,southwest_lat,northeast_lng,northeast_lat'
* Leaflet: map.getBounds().toBBoxString()
*/
if (isset($_GET['bbox'])) {
    $bbox = explode(',', $_GET['bbox']);
    $sql = $sql . ' WHERE ST_Transform(geom, 4326) && ST_SetSRID(ST_MakeBox2D(ST_Point('.$bbox[0].', '.$bbox[1].'), ST_Point('.$bbox[2].', '.$bbox[3].')),4326);';
}
# Try query or error
$rs = $conn->query($sql);
if (!$rs) {
    echo 'An SQL error occured.\n';
    exit;
}
# Build GeoJSON feature collection array
$geojson = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);
# Loop through rows to build feature arrays
while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
    $properties = $row;
    # Remove geojson and geometry fields from properties
    unset($properties['geojson']);
    unset($properties['geom']);
    $feature = array(
         'type' => 'Feature',
         'geometry' => json_decode($row['geojson'], true),
         'properties' => $properties
    );
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}
header('Content-type: application/json');
echo json_encode($geojson, JSON_NUMERIC_CHECK);
$conn = NULL;
?>