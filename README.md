# Symfony SpamBundle

Please feel free to send pull requests. I would like to incorporate a bunch of
spam methods into this project.

### Installation

Install via Composer:

```shell
$ composer require isometriks/spam-bundle
```

Use `~0.3.0` for Symfony 2.3-2.7, or `~1.0` for 3+

Add to AppKernel.php (done automatically for Symfony Flex):

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Isometriks\Bundle\SpamBundle\IsometriksSpamBundle(),
            // ...
        ];

        return $bundles;
    }
}
```

Currently we have:

### Timed Spam Prevention

Requires forms to be sent after a certain amount of time. Most bots won't wait
to submit your forms, so requiring an amount of time between render and submit
can help deter these bots.

*A side affect of this spam prevention is that you won't be able to refresh
a page to resubmit data UNLESS the view is rendered again `$form->createView()`
This is because the event listener removes the start time of the form and
when it can't find it, will cause the form to be invalid. You could set your
min time to 0 to just make use of this feature*

*Also note that this spam protection will also apply this limit to forms that
are not filled in correctly and need to be resubmitted. A high minimum time 
could affect those users who only need to fix one field quickly*

Configuration:

```YAML
# Copying this config is not necessary. These are defaults, only copy 
# what you'd like to change. 
isometriks_spam:
    timed:
        min: 7
        max: 3600
        global: false
        # message also takes translator strings.
        message: You're doing that too quickly.
```

Usage:

```php
// Only timed_spam = true is required to enable, the rest are to override settings
$this->createForm(MyType:class, null, [
    'timed_spam' => true,
    'timed_spam_min' => 3,
    'timed_spam_max' => 40,
    'timed_spam_message' => 'Please wait 3 seconds before submitting',
]);
```

Or

```php
public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults([
        'timed_spam' => true,
        // ...
    ]);
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
# Copying this config is not necessary. These are defaults, only copy 
# what you'd like to change. 
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
// Only honeypot = true is required to enable, the rest are to override settings
$this->createForm(MyType::class, null, [
    'honeypot' => true,
    'honeypot_field' => 'email_address',
    'honeypot_use_class' => false,
    'honeypot_hide_class' => 'hidden',
    'honeypot_message' => 'Form field are invalid',
]);
```

Or

```php
public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults([
        'honeypot' => true,
        // ...
    ]);
}
```

### Twig Form Error message rendering

Form errors come from the form itself, so if you want to display the errors
you'll need to make sure this is in your template.

```
{% if form.vars.errors is not empty %}
    {{ form_errors(form) }}
{% endif %}
```
