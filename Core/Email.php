<?php

namespace PaBlo\MultiOrderMailReceiver\Core;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Email\EmailValidatorServiceBridgeInterface;

/**
 * Class Email
 * @package PaBlo\MultiOrderMailReceiver\Core
 * @see \OxidEsales\Eshop\Core\Email
 */
class Email extends Email_parent
{
    /**
     * Array of carbon copy email addresses
     *
     * @var array
     */
    protected $_aCarbonCopies = [];

    /**
     * @var bool
     */
    protected $_blCarbonCopyActiveState = false;

    /**
     * Sets mailer additional settings and sends ordering mail to shop owner.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\Order $order Order object
     * @param string $subject user defined subject [optional]
     *
     * @return bool
     */
    public function sendOrderEmailToOwner($order, $subject = null)
    {
        /*Only activate the carbon copy on owner emails*/
        $this->setCarbonCopyActive();

        return parent::sendOrderEmailToOwner($order, $subject);
    }

    /**
     * Preventing possible email spam over php mail() exploit (http://www.securephpwiki.com/index.php/Email_Injection)
     *
     * @param string $address
     * @param null $name
     * @param bool $auto
     *
     * @return bool
     */
    public function setFrom($address, $name = null, $auto = true)
    {
        $success = parent::setFrom($address, $name, $auto);

        if (true === $this->getCarbonCopyActiveState()) {
            $shop = $this->_getShop();

            if (!empty($shop->oxshops__pbowneremailreceiver->value)) {
                $carbonCopyMails = explode(';', $shop->oxshops__pbowneremailreceiver->value);

                if (count($carbonCopyMails) > 0) {
                    $container = ContainerFactory::getInstance()->getContainer();
                    $validationService = $container->get(EmailValidatorServiceBridgeInterface::class);

                    $language = \OxidEsales\Eshop\Core\Registry::getLang();
                    $name = $language->translateString("order");

                    foreach ($carbonCopyMails as $carbonCopyMailAddress) {
                        if ($validationService->isEmailValid($carbonCopyMailAddress)) {
                            $this->setCarbonCopy($carbonCopyMailAddress, $name);
                        }
                    }
                }
            }
        }

        return $success;
    }

    /**
     * Sets mail carbon copy to carbon copies array
     *
     * @param string $address recipient email address
     * @param string $name recipient name
     */
    public function setCarbonCopy($address = null, $name = null): void
    {
        try {
            $address = $this->idnToAscii($address);

            $this->addCC($address, $name);

            // copying values as original class does not allow to access recipients array
            $this->_aCarbonCopies[] = [$address, $name];
        } catch (Exception $exception) {
        }
    }

    /**
     * Gets carbon copy array.
     * Returns array of recipients
     * f.e. array( array('mail1@mail1.com', 'user1Name'), array('mail2@mail2.com', 'user2Name') )
     *
     * @return array
     */
    public function getCarbonCopy(): array
    {
        return $this->_aCarbonCopies;
    }

    /**
     * Clears all recipients assigned in the TO, CC and BCC array.
     */
    public function clearAllCarbonCopies(): void
    {
        $this->_aCarbonCopies = [];
        $this->clearAllRecipients();
    }

    /**
     * Convert domain name to IDNA ASCII form.
     *
     * @param string $idn The email address
     *
     * @return string
     */
    protected function idnToAscii($idn)
    {
        if (function_exists('idn_to_ascii')) {
            // for old PHP versions support
            // remove it after the PHP 7.1 support is dropped
            if (defined('INTL_IDNA_VARIANT_UTS46')) {
                return idn_to_ascii($idn, 0, INTL_IDNA_VARIANT_UTS46);
            }

            return idn_to_ascii($idn);
        }

        return $idn;
    }

    /**
     * Sets the carbon copy state to active
     */
    private function setCarbonCopyActive(): void
    {
        $this->_blCarbonCopyActiveState = true;
    }

    /**
     * Returns the current carbon copy state
     *
     * @return bool
     */
    private function getCarbonCopyActiveState(): bool
    {
        return $this->_blCarbonCopyActiveState;
    }
}
