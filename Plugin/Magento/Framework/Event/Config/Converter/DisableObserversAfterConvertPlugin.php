<?php

declare(strict_types=1);

namespace Fom\Annotations\Plugin\Magento\Framework\Event\Config\Converter;

use Fom\Annotations\Model\ClassValidator;
use Magento\Framework\Event\Config\Converter;

class DisableObserversAfterConvertPlugin
{
    private const KEY_INSTANCE = 'instance';
    private const KEY_DISABLED = 'disabled';

    /**
     * @var ClassValidator
     */
    private $validator;

    /**
     * @param ClassValidator $validator
     */
    public function __construct(ClassValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Converter $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterConvert(Converter $subject, array $result): array
    {
        foreach ($result as &$event) {
            foreach ($event as &$observerConfig) {
                $instance = (string)($observerConfig[self::KEY_INSTANCE] ?? null);
                if (!$instance) {
                    continue;
                }

                if (!$this->validator->isExecutable($instance)) {
                    $observerConfig[self::KEY_DISABLED] = true;
                }
            }
        }

        return $result;
    }
}
