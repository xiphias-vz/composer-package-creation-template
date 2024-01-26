<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace XiphiasTest\Zed\S3FilesGui\Business;

use AWS\CRT\Auth\AwsCredentials;
use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\BucketAndFileNamesBuilder;
use Generated\Shared\DataBuilder\S3FilesDeleteRequestBuilder;
use Generated\Shared\DataBuilder\S3FilesDownloadRequestBuilder;
use Generated\Shared\DataBuilder\S3FilesResultsBuilder;
use Generated\Shared\DataBuilder\S3UploadBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\S3FilesGui\Business\Model\ResponseBuilder\DownloadResponseBuilder;
use XiphiasTest\Zed\S3FilesGui\S3FileGuiBusinessTester;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Auto-generated group annotations
 *
 * @group XiphiasTest
 * @group Zed
 * @group S3FilesGui
 * @group Business
 * @group Facade
 * @group S3FilesGuiFacadeTest
 * Add your own group annotations below this line
 */
class S3FilesGuiFacadeTest extends Unit
{
    /**
     * @var \XiphiasTest\Zed\S3FilesGui\S3FileGuiBusinessTester
     */
    protected S3FileGuiBusinessTester $tester;

    /**
     * @var string
     */
    protected const SERVICE_FORM_FACTORY = 'form.factory';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registerFormFactoryServiceMock();
    }

    /**
     * @test
     *
     * @return void
     */
    public function fileDownloadWithGoodBucketAndFileNameThatDownloads(): void
    {
        $this->tester->wantTo('Download the file');

        //Arrange
        $s3FileDownloadRequestTransfer = (new S3FilesDownloadRequestBuilder([
            'bucketAndFileNames' => (new BucketAndFileNamesBuilder([
                'bucketName' => 'oase-staging-images',
                'fileNames' => ['001_bis_004_Preisliste_alle GF_2021.xlsx'],
            ]))->build(),
        ]))->build();

        //Act
        $s3FilesBusinessFacade = $this->tester->getLocator()->s3FilesGui()->facade();
        $s3FileDownloadResponseTransfer = $s3FilesBusinessFacade->downloadFiles($s3FileDownloadRequestTransfer);

        //Assert
        $this->assertTrue($s3FileDownloadResponseTransfer->getIsSuccessful(), 'File download is successful!');
        $this->assertEquals('attachment; filename=001_bis_004_Preisliste_alle GF_2021.xlsx', $s3FileDownloadResponseTransfer->getHeaders()['Content-Disposition']);
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $s3FileDownloadResponseTransfer->getHeaders()['Content-Type']);
        $this->assertEquals('Public', $s3FileDownloadResponseTransfer->getHeaders()['Pragma']);
    }

    /**
     * @test
     *
     * @return void
     */
    public function multipleFileDownloadWithGoodBucketAndFileNamesThatDownloads(): void
    {
        $this->tester->wantTo('Download multiple the files');

        //Arrange
        $s3FileDownloadRequestTransfer = (new S3FilesDownloadRequestBuilder([
            'bucketAndFileNames' => (new BucketAndFileNamesBuilder([
                'bucketName' => 'oase-staging-images',
                'fileNames' => ['001_bis_004_Preisliste_alle GF_2021.xlsx', 'DIA_FAM_BA_39916-AquaMaxEcoExpert21000-26000-001_#SDE_#AINGIF_#V2.gif'],
            ]))->build(),
        ]))->build();

        //Act
        $s3FilesBusinessFacade = $this->tester->getLocator()->s3FilesGui()->facade();
        $s3FileDownloadResponseTransfer = $s3FilesBusinessFacade->downloadFiles($s3FileDownloadRequestTransfer);

        //Assert
        $this->assertTrue($s3FileDownloadResponseTransfer->getIsSuccessful(), 'Files download is successful!');
        $this->assertEquals('application/zip', $s3FileDownloadResponseTransfer->getHeaders()['Content-Type']);
        $this->assertEquals('Public', $s3FileDownloadResponseTransfer->getHeaders()['Pragma']);
    }

    /**
     * @test
     *
     * @return void
     */
    public function fileDownloadWithWrongBucketAndFileNameThatDoesNotDownload(): void
    {
        $this->tester->wantTo('Download the file with wrong bucket and file names');

        //Arrange
        $s3FileDownloadRequestTransfer = (new S3FilesDownloadRequestBuilder([
            'bucketAndFileNames' => (new BucketAndFileNamesBuilder())->build(),
        ]))->build();

        //Act
        $s3FilesBusinessFacade = $this->tester->getLocator()->s3FilesGui()->facade();
        $s3FileDownloadResponseTransfer = $s3FilesBusinessFacade->downloadFiles($s3FileDownloadRequestTransfer);

        //Assert
        $this->assertNotTrue($s3FileDownloadResponseTransfer->getIsSuccessful(), 'File download is not successful!');
        $this->assertCount(0, $s3FileDownloadResponseTransfer->getHeaders());
    }

    /**
     * @test
     *
     * @return void
     */
    public function fileDeleteWithWrongCredentialsThatFails(): void
    {
        $this->tester->wantTo('Delete the file with wrong credentials');

        //Arrange
        $s3FileDeleteRequestTransfer = (new S3FilesDeleteRequestBuilder([
            'bucketAndFileNames' => (new BucketAndFileNamesBuilder([
                'bucketName' => 'oase-staging-pdf',
                'fileNames' => ['11.pdf'],
            ]))->build(),
        ]))->build();

        $this->tester->mockEnvironmentConfig('s3-buckets_credentials', [
            'sprykerAdapterClass' => AwsCredentials::class,
            'key' => 'kwopšđekđghwšerkšgwAKIASXT3N7XBIICJPXLR', // can we use builder for this
            'secret' => 'zqwepšđrkgđqwšergpjel2b4BMoyOgud5d3LwFD+V1QzboW85of3r2I9', // can we use builder for this
        ]);

        //Act
        $s3FilesBusinessFacade = $this->tester->getLocator()->s3FilesGui()->facade();
        $s3FileDeleteResponseTransfer = $s3FilesBusinessFacade->deleteFiles($s3FileDeleteRequestTransfer);

        //Assert
        $this->assertNotTrue($s3FileDeleteResponseTransfer->getIsSuccessful());
        $this->assertEquals('File was not deleted successfully! Please check your S3 credentials, access and user rights for selected bucket.', $s3FileDeleteResponseTransfer->getMessage());
    }

    /**
     * @test
     *
     * @return void
     */
    public function fileDeletionWithGoodBucketThatDeletesTheFile(): void
    {
        $this->tester->wantTo('Delete the file');

        //Arrange
        $s3FileDeleteRequestTransfer = (new S3FilesDeleteRequestBuilder([
            'bucketAndFileNames' => (new BucketAndFileNamesBuilder([
                'bucketName' => 'oase-staging-pdf',
                'fileNames' => ['11.pdf'],
            ]))->build(),
        ]))->build();

        //Act
        $s3FileGuiFacade = $this->tester->getLocator()->s3FilesGui()->facade();
        $s3FilesDeleteResponseTransfer = $s3FileGuiFacade->deleteFiles($s3FileDeleteRequestTransfer);

        //Assert
        $this->assertTrue($s3FilesDeleteResponseTransfer->getIsSuccessful());
        $this->assertEquals('Files: 11.pdf deleted successfully from oase-staging-pdf', $s3FilesDeleteResponseTransfer->getMessage());
    }

    /**
     * @test
     *
     * @return void
     */
    public function fileUploadWithWrongCredentialsThatFails(): void
    {
        $this->tester->wantTo('Upload the file with wrong credentials');

        //Arrange
        $s3FileUploadRequestTransfer = (new S3UploadBuilder([
            'bucket' => 'oase-staging-pdf',
            'uploadedFile' => $this->tester->createUploadedFile('Neki content', '11', 'application/pdf'), $this->tester->createUploadedFile('Neki content', '11', 'application/pdf'),
        ]))->build();

        $this->tester->mockEnvironmentConfig('s3-buckets_credentials', [
            'sprykerAdapterClass' => AwsCredentials::class,
            'key' => 'kwopšđekđghwšerkšgwAKIASXT3N7XBIICJPXLR',
            'secret' => 'zqwepšđrkgđqwšergpjel2b4BMoyOgud5d3LwFD+V1QzboW85of3r2I9',
        ]);

        //Act
        $s3FilesBusinessFacade = $this->tester->getLocator()->s3FilesGui()->facade();
        $s3FileUploadResponseTransfer = $s3FilesBusinessFacade->upload($s3FileUploadRequestTransfer);

        //Assert
        $this->assertNotTrue($s3FileUploadResponseTransfer->getIsSuccessful());
        $this->assertEquals('File upload has failed! Please check your S3 credentials, access and user rights for selected bucket.', $s3FileUploadResponseTransfer->getMessage());
    }

    /**
     * @test
     *
     * @return void
     */
    public function fileUploadWithGoodBucketThatUploads(): void
    {
        $this->tester->wantTo('Upload the file');

        //Arrange
        $s3FileUploadRequestTransfer = (new S3UploadBuilder([
            'bucket' => 'oase-staging-pdf',
            'uploadedFile' => $this->tester->createUploadedFile('Neki content', '11', 'application/pdf'),
        ]))->build();

        //Act
        $s3FilesGuiFacade = $this->tester->getLocator()->s3FilesGui()->facade();
        $s3FileUploadResponseTransfer = $s3FilesGuiFacade->upload($s3FileUploadRequestTransfer);

        //Assert
        $this->assertTrue($s3FileUploadResponseTransfer->getIsSuccessful(), 'File Uploaded is successful!');
        $this->assertEquals('11 successfully uploaded to oase-staging-pdf', $s3FileUploadResponseTransfer->getMessage());
    }

    /**
     * @test
     *
     * @return void
     */
    public function uploadFormValidationWithSuccess(): void
    {
        $this->tester->wantTo('Validate Upload form');

        //Arrange
        $formInterface = $this->getMockBuilder(FormInterface::class)->getMock();
        $formInterface->expects($this->any())->method('isSubmitted')->willReturn(true);
        $formInterface->expects($this->any())->method('isValid')->willReturn(true);
        $formInterface->method('createView')->willReturn($this->getFormViewMock());

        $s3FileUploadTransfer = (new S3UploadBuilder([
            'bucket' => 'oase-staging-pdf',
            'uploadedFile' => $this->tester->createUploadedFile('Neki content', '11', 'application/pdf'),
        ]))->build();

        $formInterface->expects($this->any())->method('getData')->willReturn($s3FileUploadTransfer);

        //Act
        $s3FilesGuiFacade = $this->tester->getLocator()->s3FilesGui()->facade();
        $s3UploadValidatorTransfer = $s3FilesGuiFacade->validateUpload($formInterface);

        //Assert
        $this->assertTrue($s3UploadValidatorTransfer->getIsValid());
    }

    /**
     * @test
     *
     * @return void
     */
    public function uploadFormValidationWithFailure(): void
    {
        $this->tester->wantTo('Validate Upload form');

        //Arrange
        $formInterface = $this->createMock(FormInterface::class);
        $formInterface->expects($this->any())->method('isSubmitted')->willReturn(false);
        $formInterface->expects($this->any())->method('isValid')->willReturn(false);

        $s3FileUploadTransfer = (new S3UploadBuilder([
            'bucket' => 'oase-staging-pdf',
            'uploadedFile' => $this->tester->createUploadedFile('Neki content', '11', 'application/pdf'),
        ]))->build();

        $formInterface->expects($this->any())->method('getData')->willReturn($s3FileUploadTransfer);

        //Act
        $s3FilesGuiFacade = $this->tester->getLocator()->s3FilesGui()->facade();
        $s3UploadValidatorTransfer = $s3FilesGuiFacade->validateUpload($formInterface);

        //Assert
        $this->assertNotTrue($s3UploadValidatorTransfer->getIsValid());
    }

    /**
     * @return void
     */
    public function buildDownloadResponseBodyWithEmptyArrayThatReturnsEmptyString(): void
    {
        $this->tester->wantToTest('buildDownloadResponseBody method in DownloadResponseBuilder');

        // Arrange
        $downloadResponseBuilder = new DownloadResponseBuilder();
        $s3FilesResultTransfer = (new S3FilesResultsBuilder())->build();

        // Act
        $s3FilesDownloadResponseTransfer = $downloadResponseBuilder->buildDownloadFilesResponse($s3FilesResultTransfer);

        // Assert
        $this->assertTrue($s3FilesDownloadResponseTransfer->getIsSuccessful());
    }

    /**
     * @param array $formFactoryMethods
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Form\FormFactoryInterface
     */
    private function getFormFactoryMock(array $formFactoryMethods = []): MockObject|FormFactoryInterface
    {
        return $this->getMockForAbstractClass(FormFactoryInterface::class, [], '', false, false, true, $formFactoryMethods);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|(\Symfony\Component\Form\FormView&\PHPUnit\Framework\MockObject\MockObject)
     */
    private function getFormViewMock()
    {
        return $this->getMockBuilder(FormView::class)->getMock();
    }

    /**
     * @return void
     */
    private function registerFormFactoryServiceMock(): void
    {
        $this->tester->getContainer()
            ->set(static::SERVICE_FORM_FACTORY, $this->getFormFactoryMock());
    }
}
