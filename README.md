[Sujet](https://gregwar.com/s3web/project.html#title.1)

TODO (Non trié)

- [ ] Page d'accueil présentant le site (+ Suggestion de séries)
- [ ] Menu de navigation
- [ ] Page à propos (Voir sujet)
- [ ] Page Séries paginée
- [ ] Page Série filtres par genre
- [ ] Page info sur une séries (Saisons > épisodes)
- [ ] Register (Avec captcha + vérifier champs)
- [ ] Login
- [ ] Pouvoir noter une série
- [ ] Pouvoir suivre une série
- [ ] Page séries suivis
- [ ] Marquer un épisode comme vu
- [ ] Pouvoir supprimer les commentaires en tant qu'admin
- [ ] Mettre de nouvelles séries dans la base en tant qu'admin
- [ ] Pouvoir générer des commentaires pour tester

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


