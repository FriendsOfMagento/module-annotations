<?php

declare(strict_types=1);

namespace Fom\Annotations\Model;

class ClassValidator
{
    /**
     * @var AttributeReader
     */
    private $attributeReader;

    /**
     * @var PlatformValidator
     */
    private $platformValidator;

    /**
     * @param AttributeReader $attributeReader
     * @param PlatformValidator $platformValidator
     */
    public function __construct(
        AttributeReader $attributeReader,
        PlatformValidator $platformValidator
    ) {
        $this->attributeReader = $attributeReader;
        $this->platformValidator = $platformValidator;
    }

    /**
     * @param string $className
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function isExecutable(string $className): bool
    {
        $operator = $this->attributeReader->getOperator($className);
        $platformList = $this->attributeReader->getPlatformList($className);

        $isExecutable = null;
        foreach ($platformList as $platform) {
            $validationResult = $this->platformValidator->isFit($platform);
            switch (true) {
                case $operator->isOperatorOr():
                    $isExecutable = $isExecutable === null ? $validationResult : $isExecutable || $validationResult;
                    if ($isExecutable) {
                        return $isExecutable;
                    }

                    break;
                case $operator->isOperatorAnd():
                    $isExecutable = $isExecutable === null ? $validationResult : $isExecutable && $validationResult;
                    if (!$isExecutable) {
                        return $isExecutable;
                    }

                    break;
            }
        }

        return $isExecutable === null ? true : $isExecutable;
    }
}
