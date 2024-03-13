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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

let btnWishlist = document.querySelector('#btn-wishlist-arpa')
let btnWishlistCart = document.querySelector('#btn-wishlist-cart-arpa')

if (btnWishlistCart) {
    btnWishlistCart.addEventListener('click',function() {
        let postData = {
            actionAjax: 'addToCartWishlist'
        };
    
        $.ajax({
            type: 'POST',
            url: ajaxAddWishlist,
            data: postData, 
            dataType: 'json',
            success: function (data) {
                let addToCartAccountDiv = document.querySelector('#alert-cart-account')
                let alertWishlistCart = document.createElement('div')
                alertWishlistCart.classList.add('alert','alert-'+ data.type)
                alertWishlistCart.textContent = data.message
                addToCartAccountDiv.appendChild(alertWishlistCart)
                setTimeout(function() {
                    alertWishlistCart.remove();
                }, 3000);
                prestashop.emit(
                    'updateCart',
                    {reason: data.reason}
                  );
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    })
}

if (btnWishlist) {
    btnWishlist.addEventListener('click',function(){
        let productId = document.querySelector("#product_page_product_id").value
        let productIdAttribute = btnWishlist.dataset.idProductAttribute
        let postData = {        
            id_product: productId,
            id_product_attribute: productIdAttribute,
            actionAjax: 'addToWishlist'
        };
    
        $.ajax({
            type: 'POST',
            url: ajaxAddWishlist,
            data: postData, 
            dataType: 'json',
            success: function (data) {
                let addToCartDiv = document.querySelector('.product-add-to-cart')
                let alertWishlist = document.createElement('div')
                alertWishlist.classList.add('alert','alert-'+ data.type)
                alertWishlist.textContent = data.message
                addToCartDiv.appendChild(alertWishlist)
    
                setTimeout(function() {
                    alertWishlist.remove();
                }, 3000);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    })
}

prestashop.on("updatedProduct", function (event) {
    btnWishlist.dataset.idProductAttribute = event.id_product_attribute
})