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
            new Rf\CellulR\EngineBundle\EngineBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            // ...
            $bundles[] = new Rf\CellulR\GeneratorBundle\GeneratorBundle();
        }
        // ...
    }

    // ...
}
```
Usage
-----

Vous pouvez utiliser la commande `generate:cell` pour générer une Cell. La commande prend trois arguments:

- `name`: ***(required)*** Le nom de la Cell
- `type`: ***(required) [component|page]*** Le type de la Cell
- `env`: ***[default: main]*** L'environnement de la Cell, à savoir le premier environnement choisi des assets (d'autres environnements peuvent être ajoutés par la suite)

Création d'une Cell `searchPage` de type `page` avec un environnement d'asset `main`

```sh
$ php app/console generate:cell searchPage page main
```

Création d'une Cell `searchBar` de type `component` avec un environnement d'asset `embed`

```sh
$ php app/console generate:cell searchBar component embed
```

A savoir
--------

> Les Cells sont générées à l'emplacement renseigné dans la configuration de l'EngineBundle.

> Si la Cell existe, le générateur vous demandera si vous voulez le remplacer.

> Lors de la génération d'une Cell de type `page`, le générateur vérifie si la classe Abstraite `AbstractPage` existe.
Si ce n'est pas le cas, il génère aussi cette classe. Tout Core Object de chaque Cell peut étendre cette classe pour récupérer la Request courante de Symfony. 

