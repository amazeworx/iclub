<?php

/**
 * listing Submission Form
 */

if (!defined('ABSPATH')) exit;
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift($roles);
if (!in_array($role, array('administrator', 'admin', 'owner', 'seller'))) :
	$template_loader = new Listeo_Core_Template_Loader;
	$template_loader->get_template_part('account/owner_only');
	return;
endif;

$fields = array();
if (isset($data)) :
	$fields	 	= (isset($data->fields)) ? $data->fields : '';
endif;
if (isset($_GET["action"])) {
	$form_type = $_GET["action"];
} else {
	$form_type = 'submit';
}

$packages = $data->packages;
$user_packages = $data->user_packages;

global $woocommerce;
// $woocommerce->cart->empty_cart();


?>
<form method="post" id="package_selection" class="listing-package-selection">

	<?php if ($packages || $user_packages) :
		$checked = 1;
	?>

		<?php if ($user_packages) : ?>

		<?php endif; ?>

		<?php if ($packages) : ?>

			<p>Selling an online business at Investors Club is completely free. You can list your business, communicate with buyers, and complete the entire process without spending a penny. </p>
			<p>We offer additional marketing packages to increase the visibility of your listings. These packages bundle various marketing add-ons together at a discount, providing a cost-effective way to boost your listing's reach. </p><br />

			<div class="new-pricing-packages-container">

				<?php
				$counter = 0;
				$single_buy_products = get_option('listeo_buy_only_once');
				foreach ($packages as $key => $package) :

					$product = wc_get_product($package);

					if (!$product->is_type(array('listing_package', 'listing_package_subscription')) || !$product->is_purchasable()) {
						continue;
					}

					if ($single_buy_products) {
						$user = wp_get_current_user();
						if (in_array($product->get_id(), $single_buy_products)  && wc_customer_bought_product($user->user_email, $user->ID, $product->get_id())) {
							continue;
						}
					}
					$user_id = get_current_user_id();
					if ($product->is_type(array('listing_package_subscription')) && wcs_is_product_limited_for_user($product, $user_id)) {
						continue;
					}
				?>
					<div class="pricing-package  <?php echo ($product->is_featured()) ? 'best-value-plan' : ''; ?>">
						<div class="pricing-package-header">
							<h4><?php echo $product->get_title(); ?></h4>
							<?php if ($product->is_featured()) { ?><span><?php esc_html_e('Best Value', 'listeo_core') ?></span> <?php } ?>
						</div>
						<?php if ($product->get_short_description()) { ?><div class="pricing-package-text"><?php echo $product->get_short_description(); ?></div><?php } ?>

						<div class="pricing-package-price">
							<strong><?php echo $product->get_price_html(); ?></strong>
						</div>
						<div class="pricing-package-details">
							<?php
							//get product meta field _package_subtitle
							$package_subtitle = get_post_meta($product->get_id(), '_package_subtitle', true);
							if ($package_subtitle) {
								echo '<h6>' . $package_subtitle . '</h6>';
							} else { ?>
								<h6><?php echo $product->get_title(); ?> features:</h6>
							<?php }
							?>

							<ul>
								<?php
								// Add simple product to packages
								?>
								<?php if (!$product->is_type(array('simple'))) : ?>
									<?php
									// Add simple product to packages
									?>
									<?php if (get_option('listeo_populate_listing_package_options')) : ?>
										<?php
										$bookingOptions = $product->has_listing_booking();
										if ($bookingOptions) : ?>
											<li><svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42">
													<g id="Group_33" data-name="Group 33" transform="translate(-1122 -2972.25)">
														<circle id="Ellipse_4" data-name="Ellipse 4" cx="21" cy="21" r="21" transform="translate(1122 2972.25)" fill="rgba(248,0,68,0.11)" />
														<path id="Vector" d="M6,12.655l4.9,4.9,9.795-9.8" transform="translate(1129.5 2979.993)" fill="none" stroke="#f80044" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
													</g>
												</svg>
												<?php esc_html_e('Booking Module enabled', 'listeo_core');  ?>
											</li>
										<?php endif; ?>


										<?php
										$reviewsOptions = $product->has_listing_reviews();
										if ($reviewsOptions) : ?>
											<li><svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42">
													<g id="Group_33" data-name="Group 33" transform="translate(-1122 -2972.25)">
														<circle id="Ellipse_4" data-name="Ellipse 4" cx="21" cy="21" r="21" transform="translate(1122 2972.25)" fill="rgba(248,0,68,0.11)" />
														<path id="Vector" d="M6,12.655l4.9,4.9,9.795-9.8" transform="translate(1129.5 2979.993)" fill="none" stroke="#f80044" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
													</g>
												</svg>
												<?php esc_html_e('Reviews Module enabled', 'listeo_core');  ?>
											</li>
										<?php endif; ?>

										<?php
										$sociallinksOptions = $product->has_listing_social_links();
										if ($sociallinksOptions) : ?>
											<li><svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42">
													<g id="Group_33" data-name="Group 33" transform="translate(-1122 -2972.25)">
														<circle id="Ellipse_4" data-name="Ellipse 4" cx="21" cy="21" r="21" transform="translate(1122 2972.25)" fill="rgba(248,0,68,0.11)" />
														<path id="Vector" d="M6,12.655l4.9,4.9,9.795-9.8" transform="translate(1129.5 2979.993)" fill="none" stroke="#f80044" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
													</g>
												</svg>
												<?php esc_html_e('Social Links Module enabled', 'listeo_core');  ?>
											</li>
										<?php endif; ?>

										<?php
										$openinghoursOptions = $product->has_listing_opening_hours();
										if ($openinghoursOptions) : ?>
											<li><svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42">
													<g id="Group_33" data-name="Group 33" transform="translate(-1122 -2972.25)">
														<circle id="Ellipse_4" data-name="Ellipse 4" cx="21" cy="21" r="21" transform="translate(1122 2972.25)" fill="rgba(248,0,68,0.11)" />
														<path id="Vector" d="M6,12.655l4.9,4.9,9.795-9.8" transform="translate(1129.5 2979.993)" fill="none" stroke="#f80044" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
													</g>
												</svg>
												<?php esc_html_e('Opening Hours Module enabled', 'listeo_core');  ?>
											</li>
										<?php endif; ?>

										<?php
										$vidosOptions = $product->has_listing_video();
										if ($vidosOptions) : ?>
											<li><svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42">
													<g id="Group_33" data-name="Group 33" transform="translate(-1122 -2972.25)">
														<circle id="Ellipse_4" data-name="Ellipse 4" cx="21" cy="21" r="21" transform="translate(1122 2972.25)" fill="rgba(248,0,68,0.11)" />
														<path id="Vector" d="M6,12.655l4.9,4.9,9.795-9.8" transform="translate(1129.5 2979.993)" fill="none" stroke="#f80044" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
													</g>
												</svg>
												<?php esc_html_e('Video option enabled', 'listeo_core');  ?>
											</li>
										<?php endif; ?>

										<?php
										$couponsOptions = $product->has_listing_coupons();
										if ($couponsOptions == 'yes') : ?>
											<li><svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42">
													<g id="Group_33" data-name="Group 33" transform="translate(-1122 -2972.25)">
														<circle id="Ellipse_4" data-name="Ellipse 4" cx="21" cy="21" r="21" transform="translate(1122 2972.25)" fill="rgba(248,0,68,0.11)" />
														<path id="Vector" d="M6,12.655l4.9,4.9,9.795-9.8" transform="translate(1129.5 2979.993)" fill="none" stroke="#f80044" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
													</g>
												</svg>
												<?php esc_html_e('Coupons option enabled', 'listeo_core');  ?>
											</li>
										<?php endif; ?>

										<?php
										$galleryOptions = $product->has_listing_gallery();
										if ($galleryOptions == 'yes') : ?>
											<li><svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42">
													<g id="Group_33" data-name="Group 33" transform="translate(-1122 -2972.25)">
														<circle id="Ellipse_4" data-name="Ellipse 4" cx="21" cy="21" r="21" transform="translate(1122 2972.25)" fill="rgba(248,0,68,0.11)" />
														<path id="Vector" d="M6,12.655l4.9,4.9,9.795-9.8" transform="translate(1129.5 2979.993)" fill="none" stroke="#f80044" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
													</g>
												</svg>
												<?php esc_html_e('Gallery Module enabled', 'listeo_core');  ?>
											</li>
										<?php endif; ?>
										<?php
										$gallery_limitOptions = $product->get_option_gallery_limit();
										if ($gallery_limitOptions) : ?>
											<li><svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42">
													<g id="Group_33" data-name="Group 33" transform="translate(-1122 -2972.25)">
														<circle id="Ellipse_4" data-name="Ellipse 4" cx="21" cy="21" r="21" transform="translate(1122 2972.25)" fill="rgba(248,0,68,0.11)" />
														<path id="Vector" d="M6,12.655l4.9,4.9,9.795-9.8" transform="translate(1129.5 2979.993)" fill="none" stroke="#f80044" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
													</g>
												</svg>
												<?php printf(esc_html__('Maximum  %s images in gallery', 'listeo_core'), $product->get_option_gallery_limit());  ?>
											</li>
										<?php endif; ?>
									<?php endif; ?>
									<?php
									// Add simple product to packages
									?>
								<?php endif; ?>
								<?php
								// Add simple product to packages
								?>

							</ul>
							<?php

							echo $product->get_description();

							?>
						</div>
						<div class="pricing-package-select">
							<input type="radio" <?php if (!$user_packages && $counter == 0) : ?> checked="checked" <?php endif; ?> name="package" value="<?php echo $product->get_id(); ?>" id="package-<?php echo $product->get_id(); ?>" />
							<label for="package-<?php echo $product->get_id(); ?>">
								<span class="plan-unchecked"><i class="fa fa-check"></i> <?php esc_html_e('Select This Package', 'listeo_core') ?> </span>
								<span class="plan-checked"> <i class="fa fa-check"></i> <?php esc_html_e('Selected', 'listeo_core') ?></span>
							</label>
						</div>
					</div>
				<?php $counter++;
				endforeach; ?>
			</div>
		<?php endif; ?>
		</ul>
	<?php else : ?>

		<p><?php _e('No packages found', 'listeo_core'); ?></p>

	<?php endif; ?>

	<div class="submit-page">

		<p>
			<input type="hidden" name="listeo_core_form" value="<?php echo $data->form; ?>" />
			<input type="hidden" name="listing_id" value="<?php echo esc_attr($data->listing_id); ?>" />
			<input type="hidden" name="step" value="<?php echo esc_attr($data->step); ?>" />
			<button type="submit" name="continue" class="button">Continue <i class="fa fa-arrow-circle-right"></i></button>


		</p>

</form>
</div>