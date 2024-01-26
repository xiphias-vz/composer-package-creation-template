<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui;

use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class S3FilesGuiConfig extends AbstractBundleConfig implements S3BucketConstants
{
    /**
     * @return array
     */
    public function getBucketCredentials(): array
    {
        return $this->get(S3BucketConstants::S3_BUCKETS_CREDENTIALS);
    }

    /**
     * @return int
     */
    public function getNumberOfRecordsPerRequest(): int
    {
        return $this->get(S3BucketConstants::TABLE_LOADER_CONFIGURATION_NUMBER_OF_RECORDS_PER_REQUEST);
    }
}
