<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Section;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    private array $brutData;

    // Constructeur avec injection de dépendance pour le TagRepository
    public function __construct(
        private TagRepository $tagRepository
    ) {
        $this->brutData = []; // Initialisation du tableau pour stocker les données brutes des tags
    }

    // Construction du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('postTitle') // Champ pour le titre du post
            ->add('postDescription') // Champ pour la description du post
            ->add('postDateCreated', null, [ // Champ pour la date de création du post
                'widget' => 'single_text',
                'empty_data' => date('Y-m-d H:i:s'), // Valeur par défaut à la date et heure actuelle
                'required' => false,
            ])
           
            ->add('postPublished') // Champ pour indiquer si le post est publié
          
        
            ->add('user', EntityType::class, [ // Champ pour sélectionner l'utilisateur
                'class' => User::class,
                'choice_label' => 'username',
            ])
        ;
    }

    // Méthode exécutée avant la soumission du formulaire
    private function preSubmit(FormEvent $event): void {
        $data = $event->getData(); // Récupération des données du formulaire
        if (isset($data['tags']) && is_array($data['tags'])) {
            $this->brutData = $data['tags']; // Stocke les tags bruts
            $data['tags'] = array_filter($data['tags'], 'ctype_digit'); // Filtre pour garder seulement les tags numériques
            $event->setData($data); // Met à jour les données du formulaire
        }
    }

    // Méthode exécutée lors de la soumission du formulaire
    private function submit(FormEvent $event): void {
        $form = $event->getForm();
        /** @var Post $post */
        $post = $form->getData(); // Récupération des données du post
        $data = $this->brutData;
        foreach ($data as $tagName) {
            if(ctype_digit($tagName)) continue; // Ignore si le tag est déjà numérique
            $tagName = htmlspecialchars($tagName); // Sécurise le nom du tag
            $length = strlen($tagName);
            /* Sécurité côté back-end */
            if(!($length > 1 && $length <= 60)){ // Vérifie que la longueur du tag est correcte
                $message = $length < 1 ? "supérieur à 1" : ($length > 60 ? "inférieur à 60" : "ERROR");
                $form->addError(new FormError("Le nom du tag $tagName doit être $message caractères.")); // Ajoute une erreur si le tag n'est pas valide
                return;
            }
            if($this->tagRepository->findOneBy(['tagName' => $tagName]) !== null) continue; // Ignore si le tag existe déjà dans la base de données
            $tag = new Tag();
            $tag->setTagName($tagName); // Définit le nom du nouveau tag
            $post->addTag($tag); // Ajoute le tag au post
        }
    }

    // Configuration des options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class, // Définit la classe associée au formulaire
        ]);
    }
}
