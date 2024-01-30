<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace XiphiasTest\Zed\S3FilesGui\Business\Validator;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\S3UploadBuilder;
use Xiphias\Zed\S3FilesGui\Business\Model\Validator\Validator;
use XiphiasTest\Zed\S3FilesGui\S3FileGuiBusinessTester;
use Spryker\Zed\Translator\Business\TranslatorFacadeInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Auto-generated group annotations
 *
 * @group PyzTest
 * @group Zed
 * @group S3FilesGui
 * @group Business
 * @group Validator
 * @group S3FilesGuiValidatorTest
 * Add your own group annotations below this line
 */
class S3FilesGuiValidatorTest extends Unit
{
    /**
     * @var \XiphiasTest\Zed\S3FilesGui\S3FileGuiBusinessTester
     */
    protected S3FileGuiBusinessTester $tester;

    /**
     * @return void
     */
    public function testValidateUploadFormDataMethodWithInvalidBucketThatFails(): void
    {
        $this->tester->wantToTest('ValidateUploadFormData method with invalid bucket.');

        // Arrange
        $formInterface = $this->createMock(FormInterface::class);
        $formInterface->expects($this->any())->method('isSubmitted')
            ->willReturn(true);
        $formInterface->expects($this->any())->method('isValid')
            ->willReturn(true);

        $translatorFacade = $this->createMock(TranslatorFacadeInterface::class);
        $translatorFacade->expects($this->any())->method('trans')
            ->willReturn('Please choose a bucket to upload to');

        $validatorClass = new Validator($translatorFacade);
        $uploadFormData = (new S3UploadBuilder([
            'bucket' => '',
        ]))->build();

        $formInterface->expects($this->any())->method('getData')
            ->willReturn($uploadFormData);

        // Act
        $validatorTransfer = $validatorClass->validateUploadFormAndData($formInterface);

        // Assert
        $this->assertNotTrue($validatorTransfer->getIsValid());
        $this->assertEquals('Please choose a bucket to upload to', $validatorTransfer->getErrorMessage());
    }
}
