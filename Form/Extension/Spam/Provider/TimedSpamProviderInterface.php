<?php

namespace Isometriks\Bundle\SpamBundle\Form\Extension\Spam\Provider; 

interface TimedSpamProviderInterface
{
    /**
     * @return \DateTime $startTime
     */
    public function generateFormTime($name);
    
    /**
     * Gets the form time for specified form
     * 
     * @param $name Name of form to get
     */
    public function getFormTime($name); 
    
    public function isFormTimeValid($name, array $options);
}