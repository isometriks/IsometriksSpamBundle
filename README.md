# Symfony2 SpamBundle

Please feel free to send pull requests. I would like to incorporate a bunch of 
spam methods into this project. 

Currently we have:

### Timed Spam Prevention

Requires forms to be sent after a certain amount of time. Most bots won't wait 
to submit your forms, so requiring an amount of time between render and submit 
can help deter these bots. 

Configuration:

    isometriks_spam:
        timed:
            min: 7
            max: 3600
            global: false
            message: You're doing that too quickly.

Usage:

    $this->createForm(new MyType(), null, array(
        'timed_spam' => true, 
        'timed_spam_min' => 3, 
        'timed_spam_max' => 40, 
        'timed_spam_message' => 'Please wait 3 seconds before submitting',
    )); 
    
Or

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'timed_spam' => true,
            // ...
        ));
    }