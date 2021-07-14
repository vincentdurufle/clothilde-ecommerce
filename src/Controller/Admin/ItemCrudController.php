<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use App\Form\ImageType;
use App\Form\VichImageField;
use App\Service\Constants\ItemType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            ChoiceField::new('type')->setChoices(ItemType::ALL),
            TextField::new('size')->onlyOnForms(),
            TextField::new('composition_en')->onlyOnForms(),
            TextField::new('composition_fr')->onlyOnForms(),
            TextField::new('size'),
            TextareaField::new('description_en')->onlyOnForms(),
            TextareaField::new('description_fr')->onlyOnForms(),
            MoneyField::new('price')->setCurrency('EUR')->setStoredAsCents(false),
            MoneyField::new('shippingFee')->setCurrency('EUR')->setStoredAsCents(false),
            MoneyField::new('shippingFeeEurope')->setCurrency('EUR')->setStoredAsCents(false),
            MoneyField::new('shippingFeeWorld')->setCurrency('EUR')->setStoredAsCents(false),
            BooleanField::new('sold'),
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
