<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Deleter;

use Generated\Shared\Transfer\S3FilesDeleteRequestTransfer;
use Generated\Shared\Transfer\S3FilesDeleteResponseTransfer;

interface S3FilesDeleterInterface
{
    /**
     * @param \Generated\Shared\Transfer\S3FilesDeleteRequestTransfer $s3FilesDeleteRequestTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer
     */
    public function deleteFiles(S3FilesDeleteRequestTransfer $s3FilesDeleteRequestTransfer): S3FilesDeleteResponseTransfer;
}
