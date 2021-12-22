## Se connecter au serveur mysql via tunnel ssh

```
ssh login@info-ssh1.iut.u-bordeaux.fr -L 7777:info-titania.iut.bx1:3306 -N
```

> Proxié sur 127.0.0.1:7777

## Lancer le server symfony

```
symfony serve
```
