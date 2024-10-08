# exeSymG2

Créé à partir du dossier :

https://github.com/WebDevCF2m2023/CoucouSymfonyG2

## Préparation de l'environnement de développement

Certains fichiers de configuration ont été modifiés pour correspondre à l'environnement de développement original.

Le `.env` a été modifié pour correspondre à la configuration de la base de données locale.

### Installation des dépendances

J'ai désinstallé `AssetMapper` pour que vous puissiez utiliser `Twig` plus simplement pour les assets.

Installation des dépendances :

    composer install


Ouvrez `Wamp` et lancez les services `Apache` et `MySQL`.

### Création de la base de données

    php bin/console doctrine:database:create

### Fichiers disponibles

Les `Entity` récupérés depuis le projet sont stockées dans le dossier `src/Entity`:

- `Comment.php`
- `Post.php`
- `Section.php`
- `Tag.php`
- `User.php`

Les `Repository` récupérés depuis le projet sont stockés dans le dossier `src/Repository`:

- `CommentRepository.php`
- `PostRepository.php`
- `SectionRepository.php`
- `TagRepository.php`
- `UserRepository.php`

Seul le controller `src/Controller/SecurityController.php` a été récupéré depuis le projet.

### Création des tables

    php bin/console make:migration

    php bin/console doctrine:migrations:migrate

## Votre objectif

Trouvez un template et intégrez-le dans le projet en utilisant Twig.

Vous pouvez utiliser le template de votre choix, mais il doit être responsive.

Vous pouvez utiliser un template pour le front et un autre (ou le même) pour le back-office.

Créez les contrôleurs nécessaires pour afficher les pages du template.

Créez un back-office pour gérer les articles et les sections.

Comme login et mot de passe, vous pouvez utiliser `admin` et `admin` dans la table `user`.

La permission `ROLE_ADMIN` doit être ajoutée à l'utilisateur `admin`.

Allez le plus loin possible en créant des `CRUD` pour les articles et les commentaires etc ...

### Fixtures pour nos données

On va créer de fausses données pour remplir notre database

Documentation :

https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html

Pour celà, on va charger :

    composer require --dev orm-fixtures

On va créer notre Fixture

    php bin/console make:fixtures

On va importer l'entité `User` pour faire des essais

```php

// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# Entité User
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // création d'une instance de User
        $user = new User();

        // création de l'administrateur via les setters
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('admin');

        // on prépare notre requête pour la transaction
        $manager->persist($user);

        // validation de la transaction
        $manager->flush();
    }
}

```