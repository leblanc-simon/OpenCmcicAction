OpenCmcicAction
===============

Bibliothèque destiné a effectuer des actions au niveau d'un TPE virtuel, essentiellement sur des paiements récurrents

Installation
------------

Voir le fichier composer.json

Utilisation
-----------

Se baser sur le fichier config/config.template.php pour mettre en place la configuration

Une fois la configuration paramétrée, vous pouvez utiliser l'une des 3 requête :
 * Recouvrement : Recouvrement d'un paiement
 * Cancel : Annulation d'une récurrence de paiement
 * Paiement : Récupération des données pour les paiements ayant eu lieu entre 2 dates

Licence
-------

[MIT](http://opensource.org/licenses/MIT)