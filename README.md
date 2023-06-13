Pour fonctionner, l'application necessite:
une base de données MySQL,
l'application VueJS,
l'API CodeIgniter.




Pour faire fonctionner ces projets, il est necessaire de disposer de composer (PHP)
et de node (JS)

Installation des prérequis: 
XAMPP: https://www.apachefriends.org/index.html
Composer : https://getcomposer.org/download/
Initialisation du depot git:
git init
git clone <reposetory (SSH or HTTPS)>

Import de la base de données:
- Necessite MySQL (fourni avec XAMPP) et phpMyAdmin
- Importer le script fourni
- En cas de soucis, remplacer l'encodage des tables dans le script par: utf8mb4_unicode_ci

Pour l'API:
- Cloner le depot,
- Éxecuter : composer install
- Copier le fichier env, le renommer en .env et le mettre à jour
- Lancer l'API: php spark serve

Pour l'application:
- Cloner le depot
- Éxecuter : npm install
- Lancer l'appli en developement: npm run dev

Dans les deux cas, le fichier d'environement .env est a editer afin de faire correspondre les adresses de la base de données, de l'API et de l'application.
Les tests de l'API on été réalisés sur API Talend Tester, et sont privés.

Extension Chrome Talend Tester : https://chrome.google.com/webstore/detail/talend-api-tester-free-ed/aejoelaoggembcahagimdiliamlcdmfm
