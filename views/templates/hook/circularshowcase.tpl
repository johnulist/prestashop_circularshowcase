
<!-- Block circularshowcase -->
<div class="block">
  <h4>{Configuration::get('SHOWING_PRODUCTS')} PRODUCTS</h4>

			<div id="ca-container" class="ca-container">
				<div class="ca-wrapper">
    			{foreach from=$new_products item=newproduct name=myLoop}
					<div class="ca-item ca-item-1">
						<div class="ca-item-main">

								<div id="featured-products_block_center" class="block products_block clearfix">
									<ul>
										<li class="ajax_block_product item  last_line">
											<a href="{$newproduct.link}" title="{$newproduct.legend|escape:html:'UTF-8'}"><img src="{$link->getImageLink($newproduct.link_rewrite, $newproduct.id_image, 'home_default')}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$newproduct.legend|escape:html:'UTF-8'}" /></a>
											<p style="font-weight:bold;font-style:normal;font-size:12px;padding-bottom:10px;"><a href="{$newproduct.link}" title="{$newproduct.name|escape:html:'UTF-8'}">{$newproduct.name|strip_tags|escape:html:'UTF-8'}</a></p>
											<div class="product_desc" style="margin-bottom:10px;">{if $newproduct.description_short}<a href="{$newproduct.link}" title="More">{$newproduct.description_short|strip_tags:'UTF-8'|truncate:75:'...'}</a>{/if}</div>
											<div><a class="lnk_more" href="{$newproduct.link}" title="View">View</a>
{if $newproduct.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
<p class="price_container"><span class="price">{convertPrice price=$newproduct.price_tax_exc}</span></p>
{/if}

											</div>
										</li>
									</ul>
								</div>

						</div>
						
					</div>
					{/foreach}


				</div>

			</div>
		<script type="text/javascript">

			$('#ca-container').contentcarousel();

		</script>

</div>
<!-- /Block circularshowcase -->
