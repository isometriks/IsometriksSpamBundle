<?php

namespace Isometriks\Bundle\SpamBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class HoneyPotTest extends KernelTestCase
{
    /**
     * @dataProvider provideValidationShouldPass
     */
    public function testValidationShouldPass($data, $formOptions): void
    {
        self::bootKernel();

        $formBuilder = self::getContainer()->get('form.factory.custom')->createBuilder(FormType::class, null, $formOptions);
        foreach (array_keys($data) as $key) {
            $formBuilder->add($key);
        }
        $form = $formBuilder->getForm();
        $form->submit($data);
        $this->assertEquals(true, $form->isValid());
        $this->assertEmpty($form->getErrors());
    }

    public function provideValidationShouldPass()
    {
        return [
            'no_honeypot' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '0000000000',
                ],
                [
                    'honeypot' => false,
                ]
            ],
            'no_honeypot_with_field' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '0000000000',
                ],
                [
                    'honeypot' => false,
                    'honeypot_field' => 'phone',
                ]
            ],
            'honeypot_on_phone' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '',
                ],
                [
                    'honeypot' => true,
                    'honeypot_field' => 'phone',
                ]
            ],
            'honeypot_on_email' => [
                [
                    'email' => '',
                    'phone' => '0000000000',
                ],
                [
                    'honeypot' => true,
                    'honeypot_field' => 'email',
                ]
            ]
        ];
    }

    /**
     * @dataProvider provideValidationShouldFail
     */
    public function testValidationShouldFail($data, $formOptions, $expectedMessage = 'Form fields are invalid'): void
    {
        self::bootKernel();

        $formBuilder = self::getContainer()->get('form.factory.custom')->createBuilder(FormType::class, null, $formOptions);
        foreach (array_keys($data) as $key) {
            $formBuilder->add($key);
        }
        $form = $formBuilder->getForm();
        $form->submit($data);
        $this->assertFalse($form->isValid());
        $this->assertNotEmpty($form->getErrors());
        $this->assertEquals($expectedMessage, $form->getErrors()->offsetGet(0)->getMessage());
    }

    public function provideValidationShouldFail()
    {
        return [
            'honeypot_on_phone' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '0000000000',
                ],
                [
                    'honeypot' => true,
                    'honeypot_field' => 'phone',
                ]
            ],
            'honeypot_field_not_submitted' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '0000000000',
                ],
                [
                    'honeypot' => true,
                    'honeypot_field' => 'foo',
                ],
            ],
            'honeypot_field_null' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => null,
                ],
                [
                    'honeypot' => true,
                    'honeypot_field' => 'phone',
                ]
            ],
            'honeypot_with_error_message' => [
                [
                    'email' => 'ezaezae',
                    'phone' => '0000000000',
                ],
                [
                    'honeypot' => true,
                    'honeypot_field' => 'email',
                    'honeypot_message' => 'We detected a strange behaviour in your form submission. Please try again.',
                ],
                'We detected a strange behaviour in your form submission. Please try again.'
            ],
        ];
    }
}
