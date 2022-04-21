<?php

declare(strict_types=1);

namespace Fom\Annotations\Model;

use Magento\Framework\ObjectManager\ConfigInterface;

class ClassNameNormalizer
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public function execute(string $className): string
    {
        return $this->config->getInstanceType(ltrim($className, '\\'));
    }
}
