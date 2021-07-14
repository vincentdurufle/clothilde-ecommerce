<?php

namespace App\Controller\Admin;

use App\Entity\Collaboration;
use App\Form\ImageType;
use App\Form\VichImageField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CollaborationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Collaboration::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextEditorField::new('text'),
            BooleanField::new('disabled'),
            ImageField::new('cover')->setBasePath($this->getParameter('app.path.item_cover_images'))->onlyOnIndex(),
            VichImageField::new('coverFile')->hideOnIndex(),
            CollectionField::new('images')
                ->onlyOnForms()
                ->setEntryType(ImageType::class)
                ->addWebpackEncoreEntries('admin_index')
                ->addCssClass('slider-image'),
        ];
    }
}
