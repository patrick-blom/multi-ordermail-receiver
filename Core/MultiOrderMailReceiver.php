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

        if ( ! $dbMetaDataHandler->fieldExists('PBOWNEREMAILRECEIVER', 'oxshops')) {
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
        /** @var QueryBuilderFactoryInterface $queryBuilderFactory */
        $queryBuilderFactory = $container->get(QueryBuilderFactoryInterface::class);
        $queryBuilder        = $queryBuilderFactory->create();

        $queryBuilder->select('oxid')
                     ->from('oxtplblocks')
                     ->where('oxmodule = :moduleId')
                     ->setParameters([
                         'moduleId' => 'multiordermailreceiver'
                     ]);

        $row = $queryBuilder->execute()->fetch();

        // deletes are only allowed by primarykey
        if (count($row) > 0 && array_key_exists('oxid', $row)) {
            $queryBuilder->delete('oxtplblocks')
                         ->where('oxid = :id')
                         ->setParameters([
                             'id' => $row['oxid']
                         ]);
            $queryBuilder->execute();
        }
    }
}
