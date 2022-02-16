<?php

namespace App\Controller\Admin;

use App\Entity\Town;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TownCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Town::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('postalCode')->setLabel('Code Postal'),
            AssociationField::new('places')->setLabel('Lieux associés')->onlyOnIndex()
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('detail', 'Détails d\'une ville')
            ->setPageTitle('edit', 'Modifier une ville')
            ->setPageTitle('index', 'Liste des villes')
            ->setPageTitle('new', 'Créer une ville')
            ->setEntityLabelInSingular('Ville')
            ->setEntityLabelInPlural('Villes');
    }
}
