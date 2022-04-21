<?php

declare(strict_types=1);

namespace Fom\Annotations\Attribute;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructorAttribute;

/**
 * @Annotation
 * @Target({ "CLASS" })
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Operator implements NamedArgumentConstructorAttribute
{
    public const OPERATOR_AND = 'and';
    public const OPERATOR_OR = 'or';

    /**
     * @var string
     */
    private $operator;

    /**
     * @param string $operator
     */
    public function __construct(string $operator = self::OPERATOR_AND)
    {
        $this->operator = $this->prepareValue(
            $operator,
            [
                self::OPERATOR_AND,
                self::OPERATOR_OR,
            ],
            self::OPERATOR_AND
        );
    }

    /**
     * @return bool
     */
    public function isOperatorAnd(): bool
    {
        return $this->operator === self::OPERATOR_AND;
    }

    /**
     * @return bool
     */
    public function isOperatorOr(): bool
    {
        return $this->operator === self::OPERATOR_OR;
    }

    /**
     * @param string $value
     * @param array $allowedValues
     * @param string $defaultValue
     *
     * @return string
     */
    private function prepareValue(string $value, array $allowedValues, string $defaultValue): string
    {
        if (!in_array($value, $allowedValues, true)) {
            $value = $defaultValue;
        }

        return $value;
    }
}
