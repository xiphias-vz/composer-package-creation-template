<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Mapper\Delete;

use Aws\Result;
use Generated\Shared\Transfer\S3FilesDeleteRequestTransfer;
use Generated\Shared\Transfer\S3FilesDeleteResponseTransfer;

interface S3FilesDeleteMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\S3FilesDeleteRequestTransfer $s3FilesDeleteRequestTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer
     */
    public function mapDeleteRequestTransferToNewResponseTransfer(S3FilesDeleteRequestTransfer $s3FilesDeleteRequestTransfer): S3FilesDeleteResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer $responseTransfer
     * @param string $message
     * @param \Aws\Result $result
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer
     */
    public function mapMessageToDeleteResponseTransfer(
        S3FilesDeleteResponseTransfer $responseTransfer,
        string $message,
        Result $result
    ): S3FilesDeleteResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer $responseTransfer
     * @param string $message
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer
     */
    public function mapMessageToFailedDeleteResponseTransfer(S3FilesDeleteResponseTransfer $responseTransfer, string $message): S3FilesDeleteResponseTransfer;
}
