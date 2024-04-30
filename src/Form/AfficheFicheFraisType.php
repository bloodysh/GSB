<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AfficheFicheFraisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fichesFrais', ChoiceType::class, [
                'choices'=>$options['data'],
                'choice_label'=>function ($choice): string {
                    $aString = $choice->getMois();
                    if (strlen($aString) >= 4) {
                        $laDate = str_split($aString, 4);
                        $theActualDate = $laDate[0] . "/" . $laDate[1];
                        return $theActualDate;
                    } else {
                        // Handle the case where $aString is less than 4 characters long
                        // You might want to return a default value or throw an exception
                        return $aString;
                    }
                }
            ])
            ->add('submit', SubmitType::class, [
                'label'=>'Submit'
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'ficheFrais' => false,
        ]);
    }
}

