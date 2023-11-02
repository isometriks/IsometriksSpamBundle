<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionTimedSpamProvider implements TimedSpamProviderInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function generateFormTime(string $name): \DateTime
    {
        $startTime = new \DateTime();
        $key = $this->getSessionKey($name);

        $this->getSession()->set($key, $startTime);

        return $startTime;
    }

    public function isFormTimeValid(string $name, array $options): bool
    {
        $valid = true;
        $startTime = $this->getFormTime($name);

        /*
         * No value stored, so this can't be valid or session expired.
         */
        if (false === $startTime) {
            return false;
        }

        $currentTime = new \DateTime();

        /*
         * Check against a minimum time
         */
        if (null !== $options['min']) {
            $minTime = clone $startTime;
            $minTime->modify(sprintf('+%d seconds', $options['min']));

            $valid &= $minTime < $currentTime;
        }

        /*
         * Check against a maximum time
         */
        if (null !== $options['max']) {
            $maxTime = clone $startTime;
            $maxTime->modify(sprintf('+%d seconds', $options['max']));

            $valid &= $maxTime > $currentTime;
        }

        return $valid;
    }

    public function hasFormTime(string $name): bool
    {
        $key = $this->getSessionKey($name);

        return $this->getSession()->has($key);
    }

    public function getFormTime(string $name)
    {
        $key = $this->getSessionKey($name);

        if ($this->hasFormTime($name)) {
            return $this->getSession()->get($key);
        }

        return false;
    }

    public function removeFormTime(string $name): void
    {
        $key = $this->getSessionKey($name);

        $this->getSession()->remove($key);
    }

    protected function getSessionKey(string $name): string
    {
        return 'timedSpam/'.$name;
    }

    protected function getSession(): SessionInterface
    {
        return $this->requestStack->getCurrentRequest()->getSession();
    }
}
