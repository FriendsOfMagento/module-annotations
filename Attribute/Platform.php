<?php

declare(strict_types=1);

namespace Fom\Annotations\Attribute;

use Attribute;
use Magento\Framework\App\ProductMetadata as Metadata;
use Spiral\Attributes\NamedArgumentConstructorAttribute;

/**
 * @Annotation
 * @Target({ "CLASS" })
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class Platform implements NamedArgumentConstructorAttribute
{
    /**
     * Edition list.
     */
    public const EDITION_ANY = 'any';
    public const EDITION_COMMUNITY = Metadata::EDITION_NAME;
    public const EDITION_COMMERCE = 'Enterprise';
    public const EDITION_B2B = 'B2B';

    /**
     * Version list.
     */
    public const VERSION_ANY = 'any';

    /**
     * Comparison list.
     */
    public const COMPARISON_LESS_THAN = '<';
    public const COMPARISON_LESS_THAN_OR_EQUAL = '<=';
    public const COMPARISON_GREATER_THAN = '>';
    public const COMPARISON_GREATER_THAN_OR_EQUAL = '>=';
    public const COMPARISON_EQUAL = '=';
    public const COMPARISON_NOT_EQUAL = '!=';

    /**
     * @var string
     */
    private $edition;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $comparison;

    /**
     * @param string $edition
     * @param string $version
     * @param string $comparison
     */
    public function __construct(
        string $edition = self::EDITION_ANY,
        string $version = self::VERSION_ANY,
        string $comparison = self::COMPARISON_GREATER_THAN_OR_EQUAL
    ) {
        $this->edition = $this->prepareValue(
            $edition,
            [
                self::EDITION_ANY,
                self::EDITION_COMMUNITY,
                self::EDITION_COMMERCE,
                self::EDITION_B2B,
            ],
            self::EDITION_ANY
        );
        $this->version = $version ?: self::VERSION_ANY;
        $this->comparison = $this->prepareValue(
            $comparison,
            [
                self::COMPARISON_LESS_THAN,
                self::COMPARISON_LESS_THAN_OR_EQUAL,
                self::COMPARISON_GREATER_THAN,
                self::COMPARISON_GREATER_THAN_OR_EQUAL,
                self::COMPARISON_EQUAL,
                self::COMPARISON_NOT_EQUAL,
            ],
            self::COMPARISON_GREATER_THAN_OR_EQUAL
        );
    }

    /**
     * @return string
     */
    public function getEdition(): string
    {
        return $this->edition;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getComparison(): string
    {
        return $this->comparison;
    }

    /**
     * @return bool
     */
    public function isAnyEdition(): bool
    {
        return $this->edition === self::EDITION_ANY || empty($this->edition);
    }

    /**
     * @return bool
     */
    public function isAnyVersion(): bool
    {
        return $this->version === self::VERSION_ANY || empty($this->version);
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
