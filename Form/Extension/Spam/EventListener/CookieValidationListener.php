<?php


namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\EventListener;

use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider\CookieProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class CookieValidationListener implements EventSubscriberInterface
{
    private $cookieProvider;
    private $request;
    private $translator;
    private $translationDomain;
    private $cookieName;
    private $errorMessage;

    public function __construct(CookieProvider $cookieProvider,
                                Request $request,
                                TranslatorInterface $translator,
                                $translationDomain,
                                $cookieName,
                                $errorMessage)
    {
        $this->cookieProvider = $cookieProvider;
        $this->request = $request;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->cookieName = $cookieName;
        $this->errorMessage = $errorMessage;
    }

    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        
        if($form->isRoot() && $form->getConfig()->getOption('compound') && !$this->request->cookies->get($this->cookieName)) {
            $errorMessage = $this->errorMessage;

            if (null !== $this->translator) {
                $errorMessage = $this->translator->trans($errorMessage, array(), $this->translationDomain);
            }

            $form->addError(new FormError($errorMessage));
            
            return;
        }
        
        $this->cookieProvider->removeAntispamCookie($this->cookieName);
    }  
    

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }
}