<?php
/**
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Wishlist_arpa extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'wishlist_arpa';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Jimmy';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('wishlist arpa');
        $this->description = $this->l('module de wishlist arpa3');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayCustomerAccount') &&
            $this->registerHook('displayProductActions');

    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    
    public function hookDisplayCustomerAccount()
    {
        if (!$this->context->cart->id){
            $this->context->cart->add();
            $cart = new Cart();
            if ($this->context->cart->id) {
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
            }
        }
        $this->smarty->assign([
            'urlAccountWishlist' => $this->context->link->getModuleLink('wishlist_arpa', 'account'),
        ]);
        return $this->display(__FILE__, 'account-wishlist.tpl');
    }

    public function hookDisplayProductActions($params)
    {
        $this->smarty->assign([
            'id_product_attribute' => $params['product']['id_product_attribute'],
        ]);
        return $this->display(__FILE__, 'button-add-wishlist.tpl');
    }
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        Media::addJsDef(array(

            "ajaxAddWishlist" => $this->context->link->getModuleLink('wishlist_arpa', 'ajax')

        ));
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
}
