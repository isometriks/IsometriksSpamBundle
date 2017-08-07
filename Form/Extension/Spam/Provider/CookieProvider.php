<?php


namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider;

use Symfony\Component\HttpFoundation\Session\Session;

class CookieProvider
{
    private $session;
    
    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    
    public function setAntispamCookie($cookieName)
    {
        $this->session->set('antispam_cookie', array('mode' => 'add', 'name' => $cookieName));
    }

    public function removeAntispamCookie($cookieName)
    {
        $this->session->set('antispam_cookie', array('mode' => 'remove', 'name' => $cookieName));        
    }

    public function getCookieSettings()
    {
        return $this->session->get('antispam_cookie');
    }

    public function removeCookieSettings()
    {
        $this->session->remove('antispam_cookie');   
    }
}