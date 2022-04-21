<?php

declare(strict_types=1);

namespace Fom\Annotations\Model;

use Doctrine\Common\Annotations\AnnotationReader;
use Fom\Annotations\Attribute\Operator;
use Fom\Annotations\Attribute\OperatorFactory;
use Fom\Annotations\Attribute\Platform;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionClassFactory;
use ReflectionException;
use Spiral\Attributes\Factory as ReaderFactory;
use Spiral\Attributes\ReaderInterface;

class AttributeReader
{
    /**
     * @var ReaderFactory
     */
    private $readerFactory;

    /**
     * @var ReflectionClassFactory
     */
    private $reflectionClassFactory;

    /**
     * @var OperatorFactory
     */
    private $operatorFactory;

    /**
     * @var ClassNameNormalizer
     */
    private $classNameNormalizer;

    /**
     * @var ReflectionClass[]
     */
    private $reflectionClassCache = [];

    /**
     * @var string[]
     */
    private $ignoredNames;

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @param ReaderFactory $readerFactory
     * @param ReflectionClassFactory $reflectionClassFactory
     * @param OperatorFactory $operatorFactory
     * @param ClassNameNormalizer $classNameNormalizer
     * @param string[] $ignoredNames
     */
    public function __construct(
        ReaderFactory $readerFactory,
        ReflectionClassFactory $reflectionClassFactory,
        OperatorFactory $operatorFactory,
        ClassNameNormalizer $classNameNormalizer,
        array $ignoredNames = []
    ) {
        $this->readerFactory = $readerFactory;
        $this->reflectionClassFactory = $reflectionClassFactory;
        $this->operatorFactory = $operatorFactory;
        $this->classNameNormalizer = $classNameNormalizer;
        $this->ignoredNames = $ignoredNames;
    }

    /**
     * @param string $className
     *
     * @return Platform[]
     */
    public function getPlatformList(string $className): array
    {
        $platformList = [];
        try {
            foreach ($this->getAttributes($className, Platform::class) as $attribute) {
                if (!$attribute instanceof Platform) {
                    continue;
                }

                $platformList[] = $attribute;
            }
        } catch (ReflectionException $e) {
            ;
        }

        return $platformList;
    }

    /**
     * @param string $className
     *
     * @return Operator
     */
    public function getOperator(string $className): Operator
    {
        try {
            foreach ($this->getAttributes($className, Operator::class) as $attribute) {
                if (!$attribute instanceof Operator) {
                    continue;
                }

                return $attribute;
            }
        } catch (ReflectionException $e) {
            ;
        }

        return $this->operatorFactory->create();
    }

    /**
     * @param string $className
     * @param string $attributeClassName
     *
     * @return ReflectionAttribute[]
     * @throws ReflectionException
     */
    private function getAttributes(string $className, string $attributeClassName): array
    {
        return $this->getReader()->getClassMetadata(
            $this->getReflection($className),
            $attributeClassName
        );
    }

    /**
     * @param string $className
     *
     * @return ReflectionClass
     * @throws ReflectionException
     */
    private function getReflection(string $className): ReflectionClass
    {
        $className = $this->classNameNormalizer->execute($className);
        if (!array_key_exists($className, $this->reflectionClassCache)) {
            $this->reflectionClassCache[$className] = $this->reflectionClassFactory->create(
                ['objectOrClass' => $className]
            );
        }

        return $this->reflectionClassCache[$className];
    }

    /**
     * @return ReaderInterface
     */
    private function getReader(): ReaderInterface
    {
        if ($this->reader === null) {
            $this->initIgnoredNames();
            $this->reader = $this->readerFactory->create();
        }

        return $this->reader;
    }

    /**
     * @return void
     */
    private function initIgnoredNames(): void
    {
        foreach ($this->ignoredNames as $ignoredName) {
            AnnotationReader::addGlobalIgnoredName($ignoredName);
        }
    }
}
