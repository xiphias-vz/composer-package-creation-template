<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Archiver;

use Generated\Shared\Transfer\S3FilesResultsTransfer;

interface S3FilesArchiverInterface
{
    /**
     * @param \Generated\Shared\Transfer\S3FilesResultsTransfer $s3FilesResultsTransfer
     *
     * @return string
     */
    public function archiveFiles(S3FilesResultsTransfer $s3FilesResultsTransfer): string;
}
