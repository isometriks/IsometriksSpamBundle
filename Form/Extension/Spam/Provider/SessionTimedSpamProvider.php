<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider;

use Symfony\Component\HttpFoundation\Session\Session;

class SessionTimedSpamProvider implements TimedSpamProviderInterface
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function generateFormTime($name)
    {
        $startTime = new \DateTime();
        $key = $this->getSessionKey($name);

        $this->session->set($key, $startTime);

        return $startTime;
    }

    public function isFormTimeValid($name, array $options)
    {
        $valid = true;
        $startTime = $this->getFormTime($name);

        /*
         * No value stored, so this can't be valid or session expired.
         */
        if($startTime === false){
            return false;
        }

        $currentTime = new \DateTime();

        /**
         * Check against a minimum time
         */
        if($options['min'] !== null){
            $minTime = clone $startTime;
            $minTime->modify(sprintf('+%d seconds', $options['min']));

            $valid &= $minTime < $currentTime;
        }

        /**
         * Check against a maximum time
         */
        if($options['max'] !== null){
            $maxTime = clone $startTime;
            $maxTime->modify(sprintf('+%d seconds', $options['max']));

            $valid &= $maxTime > $currentTime;
        }

        return $valid;
    }

    public function hasFormTime($name)
    {
        $key = $this->getSessionKey($name);

        return $this->session->has($key);
    }

    public function getFormTime($name)
    {
        $key = $this->getSessionKey($name);

        if($this->hasFormTime($name)){
            return $this->session->get($key);
        }

        return false;
    }

    public function removeFormTime($name)
    {
        $key = $this->getSessionKey($name);

        $this->session->remove($key);
    }

    protected function getSessionKey($name)
    {
        return 'timedSpam/'.$name;
    }
}