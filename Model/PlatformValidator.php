<?php

declare(strict_types=1);

namespace Fom\Annotations\Model;

use Fom\Annotations\Attribute\Platform;
use Magento\Framework\App\ProductMetadataInterface;

class PlatformValidator
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param Platform $platform
     *
     * @return bool
     */
    public function isFit(Platform $platform): bool
    {
        return $this->isEditionFit($platform) && $this->isVersionFit($platform);
    }

    /**
     * @param Platform $platform
     *
     * @return bool
     */
    private function isEditionFit(Platform $platform): bool
    {
        if ($platform->isAnyEdition()) {
            return true;
        }

        return $this->productMetadata->getEdition() === $platform->getEdition();
    }

    /**
     * @param Platform $platform
     *
     * @return bool
     */
    private function isVersionFit(Platform $platform): bool
    {
        if ($platform->isAnyVersion()) {
            return true;
        }

        return (bool)version_compare(
            $this->productMetadata->getVersion(),
            $platform->getVersion(),
            $platform->getComparison()
        );
    }
}
