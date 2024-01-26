<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Validator;

use Generated\Shared\Transfer\S3UploadTransfer;
use Generated\Shared\Transfer\S3UploadValidatorTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;
use Spryker\Zed\Translator\Business\TranslatorFacadeInterface;
use Symfony\Component\Form\FormInterface;

class Validator implements ValidatorInterface, S3BucketFormConstants
{
    /**
     * @param \Spryker\Zed\Translator\Business\TranslatorFacadeInterface $translatorFacade
     */
    public function __construct(
        protected TranslatorFacadeInterface $translatorFacade
    ) {
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $uploadForm
     *
     * @return \Generated\Shared\Transfer\S3UploadValidatorTransfer
     */
    public function validateUploadFormAndData(FormInterface $uploadForm): S3UploadValidatorTransfer
    {
        $validatorTransfer = new S3UploadValidatorTransfer();
        $validatorTransfer->setIsValid(true);
        $validatorTransfer = $this->validateUploadForm($uploadForm, $validatorTransfer);
        if ($validatorTransfer->getIsValid()) {
            $validatorTransfer = $this->validateUploadFormData($uploadForm->getData(), $validatorTransfer);
        }

        return $validatorTransfer;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $uploadForm
     * @param \Generated\Shared\Transfer\S3UploadValidatorTransfer $validatorTransfer
     *
     * @return \Generated\Shared\Transfer\S3UploadValidatorTransfer
     */
    protected function validateUploadForm(FormInterface $uploadForm, S3UploadValidatorTransfer $validatorTransfer): S3UploadValidatorTransfer
    {
        if (!$uploadForm->isSubmitted() || !$uploadForm->isValid()) {
            $validatorTransfer->setIsValid(false);
            $validatorTransfer->setErrorMessage(
                $this->translatorFacade->trans(static::ERROR_MESSAGE_FORM_NOT_VALID),
            );
        }

        return $validatorTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\S3UploadTransfer $uploadFormData
     * @param \Generated\Shared\Transfer\S3UploadValidatorTransfer $validatorTransfer
     *
     * @return \Generated\Shared\Transfer\S3UploadValidatorTransfer
     */
    protected function validateUploadFormData(S3UploadTransfer $uploadFormData, S3UploadValidatorTransfer $validatorTransfer): S3UploadValidatorTransfer
    {
        $bucket = $uploadFormData[static::HIDDEN_BUCKET_FIELD];
        if (!$bucket) {
            $validatorTransfer->setIsValid(false);
            $validatorTransfer->setErrorMessage(
                $this->translatorFacade->trans(static::ERROR_MESSAGE_MISSING_BUCKET),
            );

            return $validatorTransfer;
        }

        $validatorTransfer->setBucket($bucket);

        return $validatorTransfer;
    }
}
