<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\EventListener;

use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider\TimedSpamProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;


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

        if ($form->isRoot() &&
            $form->getConfig()->getOption('compound') &&
            !$this->timeProvider->isFormTimeValid($form->getName(), $this->options)) {

            $errorMessage = $this->errorMessage;

            if (null !== $this->translator) {
                $errorMessage = $this->translator->trans($errorMessage, array(), $this->translationDomain);
            }

            $form->addError(new FormError($errorMessage));
        }

        /**
         * Remove the stored time
         */
        $this->timeProvider->removeFormTime($form->getName());
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }
}