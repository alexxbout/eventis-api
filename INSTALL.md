# Installation de l'application


Pour fonctionner, l'application necessite:
- une base de données MySQL,
- l'application VueJS,
- l'API CodeIgniter.


## Installation des prérequis:
>XAMPP: 
>>     https://www.apachefriends.org/index.html
>
>Composer : 
>>     https://getcomposer.org/download/
>Node : 
>>     https://nodejs.org/en/download
>Initialisation du depot git: (a faire pour l'API et l'application)<br />
>>     git init
>>     git clone <repository (SSH or HTTPS)
>Initialisation du depot git: (a faire pour l'API et l'application)<br />
>>     git init
>>     git clone <repository (SSH or HTTPS)
>Base de données: (via phpMyAdmin si disponible)
>
>>     mysql -u username -p database_name < script.sql
> En cas de soucis, remplacer l'encodage des tables dans le script par: 
>>     utf8mb4_unicode_ci
>

## Installation de l'application et de l'API

<br/>

### API:
>Initialisation de la configuration
>>     composer install
>Pour lancer l'API:
>>     php spark serve
<br/><br/>

### Application:
>Initialisation de la configuration
>>     npm install
>Lancer l'appli en developement:
>>     npm run -dev
<br/><br/>


## Configuration
<br/>

> Faire correspondre les bonnes adresses avec les bonnes variables
> dans le fichier `.env`: <br/>
>> Adresse de l'API dans l'application<br/>
>> Adresse de la base de données dans l'API<br/>  

## Tests
<br/>
Les tests de l'API on été réalisés sur [Talend API Tester](https://chrome.google.com/webstore/detail/talend-api-tester-free-ed/aejoelaoggembcahagimdiliamlcdmfm ) <br/>
Le contenu des tests est à récupérer auprès des membres du projet
