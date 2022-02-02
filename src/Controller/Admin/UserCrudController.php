<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id')->onlyOnIndex(),
            TextField::new('nickName')->setLabel('Pseudo'),
            TextField::new('password')->setLabel('Mot de passe')->onlyWhenCreating(),
            TextField::new('lastName')->setLabel('Nom de famille'),
            TextField::new('firstName')->setLabel('Prénom'),
            TextField::new('phoneNumber')->setLabel('Numéro de téléphone'),
            TextField::new('email')->setLabel('Email'),
            BooleanField::new('isAdmin')->setLabel('Administrateur ?'),
            BooleanField::new('isActive')->setLabel('Actif ?'),
            AssociationField::new('organizedOutings')->setLabel('Sorties organisées par l\'utilisateur' ),
            AssociationField::new('outings')->setLabel('Participations aux sorties'),
            AssociationField::new('campus')->setLabel('Campus'),
            TextField::new('imageFile')->setFormType(VichImageType::class),
            ImageField::new('imageName')->setBasePath('uploads/profile_pictures/')->onlyOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('detail', 'Détails d\'un participant')
            ->setPageTitle('edit', 'Modifier un participant')
            ->setPageTitle('index', 'Liste des participants')
            ->setPageTitle('new', 'Créer un participant')
            ->setEntityLabelInSingular('Participant')
            ->setEntityLabelInPlural('Participants')
            ->setDefaultSort(['nickName' => 'ASC']);
    }
}
