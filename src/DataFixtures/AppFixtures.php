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
            $manager->persist($user);
        }


        // validation de la transaction
        $manager->flush();
    }
}