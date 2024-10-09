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

On va tester l'installation de notre fixture :

    php bin/console doctrine:fixtures:load

! Risque de suppression des anciennes données !

On va hacher le mot de passe avant l'envoi dans la DB

```php
<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# Entité User
use App\Entity\User;
# chargement du hacher de mots de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // Attribut privé contenant le hacheur de mot de passe
    private UserPasswordHasherInterface $hasher;

    // création d'un constructeur pour récupérer le hacher
    // de mots de passe
    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->hasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // création d'une instance de User
        $user = new User();

        // création de l'administrateur via les setters
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        // on va hacher le mot de passe
        $pwdHash = $this->hasher->hashPassword($user, 'admin');
        // passage mu mot de passe crypté
        $user->setPassword($pwdHash);

        // on prépare notre requête pour la transaction
        $manager->persist($user);

        // validation de la transaction
        $manager->flush();
    }
}
```

Puis tapez :

    php bin/console d:f:l

### Insertion de plusieurs users

```php
<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# Entité User
use App\Entity\User;
# chargement du hacher de mots de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // Attribut privé contenant le hacheur de mot de passe
    private UserPasswordHasherInterface $hasher;

    // création d'un constructeur pour récupérer le hacher
    // de mots de passe
    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->hasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        ###
        #   USER
        # INSERTION de l'admin avec mot de passe admin
        #
        ###
        // création d'une instance de User
        $user = new User();

        // création de l'administrateur via les setters
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        // on va hacher le mot de passe
        $pwdHash = $this->hasher->hashPassword($user, 'admin');
        // passage du mot de passe crypté
        $user->setPassword($pwdHash);

        // on prépare notre requête pour la transaction
        $manager->persist($user);
        
        // on va mettre dans une variable de type tableau
        // tous nos utilisateurs pour pouvoir leurs attribués
        // des Post ou des Comment
        $users[] = $user;

        ###
        #   USER
        # INSERTION de 10 utilisateurs en ROLE_USER
        # avec nom et mots de passe "re-tenables"
        #
        ###
        for($i=1;$i<=10;$i++){
            $user = new User();
            // username de : user0 à user10
            $user->setUsername('user'.$i);
            $user->setRoles(['ROLE_USER']);
            // hashage du mot de passe de : user0 à user10
            $pwdHash = $this->hasher->hashPassword($user, 'user'.$i);
            $user->setPassword($pwdHash);
            // on récupère les utilisateurs pour
            // les post et les comments
            $users[]=$user;
            $manager->persist($user);
        }
        
        
        
        ###
        #   POST
        # INSERTION de Post avec leurs users
        #
        ###


        // validation de la transaction
        $manager->flush();
    }
}
```

Puis tapez :

    php bin/console d:f:l

### On va insérer des `Post`

```php
<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# Entité User
use App\Entity\User;
# Entité Post
use App\Entity\Post;
# chargement du hacher de mots de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // Attribut privé contenant le hacheur de mot de passe
    private UserPasswordHasherInterface $hasher;

    // création d'un constructeur pour récupérer le hacher
    // de mots de passe
    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->hasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        ###
        #
        # INSERTION de l'admin avec mot de passe admin
        #
        ###
        // création d'une instance de User
        $user = new User();

        // création de l'administrateur via les setters
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        // on va hacher le mot de passe
        $pwdHash = $this->hasher->hashPassword($user, 'admin');
        // passage du mot de passe crypté
        $user->setPassword($pwdHash);

        // on va mettre dans une variable de type tableau
        // tous nos utilisateurs pour pouvoir leurs attribués
        // des Post ou des Comment
        $users[] = $user;

        // on prépare notre requête pour la transaction
        $manager->persist($user);

        ###
        #
        # INSERTION de 10 utilisateurs en ROLE_USER
        # avec nom et mots de passe "re-tenables"
        #
        ###
        for($i=1;$i<=10;$i++){
            $user = new User();
            // username de : user0 à user10
            $user->setUsername('user'.$i);
            $user->setRoles(['ROLE_USER']);
            // hashage du mot de passe de : user0 à user10
            $pwdHash = $this->hasher->hashPassword($user, 'user'.$i);
            $user->setPassword($pwdHash);
            // on récupère les utilisateurs pour
            // les post et les comments
            $users[]=$user;
            $manager->persist($user);
        }

        //dd($users);


        ###
        #   POST
        # INSERTION de Post avec leurs users
        #
        ###

        for($i=1;$i<=100;$i++){
            $post = new Post();
            // on prend une clef d'un User
            // créé au-dessus
            $keyUser = array_rand($users);
            // on ajoute l'ajoute l'utilisateur
            // à ce post
            $post->setUser($users[$keyUser]);
            // date de création (il y a 30 jours)
            $post->setPostDateCreated(new \dateTime('now - 30 days'));
            // Au hasard, on choisit si publié ou non (+-3 sur 4)
            $publish = mt_rand(0,3) <3;
            $post->setPostPublished($publish);
            if($publish) {
                $day = mt_rand(1, 25);
                $post->setPostDatePublished(new \dateTime('now - ' . $day . ' days'));
            }
            $post->setPostTitle("Post title ".$i);
            $post->setPostDescription('Post description '.$i);

            $manager->persist($post);
        }

        // validation de la transaction
        $manager->flush();
    }
}
```

Puis tapez :

    php bin/console d:f:l

### Installation de `Faker`

Vous le trouverez à cette adresse :

https://packagist.org/packages/fakerphp/faker

Sa documentation :

https://fakerphp.org/

```php
<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# Entité User
use App\Entity\User;
# Entité Post
use App\Entity\Post;
# chargement du hacher de mots de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

# chargement de Faker et Alias de nom
# pour utiliser Faker plutôt que Factory
# comme nom de classe
use Faker\Factory AS Faker;

class AppFixtures extends Fixture
{
    // Attribut privé contenant le hacheur de mot de passe
    private UserPasswordHasherInterface $hasher;

    // création d'un constructeur pour récupérer le hacher
    // de mots de passe
    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->hasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        ###
        #
        # INSERTION de l'admin avec mot de passe admin
        #
        ###
        // création d'une instance de User
        $user = new User();

        // création de l'administrateur via les setters
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        // on va hacher le mot de passe
        $pwdHash = $this->hasher->hashPassword($user, 'admin');
        // passage du mot de passe crypté
        $user->setPassword($pwdHash);

        // on va mettre dans une variable de type tableau
        // tous nos utilisateurs pour pouvoir leurs attribués
        // des Post ou des Comment
        $users[] = $user;

        // on prépare notre requête pour la transaction
        $manager->persist($user);

        ###
        #
        # INSERTION de 10 utilisateurs en ROLE_USER
        # avec nom et mots de passe "re-tenables"
        #
        ###
        for($i=1;$i<=10;$i++){
            $user = new User();
            // username de : user0 à user10
            $user->setUsername('user'.$i);
            $user->setRoles(['ROLE_USER']);
            // hashage du mot de passe de : user0 à user10
            $pwdHash = $this->hasher->hashPassword($user, 'user'.$i);
            $user->setPassword($pwdHash);
            // on récupère les utilisateurs pour
            // les post et les comments
            $users[]=$user;
            $manager->persist($user);
        }

        //dd($users);

        // Appel de faker avec la locale en français
        $faker = Faker::create('fr-FR');

        ###
        #   POST
        # INSERTION de Post avec leurs users
        #
        ###

        for($i=1;$i<=100;$i++){
            $post = new Post();
            // on prend une clef d'un User
            // créé au-dessus
            $keyUser = array_rand($users);
            // on ajoute l'ajoute l'utilisateur
            // à ce post
            $post->setUser($users[$keyUser]);
            // date de création (il y a 30 jours)
            $post->setPostDateCreated(new \dateTime('now - 30 days'));
            // Au hasard, on choisit si publié ou non (+-3 sur 4)
            $publish = mt_rand(0,3) <3;
            $post->setPostPublished($publish);
            if($publish) {
                $day = mt_rand(1, 25);
                $post->setPostDatePublished(new \dateTime('now - ' . $day . ' days'));
            }
            // création d'un titre entre 2 et 5 mots
            $title = $faker->words(mt_rand(2,5),true);
            // utilisation du titre avec le premier mot en majuscule
            $post->setPostTitle(ucfirst($title));

            // création d'un texte entre 3 et 6 paragraphes
            $texte = $faker->paragraphs(mt_rand(3,6), true);
            $post->setPostDescription($texte);

            // on va garder les posts
            // pour les Comment, Section et Tag
            $posts[]=$post;

            $manager->persist($post);
        }

        // validation de la transaction
        $manager->flush();
    }
}
```


Puis tapez :

    php bin/console d:f:l

### Fixtures finales

```php
<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# Entité User
use App\Entity\User;
# Entité Post
use App\Entity\Post;
# Entité Section
use App\Entity\Section;
# Entité Comment
use App\Entity\Comment;
# Entité Tag
use App\Entity\Tag;

# chargement du hacher de mots de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

# chargement de Faker et Alias de nom
# pour utiliser Faker plutôt que Factory
# comme nom de classe
use Faker\Factory AS Faker;

class AppFixtures extends Fixture
{
    // Attribut privé contenant le hacheur de mot de passe
    private UserPasswordHasherInterface $hasher;

    // création d'un constructeur pour récupérer le hacher
    // de mots de passe
    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->hasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        ###
        #
        # INSERTION de l'admin avec mot de passe admin
        #
        ###
        // création d'une instance de User
        $user = new User();

        // création de l'administrateur via les setters
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        // on va hacher le mot de passe
        $pwdHash = $this->hasher->hashPassword($user, 'admin');
        // passage du mot de passe crypté
        $user->setPassword($pwdHash);

        // on va mettre dans une variable de type tableau
        // tous nos utilisateurs pour pouvoir leurs attribués
        // des Post ou des Comment
        $users[] = $user;

        // on prépare notre requête pour la transaction
        $manager->persist($user);

        ###
        #
        # INSERTION de 10 utilisateurs en ROLE_USER
        # avec nom et mots de passe "re-tenables"
        #
        ###
        for($i=1;$i<=10;$i++){
            $user = new User();
            // username de : user0 à user10
            $user->setUsername('user'.$i);
            $user->setRoles(['ROLE_USER']);
            // hashage du mot de passe de : user0 à user10
            $pwdHash = $this->hasher->hashPassword($user, 'user'.$i);
            $user->setPassword($pwdHash);
            // on récupère les utilisateurs pour
            // les post et les comments
            $users[]=$user;
            $manager->persist($user);
        }

        //dd($users);

        // Appel de faker avec la locale en français
        // de France
        $faker = Faker::create('fr_FR');

        ###
        #   POST
        # INSERTION de Post avec leurs users
        #
        ###

        for($i=1;$i<=100;$i++){
            $post = new Post();
            // on prend une clef d'un User
            // créé au-dessus
            $keyUser = array_rand($users);
            // on ajoute l'utilisateur
            // à ce post
            $post->setUser($users[$keyUser]);
            // date de création (il y a 30 jours)
            $post->setPostDateCreated(new \dateTime('now - 30 days'));
            // Au hasard, on choisit s'il est publié ou non (+-3 sur 4)
            $publish = mt_rand(0,3) <3;
            $post->setPostPublished($publish);
            if($publish) {
                $day = mt_rand(3, 25);
                $post->setPostDatePublished(new \dateTime('now - ' . $day . ' days'));
            }
            // création d'un titre entre 2 et 5 mots
            $title = $faker->words(mt_rand(2,5),true);
            // utilisation du titre avec le premier mot en majuscule
            $post->setPostTitle(ucfirst($title));

            // création d'un texte entre 3 et 6 paragraphes
            $texte = $faker->paragraphs(mt_rand(3,6), true);
            $post->setPostDescription($texte);

            // on va garder les posts
            // pour les Comment, Section et Tag
            $posts[]=$post;

            $manager->persist($post);

        }

        ###
        #   SECTION
        # INSERTION de Section en les liants
        # avec des postes au hasard
        #
        ###

        for($i=1;$i<=6;$i++){
            $section = new Section();
            // création d'un titre entre 2 et 5 mots
            $title = $faker->words(mt_rand(2,5),true);
            $section->setSectionTitle(ucfirst($title));
            // création d'une description de maximum 500 caractères
            // en pseudo français di fr_FR
            $description = $faker->realText(mt_rand(150,500));
            $section->setSectionDescription($description);

            // On va mettre dans une variable le nombre total d'articles
            $nbArticles = count($posts);
            // on récupère un tableau d'id au hasard (on commence
            // à car si on obtient un seul id, c'est un int et pas un array
            $articleID = array_rand($posts, mt_rand(2,$nbArticles));

            // Attribution des articles
            // à la section en cours
            foreach($articleID as $id){
                // entre 1 et 100 articles
                $section->addPost($posts[$id]);
            }

            $manager->persist($section);
        }

        ###
        #   COMMENT
        # INSERTION de Comment en les liants
        # avec des Post au hasard et des User
        #
        ###
        // on choisit le nombre de commentaires entre 250 et 350
        $commentNB = mt_rand(250,350);
        for($i=1;$i<=$commentNB;$i++){

            $comment = new Comment();
            // on prend une clef d'un User
            // créé au-dessus au hasard, envoie l'id en int
            $keyUser = array_rand($users);
            // on ajoute l'utilisateur
            // à ce commentaire
            $comment->setUser($users[$keyUser]);
            // on prend une clef d'un Post
            // créé au-dessus au hasard
            $keyPost = array_rand($posts);
            // on ajoute l'article
            // de ce commentaire
            $comment->setPost($posts[$keyPost]);
            // écrit entre 1 et 48 heures
            $hours = mt_rand(1,48);
            $comment->setCommentDateCreated(new \dateTime('now - ' . $hours . ' hours'));
            // entre 150 et 1000 caractères
            $comment->setCommentMessage($faker->realText(mt_rand(150,1000)));
            // Au hasard, on choisit s'il est publié ou non (+-3 sur 4)
            $publish = mt_rand(0,3) <3;
            $comment->setCommentPublished($publish);

            $manager->persist($comment);
        }

        ###
        #   Tag
        # INSERTION de 45 Tag en les liants
        # avec des Post au hasard
        #
        ###
        for($i=1;$i<=45;$i++){
            $tag = new Tag();
            # création d'un slug par Faker
            $tag->setTagName($faker->slug(mt_rand(1,3), true));
            # on compte le nombre d'articles
            $nbArticles = count($posts);
            # on en prend 1/5
            $PostNB = (int) round($nbArticles/5);
            # On en choisit au hasard avec maximum 20 tags ($nbArticles/5) = 100/5
            # On choisit 2 articles minimum au hasard sinon on récupère un int
            # et non pas un array
            $articleID = array_rand($posts, mt_rand(2,$PostNB));
            foreach($articleID as $id){
                // on ajoute l'article au tag
                $tag->addPost($posts[$id]);
            }
            $manager->persist($tag);
        }

        // validation de la transaction
        $manager->flush();
    }
}
```