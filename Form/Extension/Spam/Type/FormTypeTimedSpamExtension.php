<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\EventListener\TimedSpamValidationListener;

class FormTypeTimedSpamExtension extends AbstractTypeExtension
{
    private $translator;
    private $translationDomain;

    public function __construct(TranslatorInterface $translator, $translationDomain)
    {
        $this->translator        = $translator;
        $this->translationDomain = $translationDomain;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['timed_spam']) {
            return;
        }

        $builder
            ->setAttribute('timed_spam_factory', $builder->getFormFactory())
            ->addEventSubscriber(new TimedSpamValidationListener(
                $this->translator, 
                $this->translationDomain, 
                $options['timed_spam_message'],
                $options['timed_spam_min']
            ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['timed_spam'] && !$view->parent && $options['compound']) {
            $factory = $form->getConfig()->getAttribute('timed_spam_factory'); 
            $data = base64_encode(time()); 
            
            $spamForm = $factory->createNamed('_timed_spam', 'hidden', $data, array(
                'mapped' => false, 
            )); 
            
            $view->children['_timed_spam'] = $spamForm->createView($view); 
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'timed_spam' => false,
            'timed_spam_min' => 7, 
            'timed_spam_message' => 'You are doing that too quickly.',
        ));
    }

    public function getExtendedType()
    {
        return 'form';
    }
}