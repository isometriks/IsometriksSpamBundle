<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider;

interface TimedSpamProviderInterface
{
    /**
     * @return \DateTime $startTime
     */
    public function generateFormTime($name);

    /**
     * Check if form has time.
     *
     * @param string $name
     */
    public function hasFormTime($name);

    /**
     * Gets the form time for specified form.
     *
     * @param $name Name of form to get
     */
    public function getFormTime($name);

    /**
     * Removes a form time name.
     *
     * @param string $name
     */
    public function removeFormTime($name);

    /**
     * Check if form time is valid.
     *
     * @param string $name
     * @param array  $options
     *
     * @return bool $valid
     */
    public function isFormTimeValid($name, array $options);
}
