<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider;

interface TimedSpamProviderInterface
{
    /**
     * Generate form time.
     */
    public function generateFormTime(string $name): \DateTime;

    /**
     * Check if form has time.
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
     */
    public function removeFormTime(string $name): void;

    /**
     * Check if form time is valid.
     *
     * @return bool $valid
     */
    public function isFormTimeValid(string $name, array $options): bool;
}
