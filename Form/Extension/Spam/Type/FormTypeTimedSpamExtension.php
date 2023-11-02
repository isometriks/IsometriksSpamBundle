<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Type;

use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\EventListener\TimedSpamValidationListener;
use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider\TimedSpamProviderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormTypeTimedSpamExtension extends AbstractTypeExtension
{
    private TimedSpamProviderInterface $timeProvider;
    private ?TranslatorInterface $translator;
    private string $translationDomain;
    private array $defaults;

    public function __construct(
        TimedSpamProviderInterface $timeProvider,
        ?TranslatorInterface $translator,
        string $translationDomain,
        array $defaults
    ) {
        $this->timeProvider = $timeProvider;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->defaults = $defaults;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['timed_spam']) {
            return;
        }

        $providerOptions = [
            'min' => $options['timed_spam_min'],
            'max' => $options['timed_spam_max'],
        ];

        $builder
            ->addEventSubscriber(new TimedSpamValidationListener(
                $this->timeProvider,
                $this->translator,
                $this->translationDomain,
                $options['timed_spam_message'],
                $providerOptions
            ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if ($options['timed_spam'] && !$view->parent && $options['compound']) {
            $this->timeProvider->generateFormTime($form->getName());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'timed_spam' => $this->defaults['global'],
            'timed_spam_min' => $this->defaults['min'],
            'timed_spam_max' => $this->defaults['max'],
            'timed_spam_message' => $this->defaults['message'],
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    public function getExtendedType(): string
    {
        return FormType::class;
    }
}
