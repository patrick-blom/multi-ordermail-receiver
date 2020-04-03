Multi-Admin-Order-Mail-Receiver module
==================

This module adds the possibility for multiple admin order mail receivers to OXID eShop. 
The additional receivers will be added as carbon copy entries to the regular admin order mail.

### Compatability

* This module is OXID eShop 6.2 only 

### Module installation via composer

* create a new folder called "thirdparty" with the subfolder "pb" at the shop root level (same level as the composer.json)
    * `cd <shop root>`
    * `mkdir -p thirdparty/pb`  
* clone the repository to the new folder
    * `git clone git@github.com:patrick-blom/multi-ordermail-receiver.git thirdparty/pb/MultiOrderMailReceiver` 
* navigate back to the shop root level and add the repository to composer.json
    * `composer config repositories.patrick-blom/multi-ordermail-receiver path thirdparty/pb/MultiOrderMailReceiver`
* add the module to your shop composer.json
    * `composer require patrick-blom/multi-ordermail-receiver`
* prepare the module configuration for eShop 6.2
    * `vendor/bin/oe-console oe:module:install-configuration source/modules/pb/MultiOrderMailReceiver/`
    * `vendor/bin/oe-console oe:module:apply-configuration`
* activate the module
    * `vendor/bin/oe-console oe:module:activate multiordermailreceiver`
* regenerate the unified namespace and the views, because the module adds new database fields
    * `vendor/bin/oe-eshop-unified_namespace_generator`
    * `vendor/bin/oe-eshop-db_views_regenerate`

## Usage

- After the installation you will find a new mail address field called `Additional e-mail addresses for orders` under:
    - `Master Settings -> Core Settings -> Main (right side)`
- The additional mail addresses must be separated by a semicolon (;)
   
