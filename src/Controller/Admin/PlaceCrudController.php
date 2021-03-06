<?php

namespace App\Controller\Admin;

use App\Entity\Place;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlaceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Place::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('street')->setLabel('Rue'),
            NumberField::new('latitude'),
            NumberField::new('longitude'),
            AssociationField::new('outings')->setLabel('Sorties Associées')->onlyOnIndex(),
            AssociationField::new('town')->setLabel('Ville')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('detail', 'Détails d\'un lieu')
            ->setPageTitle('edit', 'Modifier un lieu')
            ->setPageTitle('index', 'Liste des lieux')
            ->setPageTitle('new', 'Créer un lieu')
            ->setEntityLabelInSingular('Lieu')
            ->setEntityLabelInPlural('Lieux');
    }
}
