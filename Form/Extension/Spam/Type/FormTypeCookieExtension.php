<?php


namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Type;

use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\EventListener\CookieValidationListener;
use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider\CookieProvider;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class FormTypeCookieExtension extends AbstractTypeExtension
{
    private $cookieProvider;
    private $request;
    private $session;
    private $translator;
    private $translationDomain;
    private $defaults;

    public function __construct(
        CookieProvider $cookieProvider,
        RequestStack $requestStack,
        Session $session,
        TranslatorInterface $translator,
        $translationDomain,
        array $defaults
    ) {
        $this->cookieProvider = $cookieProvider;
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $session;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->defaults = $defaults;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['cookie']) {
            return;
        }

        $builder
            ->addEventSubscriber(new CookieValidationListener(
                $this->cookieProvider,
                $this->request,
                $this->translator,
                $this->translationDomain,
                $options['cookie_name'],
                $options['cookie_message']
            ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->cookieProvider->setAntispamCookie($options['cookie_name']);
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'cookie' => $this->defaults['global'],
            'cookie_name' => $this->defaults['name'],
            'cookie_message' => $this->defaults['message'],
        ));
    }

    public function getExtendedType()
    {
        return FormType::class;
    }
}