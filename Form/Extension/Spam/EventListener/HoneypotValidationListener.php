<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class HoneypotValidationListener implements EventSubscriberInterface
{
    private ?TranslatorInterface $translator;
    private string $translationDomain;
    private string $fieldName;
    private string $errorMessage;

    public function __construct(
        ?TranslatorInterface $translator,
        string $translationDomain,
        string $fieldName,
        string $errorMessage
    )
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->fieldName = $fieldName;
        $this->errorMessage = $errorMessage;
    }

    public function preSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($form->isRoot() && $form->getConfig()->getOption('compound')) {
            if (!isset($data[$this->fieldName]) || !empty($data[$this->fieldName])) {
                $errorMessage = $this->errorMessage;

                if (null !== $this->translator) {
                    $errorMessage = $this->translator->trans($errorMessage, array(), $this->translationDomain);
                }

                $form->addError(new FormError($errorMessage));
            }

            if (is_array($data)) {
                unset($data[$this->fieldName]);
            }
        }

        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }
}
