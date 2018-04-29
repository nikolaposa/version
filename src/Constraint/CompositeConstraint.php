<?php

declare(strict_types=1);

namespace Version\Constraint;

use Version\Version;
use Version\Exception\InvalidCompositeConstraintException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class CompositeConstraint implements ConstraintInterface
{
    public const TYPE_AND = 'AND';
    public const TYPE_OR = 'OR';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var ConstraintInterface[]
     */
    protected $constraints;

    protected function __construct()
    {
    }

    public static function fromProperties(string $type, ConstraintInterface $firstConstraint, ConstraintInterface ...$constraints) : CompositeConstraint
    {
        if (! in_array($type, [self::TYPE_AND, self::TYPE_OR], true)) {
            throw InvalidCompositeConstraintException::forType($type);
        }

        $compositeConstraint = new static();

        $compositeConstraint->type = $type;
        $compositeConstraint->constraints = array_merge([$firstConstraint], $constraints);

        return $compositeConstraint;
    }

    public static function fromAndConstraints(ConstraintInterface $firstConstraint, ConstraintInterface ...$constraints) : CompositeConstraint
    {
        return self::fromProperties(self::TYPE_AND, $firstConstraint, ...$constraints);
    }

    public static function fromOrConstraints(ConstraintInterface $firstConstraint, ConstraintInterface ...$constraints) : CompositeConstraint
    {
        return self::fromProperties(self::TYPE_OR, $firstConstraint, ...$constraints);
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function getConstraints() : array
    {
        return $this->constraints;
    }

    public function assert(Version $version) : bool
    {
        if ($this->type === self::TYPE_AND) {
            return $this->assertAnd($version);
        }

        return $this->assertOr($version);
    }

    protected function assertAnd(Version $version) : bool
    {
        foreach ($this->constraints as $constraint) {
            if (! $constraint->assert($version)) {
                return false;
            }
        }

        return true;
    }

    protected function assertOr(Version $version) : bool
    {
        foreach ($this->constraints as $constraint) {
            if ($constraint->assert($version)) {
                return true;
            }
        }

        return false;
    }
}
