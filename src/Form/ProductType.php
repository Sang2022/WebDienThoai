<?php

namespace App\Form;

use App\Entity\Product;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\width;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): Void
    {
        $builder
            ->add('ProductName', TextType::class) // ->remove('name')
            ->add('price',TextType::class)
            ->add('description',TextareaType::class)
            ->add('date',DateType::class,['widget' =>'single_text'])
            ->add('quantity')
            ->add('productImage', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
//                    new File([
//                        'maxSize' => '1024k',
////                        'mimeTypes' => [
////                            '/public/uploads/manga_image/jpg',
////                            '/public/uploads/manga_image/x-jpg',
////                        ],
//                        'mimesTypesMessage' => 'Please upload a valid image',
//                    ])
                ],
            ])
            ->add('category', EntityType::class,array('class'=>'App\Entity\Category','choice_label'=>"catName"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
