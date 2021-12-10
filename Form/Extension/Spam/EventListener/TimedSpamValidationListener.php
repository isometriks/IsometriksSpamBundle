<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\EventListener;

use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider\TimedSpamProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class TimedSpamValidationListener implements EventSubscriberInterface
{
    private TimedSpamProviderInterface $timeProvider;
    private string $errorMessage;
    private ?TranslatorInterface $translator;
    private string $translationDomain;
    private array $options;

    public function __construct(
        TimedSpamProviderInterface $timeProvider,
        ?TranslatorInterface $translator,
        string $translationDomain,
        string $errorMessage,
        array $options
    )
    {
        $this->timeProvider = $timeProvider;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->errorMessage = $errorMessage;
        $this->options = $options;
    }

    public function preSubmit(FormEvent $event): void
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

        /*
         * Remove the stored time
         */
        $this->timeProvider->removeFormTime($form->getName());
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }
}
