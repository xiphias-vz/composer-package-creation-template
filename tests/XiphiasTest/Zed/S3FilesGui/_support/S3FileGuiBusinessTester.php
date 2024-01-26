<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace XiphiasTest\Zed\S3FilesGui;

use Codeception\Actor;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class S3FileGuiBusinessTester extends Actor
{
    use _generated\S3FileGuiBusinessTesterActions;

    /**
     * @param string $content
     * @param string $name
     * @param string $mimeType
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function createUploadedFile(string $content, string $name, string $mimeType): UploadedFile
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, $content);

        return new UploadedFile($tempFile, $name, $mimeType);
    }
}
