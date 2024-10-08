<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
#entite user 
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Section;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

use Faker\Factory as faker;

use function Symfony\Component\Clock\now;

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
            $users[]= $user;
            //on prepare notre requete pour la transation
            $manager->persist($user);
        }



        # insertions des post avec user au hasard en creant un tableau

        $faker = faker::create('fr-FR');
        
        for ($i = 1; $i < 100; $i++) {
            $post = new post();
            $keyUser = array_rand($users);
            $post->setUser($users[$keyUser]);
            $post->setPostDateCreated(new \DateTime('now - 30 days'));
            $publish = (mt_rand(0,3)<3 ? true:false);
            $post->setPostPublished($publish);
            if($publish){
                $day = mt_rand(1,25);
                $post->setPostDatePublished(new \DateTime('now - ' .$day. 'days'));
            }
            $title = $faker->words(mt_rand(2,4),true);
            $text = $faker->paragraphs(mt_rand(3,6),true);
            $post->setPostTitle(ucfirst($title));
            $post->setPostDescription(ucfirst($text));

            $posts[]=$post;

            $manager->persist($post);
        }

        #insert de section en les liant avec des postes au hasard  avec fake maker
        $faker = faker::create('fr-BE');
        for ($i = 1; $i < 3; $i++) {
            $section = new Section();
            $title = $faker->words(mt_rand(1,1),true);
            $text = $faker->text(mt_rand(10,20));
            $section->setSectionTitle($title);
            $section->setSectionDescription($text);
            $sections[]=$section;
            $manager->persist($section);
        }






        //validation de la transaction
        $manager->flush();
    }
}
