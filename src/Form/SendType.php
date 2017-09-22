<?php

namespace Hgabka\KunstmaanEmailBundle\Form;

use Hgabka\KunstmaanExtensionBundle\Form\Type\DateTimepickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'hgabka_kuma_email.labels.send_at',
                'choices' => [
                    'hgabka_kuma_email.labels.send_now'   => 'now',
                    'hgabka_kuma_email.labels.send_later' => 'later',
                ]
            ])
            ->add('time', DateTimepickerType::class, [
                'label' => 'hgabka_kuma_email.labels.send_time',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => true,
        ]);
    }
}