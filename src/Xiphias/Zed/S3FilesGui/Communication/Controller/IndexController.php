<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Communication\Controller;

use Generated\Shared\Transfer\S3UploadValidatorTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \Xiphias\Zed\S3FilesGui\Business\S3FilesGuiFacadeInterface getFacade();
 * @method \Xiphias\Zed\S3FilesGui\Communication\S3FilesGuiCommunicationFactory getFactory();
 */
class IndexController extends AbstractController implements S3BucketConstants, S3BucketFormConstants
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @return array
     */
    public function indexAction(?Request $request): array
    {
        $controllerIndexActionsHandler = $this->getFactory()->createControllerActionsHandler();

        $s3BucketForm = $controllerIndexActionsHandler->handleS3BucketFormAndTableData($request);

        $s3BucketTable = $this->getFactory()->createS3BucketTable();

        $s3UploadForm = $this->getFactory()->createS3UploadForm();

        $s3DownloadDeleteSelectedForm = $this->getFactory()->getS3DownloadDeleteSelectedForm();

        return [
            static::TWIG_BUCKET_TABLE => $s3BucketTable->render(),
            static::TWIG_BUCKET_FORM => $s3BucketForm->createView(),
            static::TWIG_UPLOAD_FORM => $s3UploadForm->createView(),
            static::TWIG_DOWNLOAD_DELETE_SELECTED_FORM => $s3DownloadDeleteSelectedForm->createView(),
            static::FILTER_PARAM => $controllerIndexActionsHandler->getFilterString(),
            static::CHOSEN_BUCKET => $controllerIndexActionsHandler->getBucketName(),
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function s3TableAction(Request $request): JsonResponse
    {
        $controllerS3TableActionsHandler = $this->getFactory()->createControllerActionsHandler();
        $controllerS3TableActionsHandler->setSearchString($request);
        $controllerS3TableActionsHandler->setFilterString($request);
        $controllerS3TableActionsHandler->setTableAction($request);
        $controllerS3TableActionsHandler->handleTableAction();

        $s3BucketTable = $this->getFactory()->createS3BucketTable();

        return $this->jsonResponse(
            $s3BucketTable->fetchData(),
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadAction(Request $request): Response
    {
        $controllerDownloadActionHandler = $this->getFactory()->createControllerActionsHandler();

        $bucketName = $controllerDownloadActionHandler->getBucketName();
        $fileNames = $controllerDownloadActionHandler->getFileNames($request);
        if (!$fileNames) {
            return $this->redirectResponse(static::ROUTE_INDEX);
        }

        $s3FilesDownloadRequestTransfer = $controllerDownloadActionHandler->getS3FilesDownloadRequest($bucketName, $fileNames);
        $downloadResponseTransfer = $this->getFacade()->downloadFiles($s3FilesDownloadRequestTransfer);
        if (!$downloadResponseTransfer->getIsSuccessful()) {
            $this->addErrorMessage(
                static::MESSAGE_DOWNLOAD_FAILED,
                [static::MESSAGE_RESPONSE_CONTENT_PARAM => $downloadResponseTransfer->getContent()],
            );

            return $this->redirectResponse(static::ROUTE_INDEX);
        }

        return $this->getFactory()->createResponse(
            $downloadResponseTransfer->getContent(),
            Response::HTTP_OK,
            $downloadResponseTransfer->getHeaders(),
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request): Response
    {
        $controllerDeleteActionHandler = $this->getFactory()->createControllerActionsHandler();

        $bucketName = $controllerDeleteActionHandler->getBucketName();
        $fileNames = $controllerDeleteActionHandler->getFileNames($request);
        if ($fileNames) {
            $deleteRequestTransfer = $controllerDeleteActionHandler->getS3FilesDeleteRequest($bucketName, $fileNames);
            $deleteResponseTransfer = $this->getFacade()->deleteFiles($deleteRequestTransfer);

            $this->addMessage(
                $deleteResponseTransfer->getIsSuccessful(),
                $deleteResponseTransfer->getMessage(),
            );
        }

        $controllerDeleteActionHandler->setDeletedFilesAndTableAction($fileNames);

        return $this->redirectResponse(static::ROUTE_INDEX);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleSelectedFilesAction(Request $request): Response
    {
        $controllerHandleSelectedFilesActionHandler = $this->getFactory()->createControllerActionsHandler();
        $s3DownloadDeleteSelectedForm = $controllerHandleSelectedFilesActionHandler->handleS3DownloadDeleteSelectedForm($request);

        if ($s3DownloadDeleteSelectedForm->isSubmitted() && $s3DownloadDeleteSelectedForm->isValid()) {
            $url = $controllerHandleSelectedFilesActionHandler->downloadDeleteRedirect($request);

            return $this->redirectResponse($url);
        }

        return $this->redirectResponse(static::ROUTE_INDEX);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function uploadAction(Request $request): RedirectResponse
    {
        $controllerUploadActionHandler = $this->getFactory()->createControllerActionsHandler();
        $form = $controllerUploadActionHandler->handleUploadAction($request);

        $validatorTransfer = $this
                ->getFacade()
                ->validateUpload($form);

        if ($this->isUploadFormValid($validatorTransfer)) {
            $uploadResponseTransfer = $this->getFacade()->upload($form->getData());
            $this->addMessage(
                $uploadResponseTransfer->getIsSuccessful(),
                $uploadResponseTransfer->getMessage(),
            );
        }

        return $this->redirectResponse(self::ROUTE_INDEX);
    }

    /**
     * @param \Generated\Shared\Transfer\S3UploadValidatorTransfer $validatorTransfer
     *
     * @return bool
     */
    protected function isUploadFormValid(S3UploadValidatorTransfer $validatorTransfer): bool
    {
        if (!$validatorTransfer->getIsValid()) {
            $this->addErrorMessage($validatorTransfer->getErrorMessage());

            return false;
        }

        return true;
    }

    /**
     * @param bool $isSuccessful
     * @param string $message
     *
     * @return void
     */
    protected function addMessage(bool $isSuccessful, string $message): void
    {
        if (!$isSuccessful) {
            $this->addErrorMessage($message);

            return;
        }

        $this->addSuccessMessage($message);
    }
}
