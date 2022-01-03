[Sujet](https://gregwar.com/s3web/project.html#title.1)

![db](https://gregwar.com/s3web/img/db.png)

TODO (Non trié)

- [ ] Page d'accueil présentant le site (+ Suggestion de séries)
- [x] Menu de navigation
- [ ] Page à propos (Voir sujet)
- [x] Page Séries paginée
- [x] Page Série filtres par genre
- [x] Page info sur une séries (Saisons > épisodes)
- [ ] Register (Avec captcha + vérifier champs)
- [x] Login
- [x] Pouvoir noter une série
- [x] Pouvoir suivre une série
- [ ] Page séries suivis
- [x] Marquer un épisode comme vu
- [ ] Pouvoir supprimer les commentaires en tant qu'admin
- [ ] Mettre de nouvelles séries dans la base en tant qu'admin
- [ ] Pouvoir générer des commentaires pour tester
- [ ] Redirect sur les routes ou on doit être login et que l'on est pas login


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

## Lancer le server symfony

```
symfony serve
```

symfony console make:entity

https://gregwar.com/s3web/img/db.png

http://www.omdbapi.com/?i=tt3896198&apikey=38a1fd74

php bin/console app:create-user