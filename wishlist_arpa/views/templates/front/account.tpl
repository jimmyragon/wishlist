{extends file='customer/page.tpl'}

{block name='page_title'}
    Ma liste d'envie
{/block}

{block name='page_content'}
{if !$products}
    <p>Vous n'avez pas de produit dans votre liste d'envie</p>
{else}
    <div id="alert-cart-account"></div>
    <a id="btn-wishlist-cart-arpa" class="btn btn-secondary mb-2">Ajouter au panier</a>
    {foreach from=$products item=$product }
        <div class="row">
            <div class="col-md-2">
                <img src="{$product.image}" class="img-fluid">
            </div>
            <div class="col-md-10">
                <h2>{$product.name}</h2>
                <span class="current-price-value" content="14.28">
                    {$product.price}&nbsp;€
                </span>
                {if $product.attribute}
                    <br>
                    {foreach from=$product.attribute item=$attribute }
                        <span class="badge">{$attribute.name}</span><br>
                    {/foreach}
                {/if}
            </div>
        </div>
        <hr>
    {/foreach}
{/if}

{/block}
