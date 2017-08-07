<?php


namespace Isometriks\Bundle\SpamBundle\EventListener;

use Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider\CookieProvider;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class KernelResponseListener
{
    /** @var CookieProvider */
    private $cookieProvider;
    
    public function __construct($cookieProvider)
    {
        $this->cookieProvider = $cookieProvider;
    }

    public function onKernelResponse(FilterResponseEvent $responseEvent)
    {
        $cookieData = $this->cookieProvider->getCookieSettings();
        if ($cookieData) {
            if($cookieData['mode'] == 'add') {
                $cookie = new Cookie($cookieData['name'], 1);
                
                $responseEvent->getResponse()->headers->setCookie($cookie);
            } else if ($cookieData['mode'] == 'remove') {
                $responseEvent->getResponse()->headers->removeCookie($cookieData['name']);
                $this->cookieProvider->removeCookieSettings();
            }
        }        
    }
}