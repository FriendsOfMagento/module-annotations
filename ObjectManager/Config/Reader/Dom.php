<?php

declare(strict_types=1);

namespace Fom\Annotations\ObjectManager\Config\Reader;

use Fom\Annotations\Model\ClassValidator;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\ObjectManager\Config\Mapper\Dom as Converter;
use Magento\Framework\ObjectManager\Config\Reader\Dom as Reader;
use Magento\Framework\ObjectManager\Config\SchemaLocator;

class Dom extends Reader
{
    private const PREFERENCES = 'preferences';

    private const KEY_PLUGINS = 'plugins';
    private const KEY_INSTANCE = 'instance';
    private const KEY_DISABLED = 'disabled';

    /**
     * @var ClassValidator
     */
    private $validator;

    /**
     * @param FileResolverInterface $fileResolver
     * @param Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param ValidationStateInterface $validationState
     * @param ClassValidator $validator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        ClassValidator $validator,
        $fileName = 'di.xml',
        $idAttributes = [],
        $domDocumentClass = \Magento\Framework\Config\Dom::class,
        $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
        $this->validator = $validator;
    }

    /**
     * @param string $scope
     *
     * @return array
     */
    public function read($scope = null): array
    {
        return $this->processOutput(parent::read($scope));
    }

    /**
     * @param array $result
     *
     * @return array
     */
    public function processOutput(array $result): array
    {
        foreach ($result as $className => &$classConfig) {
            if ($className === self::PREFERENCES) {
                continue;
            }

            $plugins = (array)($classConfig[self::KEY_PLUGINS] ?? []);
            if (!is_array($plugins) || empty($plugins)) {
                continue;
            }

            foreach ($plugins as $pluginName => $pluginConfig) {
                $instance = (string)($pluginConfig[self::KEY_INSTANCE] ?? null);
                if (!$instance) {
                    continue;
                }

                if (!$this->validator->isExecutable($instance)) {
                    $classConfig[self::KEY_PLUGINS][$pluginName][self::KEY_DISABLED] = true;
                }
            }
        }

        return $result;
    }
}
