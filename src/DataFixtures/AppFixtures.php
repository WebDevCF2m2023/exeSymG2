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
# Entité tag
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
            // on ajoutel'utilisateur
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

            // création d'un texte entre 3 et 6 text
            $texte = $faker->realText(mt_rand(10,500), true);
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


        for($i=1;$i<=2;$i++){
            $section = new Section();
            // création d'un titre entre 2 et 5 mots
            $title = $faker->words(mt_rand(1,1),true);
            $section->setSectionTitle(ucfirst($title));
            // création d'une description de maximum 500 caractères
            // en pseudo français di fr_FR
            $description = $faker->realText(mt_rand(150,500));
            $section->setSectionDescription($description);

            // On va mettre dans une variable le nombre total d'articles
            $nbArticles = count($posts);
            // on récupère un tableau d'id au hasard
            $articleID = array_rand($posts, mt_rand(1,$nbArticles));

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
            // créé au-dessus au hasard
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
        #   tag
        # INSERTION de tag en les liants
        # avec des Post au hasard
        #
        ###

        for ($i = 1; $i < 45; $i++) {
            $tag = new Tag();
            $tag->setTagName($faker->realText(mt_rand(10,15),true));
            $nbpost = count($posts);
            $postID = array_rand($posts,mt_rand(1,$nbpost));
            foreach($postID as $id){
                $tag->addPost($posts[$id]);
            }
            $manager->persist($tag);
        }

        // validation de la transaction
        $manager->flush();
    }
}