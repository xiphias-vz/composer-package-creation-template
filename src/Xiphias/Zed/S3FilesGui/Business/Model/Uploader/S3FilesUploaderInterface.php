<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Uploader;

use Generated\Shared\Transfer\S3UploadResponseTransfer;
use Generated\Shared\Transfer\S3UploadTransfer;

interface S3FilesUploaderInterface
{
    /**
     * @param \Generated\Shared\Transfer\S3UploadTransfer $uploadTransfer
     *
     * @return \Generated\Shared\Transfer\S3UploadResponseTransfer
     */
    public function upload(S3UploadTransfer $uploadTransfer): S3UploadResponseTransfer;
}
