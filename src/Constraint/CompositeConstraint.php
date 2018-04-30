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

    public function __construct(string $type, ConstraintInterface $constraint, ConstraintInterface ...$constraints)
    {
        if (! in_array($type, [self::TYPE_AND, self::TYPE_OR], true)) {
            throw InvalidCompositeConstraintException::forType($type);
        }

        $this->type = $type;
        $this->constraints = array_merge([$constraint], $constraints);
    }

    public static function and(ConstraintInterface $constraint, ConstraintInterface ...$constraints) : CompositeConstraint
    {
        return new static(self::TYPE_AND, $constraint, ...$constraints);
    }

    public static function or(ConstraintInterface $constraint, ConstraintInterface ...$constraints) : CompositeConstraint
    {
        return new static(self::TYPE_OR, $constraint, ...$constraints);
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
