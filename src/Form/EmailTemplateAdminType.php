<?php


namespace Hgabka\KunstmaanEmailBundle\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Hgabka\KunstmaanEmailBundle\Entity\EmailLayout;
use Hgabka\KunstmaanSettingsBundle\Choices\SettingTypes;
use Hgabka\KunstmaanSettingsBundle\Entity\Setting;
use Hgabka\KunstmaanSettingsBundle\Helper\SettingsManager;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Hgabka\KunstmaanExtensionBundle\Form\Type\StaticControlType;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EmailTemplateAdminType extends AbstractType
{
    /** @var AuthorizationChecker  */
    private $authChecker;

    public function __construct(AuthorizationChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, ['label' => 'hgabka_kuma_email.labels.name', 'required' => true])
            ->add('comment',TextareaType::class, ['label' => 'hgabka_kuma_email.labels.comment'])
        ;
        if ($this->authChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $builder->add('slug', TextType::class, ['label' => 'hgabka_kuma_email.labels.slug']);
            $builder->add('isSystem', CheckboxType::class, ['label' => 'hgabka_kuma_email.labels.is_system']);
        }
        $builder->add('layout', EntityType::class, ['label' => 'hgabka_kuma_settings.labels.layout', 'class' => EmailLayout::class]);
        $builder->add('translations', TranslationsType::class, [
            'label' => false,
            'fields' => [
                'subject' => [
                    'field_type' => TextType::class,
                    'label' => 'hgabka_kuma_email.labels.subject',
                ],
                'contentText' => [
                    'field_type' => TextareaType::class,
                    'label' => 'hgabka_kuma_email.labels.content_text',
                ],
                'contentHtml' => [
                    'field_type' => WysiwygType::class,
                    'label' => 'hgabka_kuma_email.labels.content_html',
                ],
            ]
            ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'hgabka_kunstmaanemail_email_template_type';
    }
}