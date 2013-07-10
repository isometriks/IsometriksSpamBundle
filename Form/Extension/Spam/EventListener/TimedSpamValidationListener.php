<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\EventListener; 

use Symfony\Component\EventDispatcher\EventSubscriberInterface; 
use Symfony\Component\Form\FormEvents; 
use Symfony\Component\Form\FormEvent; 
use Symfony\Component\Form\FormError; 
use Symfony\Component\Translation\TranslatorInterface; 


class TimedSpamValidationListener implements EventSubscriberInterface
{
    private $errorMessage; 
    private $translator; 
    private $translationDomain; 
    private $minTime; 
    
    public function __construct(TranslatorInterface $translator, $translationDomain, $errorMessage, $minTime)
    {
        $this->translator = $translator; 
        $this->translationDomain = $translationDomain; 
        $this->errorMessage = $errorMessage; 
        $this->minTime = $minTime; 
    }
    
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm(); 
        $data = $event->getData(); 
        
        if ($form->isRoot() && $form->getConfig()->getOption('compound')) {
            if(!isset($data['_timed_spam']) || !$this->timeValid($data['_timed_spam'])){
                $errorMessage = $this->errorMessage; 

                if (null !== $this->translator) {
                    $errorMessage = $this->translator->trans($errorMessage, array(), $this->translationDomain); 
                }
            
                $form->addError(new FormError($errorMessage)); 
            }
            
            if(is_array($data)) {
                unset($data['_timed_spam']); 
            }
        }
        
        $event->setData($data); 
    }    
    
    protected function timeValid($value)
    {
        $time = base64_decode($value); 
        
        return ctype_digit($time) && (time() - $time) > $this->minTime; 
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',     
        ); 
    }    
}