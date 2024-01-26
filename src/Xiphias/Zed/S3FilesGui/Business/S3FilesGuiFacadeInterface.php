<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business;

use Generated\Shared\Transfer\S3FilesDeleteRequestTransfer;
use Generated\Shared\Transfer\S3FilesDeleteResponseTransfer;
use Generated\Shared\Transfer\S3FilesDownloadRequestTransfer;
use Generated\Shared\Transfer\S3FilesDownloadResponseTransfer;
use Generated\Shared\Transfer\S3UploadResponseTransfer;
use Generated\Shared\Transfer\S3UploadTransfer;
use Generated\Shared\Transfer\S3UploadValidatorTransfer;
use Symfony\Component\Form\FormInterface;

interface S3FilesGuiFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\S3FilesDownloadRequestTransfer $s3FilesDownloadRequestTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDownloadResponseTransfer
     */
    public function downloadFiles(S3FilesDownloadRequestTransfer $s3FilesDownloadRequestTransfer): S3FilesDownloadResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\S3FilesDeleteRequestTransfer $s3FilesDeleteRequestTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer
     */
    public function deleteFiles(S3FilesDeleteRequestTransfer $s3FilesDeleteRequestTransfer): S3FilesDeleteResponseTransfer;

    /**
     * @param \Symfony\Component\Form\FormInterface $uploadForm
     *
     * @return \Generated\Shared\Transfer\S3UploadValidatorTransfer
     */
    public function validateUpload(FormInterface $uploadForm): S3UploadValidatorTransfer;

    /**
     * @param \Generated\Shared\Transfer\S3UploadTransfer $uploadTransfer
     *
     * @return \Generated\Shared\Transfer\S3UploadResponseTransfer
     */
    public function upload(S3UploadTransfer $uploadTransfer): S3UploadResponseTransfer;
}
