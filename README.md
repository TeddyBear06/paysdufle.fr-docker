# Le site du Pays du FLE

## Quickstart

Pré-requis :

```bash
$ ./portainer-run.sh
```

[1/2] Ensuite, copier le fichier contenant les variables d'environnement :

```bash
$ cp .env.example .env
```

[OPTIONNEL] Basculer APP_ENV à "local" ou "prod" en fonction du contexte d'exécution.

[2/2] Construire et lancer les containers :

```bash
$ ./stack-run.sh
```

Enjoy!