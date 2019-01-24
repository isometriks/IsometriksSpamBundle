<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Type;

use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\EventListener\HoneypotValidationListener;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FormTypeHoneypotExtension extends AbstractTypeExtension
{
    private $translator;
    private $translationDomain;
    private $defaults;

    public function __construct(TranslatorInterface $translator,
                                $translationDomain,
                                array $defaults)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->defaults = $defaults;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['honeypot']) {
            return;
        }

        $builder
            ->setAttribute('honeypot_factory', $builder->getFormFactory())
            ->addEventSubscriber(new HoneypotValidationListener(
                $this->translator,
                $this->translationDomain,
                $options['honeypot_field'],
                $options['honeypot_message']
            ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['honeypot'] && !$view->parent && $options['compound']) {
            if ($form->has($options['honeypot_field'])) {
                throw new \RuntimeException(sprintf('Honeypot field "%s" is already in use.', $options['honeypot_field']));
            }

            $formOptions = array(
                'mapped' => false,
                'label' => false,
                'required' => false,
            );

            if ($options['honeypot_use_class']) {
                $formOptions['attr'] = array(
                    'class' => $options['honeypot_hide_class'],
                );
            } else {
                $formOptions['attr'] = array(
                    'style' => 'display:none',
                );
            }

            $formOptions['attr']['title'] = '';

            $factory = $form->getConfig()->getAttribute('honeypot_factory');
            $honeypotForm = $factory->createNamed($options['honeypot_field'], 'text', null, $formOptions);

            $view->children[$options['honeypot_field']] = $honeypotForm->createView($view);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'honeypot' => $this->defaults['global'],
            'honeypot_use_class' => $this->defaults['use_class'],
            'honeypot_hide_class' => $this->defaults['hide_class'],
            'honeypot_field' => $this->defaults['field'],
            'honeypot_message' => $this->defaults['message'],
        ));
    }

    /**
     * @inheritdoc
     */
    public static function getExtendedTypes()
    {
        return [
            FormType::class,
        ];
    }

    public function getExtendedType()
    {
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? FormType::class
            : 'form' // SF <2.8 BC
        ;
    }
}
