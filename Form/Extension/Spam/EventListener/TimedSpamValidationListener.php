<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\EventListener; 

use Symfony\Component\EventDispatcher\EventSubscriberInterface; 
use Symfony\Component\Form\FormEvents; 
use Symfony\Component\Form\FormEvent; 
use Symfony\Component\Form\FormError; 
use Symfony\Component\Translation\TranslatorInterface; 
use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider\TimedSpamProviderInterface;


class TimedSpamValidationListener implements EventSubscriberInterface
{
    private $timeProvider; 
    private $errorMessage; 
    private $translator; 
    private $translationDomain; 
    private $options; 
    
    public function __construct(TimedSpamProviderInterface $timeProvider, 
                                TranslatorInterface $translator, 
                                $translationDomain, 
                                $errorMessage, 
                                $options)
    {
        $this->timeProvider = $timeProvider;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->errorMessage = $errorMessage;
        $this->options = $options;
    }
    
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm(); 
        $data = $event->getData(); 
        
        if ($form->isRoot() &&
            $form->getConfig()->getOption('compound') &&
            !$this->timeProvider->isFormTimeValid($form->getName(), $this->options)) {

            $errorMessage = $this->errorMessage; 

            if (null !== $this->translator) {
                $errorMessage = $this->translator->trans($errorMessage, array(), $this->translationDomain); 
            }

            $form->addError(new FormError($errorMessage)); 
        }
        
        $event->setData($data); 
    }    
    
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',     
        ); 
    }    
}