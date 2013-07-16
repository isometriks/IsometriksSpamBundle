# Symfony2 SpamBundle

Please feel free to send pull requests. I would like to incorporate a bunch of 
spam methods into this project. 

Currently we have:

### Timed Spam Prevention

Requires forms to be sent after a certain amount of time. Most bots won't wait 
to submit your forms, so requiring an amount of time between render and submit 
can help deter these bots. 

Configuration:

```YAML
isometriks_spam:
    timed:
        min: 7
        max: 3600
        global: false
        message: You're doing that too quickly.
```

Usage:

```php
$this->createForm(new MyType(), null, array(
    'timed_spam' => true, 
    'timed_spam_min' => 3, 
    'timed_spam_max' => 40, 
    'timed_spam_message' => 'Please wait 3 seconds before submitting',
)); 
```

Or

```php
public function setDefaultOptions(OptionsResolverInterface $resolver)
{
    $resolver->setDefaults(array(
        'timed_spam' => true,
        // ...
    ));
}
```

### Honeypot Spam Prevention

A honeypot is a way to trick bots into filling out a field that should not 
be filled out. It is hidden and can be named something usual so that any 
bots / crawlers will think it is a real field. 

If the field is filled out, then the form is invalid. You can optionally
choose to use a class name to hide the form element as well in case the
bot tries to check the style attribute. 

```yml
isometriks_spam:
    honeypot:
        field: email_address
        use_class: false
        hide_class: hidden
        global: false
        message: Form fields are invalid
```

Usage:

```php
$this->createForm(new MyType(), null, array(
    'honeypot' => true, 
    'honeypot_field' => 'email_address', 
    'honeypot_use_class' => false, 
    'honeypot_hide_class' => 'hidden',
    'honeypot_message' => 'Form field are invalid', 
)); 
```

Or

```php
public function setDefaultOptions(OptionsResolverInterface $resolver)
{
    $resolver->setDefaults(array(
        'honeypot' => true,
        // ...
    ));
}
```
