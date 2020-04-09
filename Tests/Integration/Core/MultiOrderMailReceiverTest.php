<?php

namespace PaBlo\MultiOrderMailReceiver\Test\Integration\Core;

use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use PaBlo\MultiOrderMailReceiver\Core\MultiOrderMailReceiver;


/**
 * Class MultiOrderMailReceiverTest
 * UNIT/INTEGRATION tests for core class MultiOrderMailReceiver.
 *
 * @package PaBlo\MultiOrderMailReceiver\Test\Integration\Core
 */
class MultiOrderMailReceiverTest extends UnitTestCase
{
    /**
     * Subject under the test.
     *
     * @var MultiOrderMailReceiver
     */
    protected $SUT;

    /**
     * Set SUT state before test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->SUT = new MultiOrderMailReceiver();
    }

    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\MultiOrderMailReceiver::onActivate
     */
    public function testOnActivate_willAddCustomFieldToTheDatabase(): void
    {
        // ensure field does not exists
        $dbMetaDataHandler = oxNew(DbMetaDataHandler::class);
        $dbMetaDataHandler->executeSql(['ALTER TABLE oxshops DROP PBOWNEREMAILRECEIVER;']);
        $this->assertFalse($dbMetaDataHandler->fieldExists('PBOWNEREMAILRECEIVER', 'oxshops'));

        $this->SUT::onActivate();
        $this->assertTrue($dbMetaDataHandler->fieldExists('PBOWNEREMAILRECEIVER', 'oxshops'));
    }

    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\MultiOrderMailReceiver::onDeactivate
     */
    public function testOnDeactivate_willRemoveTemplateBlocks(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();

        // ensure the module is active
        /** @var QueryBuilderFactoryInterface $queryBuilderFactory */
        $queryBuilderFactory = $container->get(QueryBuilderFactoryInterface::class);

        $queryBuilder = $queryBuilderFactory->create();
        $queryBuilder->select('oxid')
                     ->from('oxtplblocks', 'tpl')
                     ->where('tpl.oxmodule = :moduleId')
                     ->setParameters([
                         'moduleId' => 'multiordermailreceiver'
                     ]);

        $result = $queryBuilder->execute()->fetchAll();
        $this->assertCount(1, $result);

        $this->SUT::onDeactivate();

        $result = $queryBuilder->execute()->fetchAll();
        $this->assertCount(0, $result);
    }
}

