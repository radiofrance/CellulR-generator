Getting started: GeneratorBundle
=============================

Télécharger le bundle
---------------------

```
$ composer require webcomponents/generator-bundle:dev-features/v3
```

Activer le bundle
-----------------

```php
<?php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Dunglas\ActionBundle\DunglasActionBundle(),
            new Rf\WebComponent\EngineBundle\EngineBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            // ...
            $bundles[] = new Rf\WebComponent\GeneratorBundle\GeneratorBundle();
        }
        // ...
    }

    // ...
}
```
Usage
-----

Vous pouvez utiliser la commande `generate:wc` pour générer un Web Component. La commande prend trois arguments:

- `name`: ***(required)*** Le nom du Web Component
- `type`: ***(required) [component|page]*** Le type du Web Component
- `env`: ***[default: main]*** L'environnement du Web Component, à savoir le premier environnement choisi des assets (d'autres environnements peuvent être ajoutés par la suite)

Création d'un Web Component `searchPage` de type `page` avec un environnement d'asset `main`

```sh
$ php app/console generate:wc searchPage page main
```

Création d'un Web Component `searchBar` de type `component` avec un environnement d'asset `embed`

```sh
$ php app/console generate:wc searchBar component embed
```

A savoir
--------

> Les Web Components sont générés à l'emplacement renseigné dans la configuration de l'EngineBundle.

> Si le Web Component existe, le générateur vous demandera si vous voulez le remplacer.

> Lors de la génération d'un Web Component de type `page`, le générateur vérifie si la classe Abstraite `AbstractPage` existe.
Si ce n'est pas le cas, il génère aussi cette classe. Toute View Object de chaque Web Component peut étendre cette classe pour récupérer la Request courante de Symfony. 

