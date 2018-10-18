# Bd-Bocage-Outils
Partage d'outils autours de la valorisation des bases de données de haies (Bd Bocage) des PNR Normands

## Interfaces Bocage PNR Normandie-Maine

Les "développements" sont basés sur Leaflet + PostGis
2 interfaces sont mises à dispositions : 1 statistique et 1 diachronique (navigation synchronisée)


### Interface statistique

Fichier PHP principal : [lien](interfaces/bocage.php)

L'interface statistique interroge la base de données PostGis en créant des GeoJson basés sur des Vues Matérialisées (VM), ceci à différentes échelles d'analyse.

![Interface Densité](interfaces/bocage/interface_bocage_densite_200px.png)



### Interface diachronique

Fichier PHP principal : [lien](interfaces/bocage_diachronique.php)

L'interface diachronique affiche la Bd-Bocage depuis les flux WMS générés par Carmen.

Le plugin [Leaflet.Sync](https://github.com/jieter/Leaflet.Sync) est utilisé pour synchroniser la navigation entre les 2 cartes.

![Interface Diachronique](interfaces/bocage/interface_bocage_diachro_200px.png)
