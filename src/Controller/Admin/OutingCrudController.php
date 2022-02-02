<?php

namespace App\Controller\Admin;

use App\Entity\Outing;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OutingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Outing::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name')->setLabel('Nom'),
            DateTimeField::new('startAt')->setLabel('Date de début'),
            IntegerField::new('duration')->setLabel('Durée (en mn)'),
            DateTimeField::new('limitSubscriptionDate')->setLabel('Date limite d\'inscription'),
            IntegerField::new('maxUsers')->setLabel('Participants MAX'),
            AssociationField::new('users')->setLabel('Inscrits')->onlyOnIndex(),
            AssociationField::new('organizer')->setLabel('Organisateur'),
            AssociationField::new('campus'),
            AssociationField::new('place')->setLabel('Lieu'),
            AssociationField::new('status')->setLabel('Satut'),
            TextareaField::new('about')->setLabel('A propos')
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('detail', 'Détails d\'une sortie')
            ->setPageTitle('edit', 'Modifier une sortie')
            ->setPageTitle('index', 'Liste des sorties')
            ->setPageTitle('new', 'Créer une sortie')
            ->setEntityLabelInSingular('Sortie')
            ->setEntityLabelInPlural('Sorties');
    }
}
