<?php

namespace App\Validator;

use Silex\Application;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

class ConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
    protected $app;
    protected $validators;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container  The service container
     * @param array              $validators An array of validators
     */
    public function __construct(Application $app, array $validators = array())
    {
        $this->app = $app;
        $this->validators = $validators;
    }

    /**
     * Returns the validator for the supplied constraint.
     *
     * @param Constraint $constraint A constraint
     *
     * @return Symfony\Component\Validator\ConstraintValidator A validator for the supplied constraint
     */
    public function getInstance(Constraint $constraint)
    {
        $name = $constraint->validatedBy();

        if (!isset($this->validators[$name])) {
            if (class_exists($name)) {
                $this->validators[$name] = new $name();
            } else {
                $this->validators[$name] = $this->app[$name];
            }
        }

        return $this->validators[$name];
    }
}
