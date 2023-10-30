<?php

namespace Isometriks\Bundle\SpamBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionFactory;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorageFactory;

class TimedSpamTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        $sessionFactory = new MockFileSessionStorageFactory();
        $request = new Request();
        $request->setSession(new Session($sessionFactory->createStorage(null)));
        self::getContainer()->get('request_stack')->push($request);
    }

    /**
     * @dataProvider provideValidationShouldPass
     */
    public function testValidationShouldPass($data, $formOptions, $sleep = 0): void
    {
        $formBuilder = self::getContainer()->get('form.factory.custom')->createBuilder(FormType::class, null, $formOptions);
        foreach (array_keys($data) as $key) {
            $formBuilder->add($key);
        }
        $form = $formBuilder->getForm();
        $form->createView();
        if ($sleep > 0) {
            sleep($sleep);
        }
        $form->submit($data);
        $this->assertTrue($form->isValid());
        $this->assertEmpty($form->getErrors());
    }

    public function provideValidationShouldPass()
    {
        return [
            'no_timed_spam' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '0000000000',
                ],
                [
                    'timed_spam' => false,
                ]
            ],
            'no_timed_spam_with_options' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '0000000000',
                ],
                [
                    'timed_spam' => false,
                    'timed_spam_min' => 3,
                    'timed_spam_max' => 40,
                    'timed_spam_message' => 'Please wait 3 seconds before submitting',
                ]
            ],
            'timed_spam_min_0' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '',
                ],
                [
                    'timed_spam' => true,
                    'timed_spam_min' => 0,
                ]
            ],
            'timed_spam_min_1' => [
                [
                    'email' => '',
                    'phone' => '0000000000',
                ],
                [
                    'timed_spam' => true,
                    'timed_spam_min' => 1,
                ],
                1
            ],
            'timed_spam_min_0_max_1' => [
                [
                    'email' => '',
                    'phone' => '0000000000',
                ],
                [
                    'timed_spam' => true,
                    'timed_spam_min' => 0,
                    'timed_spam_max' => 1,
                ]
            ]
        ];
    }

    /**
     * @dataProvider provideValidationShouldFail
     */
    public function testValidationShouldFail($data, $formOptions, $sleep = 0, $expectedMessage = 'You are doing that too quickly'): void
    {
        $formBuilder = self::getContainer()->get('form.factory.custom')->createBuilder(FormType::class, null, $formOptions);
        foreach (array_keys($data) as $key) {
            $formBuilder->add($key);
        }
        $form = $formBuilder->getForm();
        $form->createView();
        if ($sleep > 0) {
            sleep($sleep);
        }
        $form->submit($data);
        $this->assertFalse($form->isValid());
        $this->assertNotEmpty($form->getErrors());
        $this->assertEquals($expectedMessage, $form->getErrors()->offsetGet(0)->getMessage());
    }

    public function provideValidationShouldFail(): array
    {
        return [
            'timed_spam_min_1' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '0000000000',
                ],
                [
                    'timed_spam' => true,
                    'timed_spam_min' => 1,
                ]
            ],
            'timed_spam_max_0' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '',
                ],
                [
                    'timed_spam' => true,
                    'timed_spam_max' => 0,
                ]
            ],
            'timed_spam_max_1' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '',
                ],
                [
                    'timed_spam' => true,
                    'timed_spam_max' => 1,
                ],
                1
            ],
            // Maybe this should rather throw an error during option resolution ?
            'timed_spam_min_2_max_1' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '',
                ],
                [
                    'timed_spam' => true,
                    'timed_spam_min' => 2,
                    'timed_spam_max' => 1,
                ]
            ],
            'timed_spam_with_error_message' => [
                [
                    'email' => 'foo@email.com',
                    'phone' => '',
                ],
                [
                    'timed_spam' => true,
                    'timed_spam_min' => 1,
                    'timed_spam_message' => 'Please wait 1 second before submitting',
                ],
                0,
                'Please wait 1 second before submitting'
            ],
        ];
    }
}
