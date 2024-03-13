<?php

class Wishlist_arpaAccountModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();

    }
    public function initContent()
    {
        parent::initContent();

        if (Context::getContext()->customer->isLogged()) {
            $query = (new DbQuery())
                ->select('*')
                ->from('wishlist_arpa')
                ->where('id_customer = '. Context::getContext()->customer->id)
            ;

            $results = Db::getInstance()->executeS($query);
            $products = [];
            foreach ($results as $productWishlist) {
                $product = new Product($productWishlist['id_product']);
                if ($productWishlist['id_product_attribute'] != 0) {
                    $imageCombination = $product->getCombinationImages(Context::getContext()->language->id);
                    $image = $imageCombination[$productWishlist['id_product_attribute']][0];
                } else {
                    $image = Image::getCover($productWishlist['id_product']);

                }

                if ($image) {
                    $imagePath = $this->context->link->getImageLink($product->link_rewrite[1], $image['id_image'], 'cart_default');
                } else {
                    $imagePath = '/img/p/fr-default-cart_default.jpg';
                }

                if ($productWishlist['id_product_attribute'] != 0) {
                    $price = $product->getPrice(true,$productWishlist['id_product_attribute'],2);

                    $combination = new Combination($productWishlist['id_product_attribute']);
                    $attribute = $combination->getAttributesName(Context::getContext()->language->id);
                } else {
                    $price = $product->getPrice(true,null,2);
                    $attribute = null;
                }
                $products[] = [
                    "name" => $product->name[1],
                    "image" => $imagePath,
                    "price" => $price,
                    "attribute" => $attribute
                ];

            }
            $this->context->smarty->assign([
                'products' => $products
            ]);
            $this->setTemplate('module:wishlist_arpa/views/templates/front/account.tpl');

        } else {

            Tools::redirect('index.php?controller=authentication');

        }

        
    }
}