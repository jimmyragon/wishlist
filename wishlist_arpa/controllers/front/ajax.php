<?php

class Wishlist_arpaAjaxModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();

    }

    public function postProcess()
    {
        switch (Tools::getValue('actionAjax')) {
            case 'addToWishlist':
                $this->addProductToWishlist();
                break;
            case 'addToCartWishlist':
            $this->addToCart();
                break;
            
            default:
                exit();
                break;
        }
        $this->addProductToWishlist();
    }
    public function addToCart() {
        try {
            if (!$this->context->cart->id){
                $this->context->cart->add();
                $cart = new Cart();
                if ($this->context->cart->id) {
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                }
            } else {
                $cart = new Cart($this->context->cookie->id_cart);
            } 
            $cart->id_currency = $this->context->cookie->id_currency;

            $query = (new DbQuery())
                ->select('*')
                ->from('wishlist_arpa')
                ->where('id_customer = '. Context::getContext()->customer->id)
            ;

            $results = Db::getInstance()->executeS($query);

            if ($results) {
                foreach ($results as $productWislist) {
                    $product = new Product($productWislist['id_product']);
                    if (!$product->id || !$product->active) {
                        throw new Exception("Error Processing Request", 1);
                        
                    } else {
                        if ($productWislist['id_product_attribute'] != 0) {
                            $cart->updateQty(1, $productWislist['id_product'],$productWislist['id_product_attribute']);
                        } else {
                            $cart->updateQty(1, $productWislist['id_product']);
                        }
                    }
                }
            } else {
                exit(json_encode(
                    [
                        'message' => "Pas de produit dans la wishlist",
                        'type' => 'danger',
                        'reason' => null
                    ]
                ));
            }
    
            exit(json_encode(
                [
                    'message' => "Produit(s) ajouté(s) dans le panier",
                    'type' => 'success',
                    'reason' => true
                ]
            ));
        } catch (\Throwable $th) {
            exit(json_encode(
                [
                    'message' => "Erreur",
                    'type' => 'danger',
                    'reason' => null
                ]
            ));
        }
        
    }


    public function addProductToWishlist() {
        if (!Context::getContext()->customer->id) {
            exit(json_encode(
                [
                    'message' => "Veuillez vous identifier.",
                    'type' => 'danger'
                ]
            ));
        }
        
        if (Validate::isInt(Tools::getValue('id_product'))) {

            $product = new Product(Tools::getValue('id_product'));
            $idProductAttribute = Tools::getValue('id_product_attribute');
            if ($idProductAttribute) {
                if ( $idProductAttribute != 0 && Validate::isInt(Tools::getValue('id_product_attribute'))) {
                    $combination = new Combination(Tools::getValue('id_product_attribute'));
                    if ($combination->id_product != $product->id) {
                        exit(json_encode(
                            [
                                'message' => "Le produit n'existe pas",
                                'type' => 'danger'
                            ]
                        ));
                    }
                }
            }
        }
        if (!$product->id ) {
            exit(json_encode(
                [
                    'message' => "Le produit n'existe pas",
                    'type' => 'danger'
                ]
            ));
        } else {
            $query = (new DbQuery())
                ->select('*')
                ->from('wishlist_arpa')
                ->where('id_customer = '. Context::getContext()->customer->id)
                ->where('id_product_attribute = '. $idProductAttribute)
                ->where('id_product = '. $product->id)
                ->where('id_shop = '. Context::getContext()->shop->id)
            ;

            $results = Db::getInstance()->executeS($query);
            if ($results) {
                Db::getInstance()->delete(
                    'wishlist_arpa',
                    'id_customer = '.(int)Context::getContext()->customer->id.
                    ' AND id_product_attribute = '.(int)$idProductAttribute.
                    ' AND id_product = '.(int)$product->id.
                    ' AND id_shop = '.(int)Context::getContext()->shop->id
                );
                exit(json_encode(
                    [
                        'message' => "Produit supprimé de votre wishlist",
                        'type' => 'danger'
                    ]
                ));
            }
            
        }

        
        DbCore::getInstance()->insert("wishlist_arpa",array(
            "id_product" => $product->id,
            "id_product_attribute" => Tools::getValue('id_product_attribute'),
            "id_customer" => Context::getContext()->customer->id,
            "id_shop" => Context::getContext()->shop->id
        ));
        exit(json_encode(
            [
                'message' => "Produit ajouté à votre wishlist",
                'type' => 'success'
            ]
        ));
    }
}