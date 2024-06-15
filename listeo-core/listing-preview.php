<div class="listing_preview_container">

	<?php
	$template_loader = new Listeo_Core_Template_Loader;
	$post = get_post();
	$post_id = $post->ID;
	?>

	<?php
	$gallery_style = 'content';
	$listing_logo = get_post_meta($post_id, '_listing_logo', true);
	if ($gallery_style == 'top') :
		$template_loader->get_template_part('single-partials/single-listing', 'gallery');
	endif;
	$businessdescription = get_the_content();
	$monetization_summary = get_post_meta($post->ID, '_monetization_summary', true);
	$traffic_source_list = get_the_terms($post->ID, 'region');
	?>

	<?php if ($listing_logo) { ?>
		<div class="listing-logo"> <img src="<?php echo $listing_logo; ?>" alt=""></div>
	<?php } ?>

	<div id="titlebar" class="listing-titlebar tw-flex tw-justify-between tw-items-end">
		<div class="listing-titlebar-title">
			<div class="listing-titlebar-tags">
				<?php
				$listing_type = get_post_meta(get_the_ID(), '_listing_type', true);
				$terms = get_the_terms(get_the_ID(), 'listing_category');
				if ($terms && !is_wp_error($terms)) :
					$categories = array();
					foreach ($terms as $term) {

						$categories[] = sprintf(
							'<a href="%1$s">%2$s</a>',
							esc_url(get_term_link($term->slug, 'listing_category')),
							esc_html($term->name)
						);
					}
					$categories_list = join(", ", $categories);
				?>
					<span class="listing-tag">
						<?php echo ($categories_list) ?>
					</span>
				<?php endif; ?>
				<?php
				switch ($listing_type) {
					case 'service':
						$type_terms = get_the_terms(get_the_ID(), 'service_category');
						$taxonomy_name = 'service_category';
						break;
					case 'rental':
						$type_terms = get_the_terms(get_the_ID(), 'rental_category');
						$taxonomy_name = 'rental_category';
						break;
					case 'event':
						$type_terms = get_the_terms(get_the_ID(), 'event_category');
						$taxonomy_name = 'event_category';
						break;
					case 'classifieds':
						$type_terms = get_the_terms(get_the_ID(), 'classifieds_category');
						$taxonomy_name = 'classifieds_category';
						break;

					default:
						# code...
						break;
				}
				if (isset($type_terms)) {
					if ($type_terms && !is_wp_error($type_terms)) :
						$categories = array();
						foreach ($type_terms as $term) {
							$categories[] = sprintf(
								'<a href="%1$s">%2$s</a>',
								esc_url(get_term_link($term->slug, $taxonomy_name)),
								esc_html($term->name)
							);
						}

						$categories_list = join(", ", $categories);
				?>
						<span class="listing-tag">
							<?php echo ($categories_list) ?>
						</span>
				<?php endif;
				}
				?>
				<?php if (get_the_listing_price_range()) : ?>
					<span class="listing-pricing-tag"><i class="fa fa-<?php echo esc_attr(get_option('listeo_price_filter_icon', 'tag')); ?>"></i><?php echo get_the_listing_price_range(); ?></span>
				<?php endif; ?>
			</div>
			<h1><?php the_title(); ?></h1>
		</div>

		<?php
		$askingprice = get_post_meta($post_id, '_asking_price', true);
		if ($askingprice) :
		?>
			<div class="tw-flex tw-flex-col tw-items-end">
				<div class="tw-text-[13px] tw-text-[#999999] tw-font-semibold tw-uppercase">Asking Price</div>
				<div class="tw-text-2xl tw-leading-tight tw-font-semibold tw-text-[#4A1172]">$<label class="number-format tw-inline tw-text-2xl tw-leading-tight tw-font-semibold tw-text-[#4A1172] tw-mb-0"><?php echo esc_html($askingprice) ?></label></div>
			</div>
		<?php endif ?>
	</div>

	<div id="listing-nav" class="listing-nav-container">
		<ul class="listing-nav">
			<li><a href="#listing-overview" class="active"><?php esc_html_e('Overview', 'listeo_core'); ?></a></li>

			<?php if ($businessdescription) : ?>
				<li><a href="#listing-description"><?php esc_html_e('Description', 'listeo_core'); ?></a></li>
			<?php endif; ?>

			<?php if ($monetization_summary) : ?>
				<li><a href="#listing-financials"><?php esc_html_e('Financials', 'listeo_core'); ?></a></li>
			<?php endif; ?>

			<?php if ($traffic_source_list) : ?>
				<li><a href="#listing-traffic"><?php esc_html_e('Traffic', 'listeo_core'); ?></a></li>
			<?php endif; ?>

			<!-- <?php if ($gallery_style == 'content') : ?>
				<li><a href="#listing-gallery"><?php esc_html_e('Gallery', 'listeo_core'); ?></a></li>
			<?php endif; ?> -->

		</ul>
	</div>

	<!-- Overview -->
	<div id="listing-overview" class="listing-section">

		<?php $template_loader->get_template_part('single-partials/single-listing', 'main-details');  ?>

		<!-- Description -->
		<?php
		$businessdescription = get_the_content();
		if ($businessdescription) :
		?>
			<div id="listing-description" class="listing-desc-container tw-pt-6 tw-mt-6 tw-border-0 tw-border-t tw-border-solid tw-border-slate-200">
				<h3 class="listing-desc-headline !tw-mt-0 !tw-mb-6">Business Description</h3>
				<div class="listing-desc-content">
					<?php the_content(); ?>
				</div>
			</div>
		<?php endif; ?>
		<?php $template_loader->get_template_part('single-partials/single-listing', 'financials');  ?>
		<?php $template_loader->get_template_part('single-partials/single-listing', 'traffic');  ?>
		<?php /* $template_loader->get_template_part('single-partials/single-listing', 'socials'); */  ?>
		<?php /* $template_loader->get_template_part('single-partials/single-listing', 'features'); */  ?>

	</div>
	<style>
		#listing-gallery {
			width: calc(100vw - 460px)
		}

		@media (max-width: 992px) {
			#listing-gallery {
				width: calc(100vw - 160px)
			}
		}
	</style>


	<?php if ($gallery_style == 'content') : $template_loader->get_template_part('single-partials/single-listing', 'gallery-content');
	endif; ?>

	<?php $template_loader->get_template_part('single-partials/single-listing', 'pricing');  ?>
	<?php if (class_exists('WeDevs_Dokan') &&  get_post_meta(get_the_ID(), '_store_section_status', 1)) :   $template_loader->get_template_part('single-partials/single-listing', 'store');
	endif; ?>
	<?php $template_loader->get_template_part('single-partials/single-listing', 'opening');  ?>
	<?php $template_loader->get_template_part('single-partials/single-listing', 'video');  ?>
	<?php $template_loader->get_template_part('single-partials/single-listing', 'location');  ?>

</div>
<?php if (get_option('listeo_edit_listing_requires_approval')) { ?>
	<div class="notification closeable notice">
		<?php esc_html_e('Editing listing requires admin approval, your listing will be unpublished if you Save Changes.', 'listeo_core'); ?>
	</div>
<?php } ?>

<form method="post" id="listing_preview">
	<div class="row margin-bottom-30">
		<div class="col-md-12">

			<button type="submit" value="edit_listing" name="edit_listing" class="button border margin-top-20"><i class="fa fa-edit"></i> <?php esc_attr_e('Edit listing', 'listeo_core'); ?></button>
			<!-- <input type="submit" name="continue"> -->
			<button type="submit" value="<?php echo apply_filters('submit_listing_step_preview_submit_text', __('Submit Listing', 'listeo_core')); ?>" name="continue" class="button margin-top-20"><i class="fa fa-check"></i>
				<?php
				if (isset($_GET["action"]) && $_GET["action"] == 'edit') {
					esc_html_e('Save Changes', 'listeo_core');
				} else {
					echo apply_filters('submit_listing_step_preview_submit_text', __('Submit Listing', 'listeo_core'));
				} ?>
			</button>

			<input type="hidden" name="listing_id" value="<?php echo esc_attr($data->listing_id); ?>" />
			<input type="hidden" name="step" value="<?php echo esc_attr($data->step); ?>" />
			<input type="hidden" name="listeo_core_form" value="<?php echo $data->form; ?>" />
		</div>
	</div>
</form>