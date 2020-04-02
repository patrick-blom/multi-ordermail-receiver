<?php

namespace PaBlo\MultiOrderMailReceiver\Core;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * Class MultiOrderMailReceiver
 * @package PaBlo\MultiOrderMailReceiver\Core
 */
class MultiOrderMailReceiver
{
    /**
     * Adds a custom field to oxshops table to store the additional mail addresses
     *
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public static function onActivate(): void
    {
        $dbMetaDataHandler = oxNew(DbMetaDataHandler::class);

        if (!$dbMetaDataHandler->fieldExists('PBOWNEREMAILRECEIVER', 'oxshops')) {
            DatabaseProvider::getDb()->execute(
                "ALTER TABLE oxshops ADD PBOWNEREMAILRECEIVER text NOT NULL default '' COMMENT 'Additional recipients for order owner mail';"
            );
        }
    }

    /**
     * Ensures that the template blocks will be cleared on module deactivation.
     */
    public static function onDeactivate(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $queryBuilderFactory = $container->get(QueryBuilderFactoryInterface::class);

        $queryBuilder = $queryBuilderFactory->create();
        $queryBuilder->delete('oxtplblocks', 'tpl')
            ->where('tpl.oxmodule = :moduleId')
            ->setParameters([
                'moduleId' => 'multiordermailreceiver'
            ]);
    }
}
