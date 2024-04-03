<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Mapper\Upload;

use Generated\Shared\Transfer\LoadMoreDataTransfer;
use Generated\Shared\Transfer\S3ListObjectsResultTransfer;
use Generated\Shared\Transfer\S3UploadResponseTransfer;
use Generated\Shared\Transfer\S3UploadTransfer;

interface S3FilesUploadMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\S3UploadTransfer $uploadTransfer
     * @param bool $isSuccessful
     * @param string $message
     *
     * @return \Generated\Shared\Transfer\S3UploadResponseTransfer
     */
    public function mapMessageToNewUploadResponseTransfer(
        S3UploadTransfer $uploadTransfer,
        bool $isSuccessful,
        string $message
    ): S3UploadResponseTransfer;
}
