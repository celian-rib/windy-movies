# Windy movies
> Victor JOSSO / Célian RIBOULET

> S3B

## Commandes custom :

> Générer des utilisateurs 

> (/!\ 1 user = un call API)

```
php bin/console app:generate-user <count>
```

> Générer des ratings 

> (count entre 300 et 1000 recommandé)

```
symfony console app:generate-rating <count>
```

***

## Se connecter au serveur mysql via tunnel ssh

```
ssh login@info-ssh1.iut.u-bordeaux.fr -L 7777:info-titania.iut.bx1:3306 -N
```
```
ssh criboulet@info-ssh1.iut.u-bordeaux.fr -L 7777:info-titania.iut.bx1:3306 -N
```
```
ssh vjosso@info-ssh1.iut.u-bordeaux.fr -L 7777:info-titania.iut.bx1:3306 -N
```

> Proxié sur 127.0.0.1:7777

## Commandes symfony

```
symfony serve
```

```
symfony console make:entity
```

## Ressources

[[Sujet]](https://gregwar.com/s3web/project.html#title.1)

![db](https://gregwar.com/s3web/img/db.png)

```
http://www.omdbapi.com/?i=tt3896198&apikey=38a1fd74
```

***

TODO

- [x] Page d'accueil présentant le site (+ Suggestion de séries)
- [x] Menu de navigation
- [x] Page à propos (Voir sujet)
- [x] Page Séries paginée
- [x] Page Série filtres par genre
- [x] Page info sur une séries (Saisons > épisodes)
- [x] Register (Avec captcha + vérifier champs)
- [x] Login
- [x] Pouvoir noter une série
- [x] Pouvoir suivre une série
- [x] Page séries suivis
- [x] Marquer un épisode comme vu
- [x] Pouvoir supprimer les commentaires en tant qu'admin
- [x] Mettre de nouvelles séries dans la base en tant qu'admin
- [x] Pouvoir générer des commentaires pour tester
- [ ] 404
- [x] Redirect sur les routes ou on doit être login et que l'on est pas login