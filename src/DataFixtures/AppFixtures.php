<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
#entite user 
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $UserPasswordHasher)
    {
        $this->hasher = $UserPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        // creation de l'admin
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $pwdHash = $this->hasher->hashPassword($user,'admin');
        $user->setPassword($pwdHash); 
        //on prepare notre requete pour la transation
        $manager->persist($user);


        #insertion de 10 users
        for ($i = 1; $i < 10; $i++) {
            $user = new User();
            $user->setUsername('user'.$i);
            $user->setRoles(['ROLE_USER']);
            $pwdHash = $this->hasher->hashPassword($user,'user'.$i);
            $user->setPassword($pwdHash);
            //on prepare notre requete pour la transation
            $manager->persist($user);
        }


        //validation de la transaction
        $manager->flush();
    }
}
