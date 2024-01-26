<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace XiphiasTest\Zed\S3FilesGui\Communication\Controller;

use XiphiasTest\Zed\S3FilesGui\PageObject\S3FilesGuiIndexPage;
use XiphiasTest\Zed\S3FilesGui\S3FileGuiCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group XiphiasTest
 * @group Zed
 * @group S3FilesGui
 * @group Communication
 * @group Controller
 * @group IndexControllerCest
 * Add your own group annotations below this line
 */
class IndexControllerCest
{
    /**
     * @param \XiphiasTest\Zed\S3FilesGui\S3FileGuiCommunicationTester $i
     *
     * @return void
     */
    public function testICanTriggerIndexAction(S3FileGuiCommunicationTester $i): void
    {
        $i->wantTo('Test index action of S3 controller.');

        $i->amOnPage(S3FilesGuiIndexPage::URL);
        $i->seeResponseCodeIs(200);

        $i->listDataTable(S3FilesGuiIndexPage::TABLE_URL);
        $i->canSeeDataTable();
    }
}
