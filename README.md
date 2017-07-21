
#  Widget Carto_HAL Wordpress #

Widget pour Wordpress 4.8 permettant de cartographier les pays publiant dans une collection HAL.


Le Widget s'installe de cette manière:

	git clone git@bitbucket.org:arnouldpy/carto_hal-wordpress.git

Si vous souhaitez lui apporter des modifications, vous devez recompiler le projet angular:

Prerequis nodejs installé.

	-Placez vous a la racine du projet et executer npm run js

Si vous souhaitez juste le deployer:

Executez cette commande:

zip -r Widget_carto_hal.zip  Widget_carto_hal.php app/app.min.js app/templates/ app/js/ app/ConfigDefault.js css/ 

Uploader le Zip génèrer dans le backend de wordpress:

Rendez vous dans Plugins, Upload et uploadez le fichier .zip

Une fois cela fait, rendez vous dans Appaerance/widgets, et selectionnez Widget_carto_hal, glissez deposez ou vous le souhaitez.

Vous pouvez configurer le widget en suivant les instructions sur le backend.
