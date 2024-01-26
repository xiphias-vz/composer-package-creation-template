<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Validator;

use Generated\Shared\Transfer\S3UploadValidatorTransfer;
use Symfony\Component\Form\FormInterface;

interface ValidatorInterface
{
    /**
     * @param \Symfony\Component\Form\FormInterface $uploadForm
     *
     * @return \Generated\Shared\Transfer\S3UploadValidatorTransfer
     */
    public function validateUploadFormAndData(FormInterface $uploadForm): S3UploadValidatorTransfer;
}
