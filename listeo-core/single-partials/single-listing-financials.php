<!-- Financials -->
<?php
$monetization_list = get_the_terms($post->ID, 'listing_feature');
$monetization_summary = get_post_meta($post->ID, '_monetization_summary', true);
$ttm_gross_revenue = get_post_meta($post->ID, '_ttm_gross_revenue', true);
$ttm_net_profit = get_post_meta($post->ID, '_ttm_net_profit', true);
$revenue_multiple = get_post_meta($post->ID, '_revenue_multiple', true);
$profit_multiple = get_post_meta($post->ID, '_profit_multiple', true);
?>

<div id="listing-financials" class="listing-desc-container tw-pt-6 tw-mt-6 tw-border-0 tw-border-t tw-border-solid tw-border-[#e0e0e0]">
	<h3 class="listing-desc-headline !tw-mt-0 !tw-mb-6">Financials</h3>

	<?php if ($monetization_summary) : ?>
		<div class="tw-mb-6">
			<?php echo esc_html($monetization_summary) ?>
		</div>
	<?php endif ?>

	<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-4 md:tw-gap-4 tw-my-6">

		<?php if ($ttm_gross_revenue) : ?>
			<div class="flex flex-col">
				<div class="tw-text-[#999999] tw-text-[13px] tw-font-semibold tw-uppercase">Annual gross revenue</div>
				<div class="tw-text-base tw-font-semibold"><span class="number-format tw-text-base tw-font-semibold"><?php echo esc_html($ttm_gross_revenue) ?></span></div>
			</div>
		<?php endif; ?>

		<?php if ($revenue_multiple) : ?>
			<div class="flex flex-col">
				<div class="tw-text-[#999999] tw-text-[13px] tw-font-semibold tw-uppercase">Revenue Multiple</div>
				<div class="tw-text-base tw-font-semibold"><?php echo esc_html($revenue_multiple) ?>x</div>
			</div>
		<?php endif; ?>

		<?php if ($ttm_net_profit) : ?>
			<div class="flex flex-col">
				<div class="tw-text-[#999999] tw-text-[13px] tw-font-semibold tw-uppercase">Annual net profit</div>
				<div class="tw-text-base tw-font-semibold"><span class="number-format tw-text-base tw-font-semibold"><?php echo esc_html($ttm_net_profit) ?></span></div>
			</div>
		<?php endif; ?>

		<?php if ($profit_multiple) : ?>
			<div class="flex flex-col">
				<div class="tw-text-[#999999] tw-text-[13px] tw-font-semibold tw-uppercase">Profit Multiple</div>
				<div class="tw-text-base tw-font-semibold"><?php echo esc_html($profit_multiple) ?>x</div>
			</div>
		<?php endif; ?>

	</div>

	<?php
	$monetization_list = get_the_terms($post->ID, 'listing_feature');
	if (!empty($monetization_list)) :
	?>
		<h4 class="tw-text-base tw-font-semibold">Revenue sources:</h4>
		<ul class="listing-features checkboxes margin-top-0">
			<?php
			foreach ($monetization_list as $term) {
				echo '';
				$term_link = get_term_link($term);
				if (is_wp_error($term_link))
					continue;
				$t_id = $term->term_id;
				if (isset($t_id)) {
					$_icon_svg = get_term_meta($t_id, '_icon_svg', true);
					$_icon_svg_image = wp_get_attachment_image_src($_icon_svg, 'medium');
				}
				if (isset($_icon_svg_image) && !empty($_icon_svg_image)) {
					$icon = listeo_render_svg_icon($_icon_svg);
					//$icon = '<img class="listeo-map-svg-icon" src="'.$_icon_svg_image[0].'"/>';


				} else {

					if (empty($icon)) {
						$icon = get_post_meta($post->ID, '_icon', true);
					}
				}
				if (!empty($icon)) {
					echo '<li class="feature-has-icon"><span class="feature-svg-icon">' . $icon . '</span><a href="' . esc_url($term_link) . '">' . $term->name . '</a></li>';
				} else {
					echo '<li class="feature-no-icon"><a href="' . esc_url($term_link) . '">' . $term->name . '</a></li>';
				}
				$icon = false;
			}
			?>
		</ul>
	<?php endif ?>

</div>