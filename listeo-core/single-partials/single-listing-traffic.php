<!-- Traffic -->
<?php
$traffic_source_list = get_the_terms($post->ID, 'region');
$traffic_summary = get_post_meta($post->ID, '_traffic_summary', true);
?>

<div id="listing-traffic" class="listing-desc-container tw-pt-6 tw-mt-6 tw-border-0 tw-border-t tw-border-solid tw-border-[#e0e0e0]">
	<h3 class="listing-desc-headline !tw-mt-0 !tw-mb-6">Traffic</h3>

	<?php if ($traffic_summary) : ?>
		<div class="tw-mb-6">
			<?php echo esc_html($traffic_summary) ?>
		</div>
	<?php endif ?>

	<?php
	$traffic_source_list = get_the_terms($post->ID, 'region');
	if (!empty($traffic_source_list)) :
	?>
		<h4 class="tw-text-base tw-font-semibold">Traffic sources:</h4>
		<ul class="listing-features checkboxes margin-top-0">
			<?php
			foreach ($traffic_source_list as $term) {
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