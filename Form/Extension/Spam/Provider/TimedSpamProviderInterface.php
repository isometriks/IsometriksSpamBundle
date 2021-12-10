<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider;

use DateTime;

interface TimedSpamProviderInterface
{
    /**
     * Generate form time.
     *
     * @param string $name
     *
     * @return DateTime
     */
    public function generateFormTime(string $name): DateTime;

    /**
     * Check if form has time.
     *
     * @param string $name
     */
    public function hasFormTime(string $name): bool;

    /**
     * Gets the form time for specified form.
     *
     * @param string $name Name of form to get
     */
    public function getFormTime(string $name);

    /**
     * Removes a form time name.
     *
     * @param string $name
     */
    public function removeFormTime(string $name): void;

    /**
     * Check if form time is valid.
     *
     * @param string $name
     * @param array  $options
     *
     * @return bool $valid
     */
    public function isFormTimeValid(string $name, array $options): bool;
}
