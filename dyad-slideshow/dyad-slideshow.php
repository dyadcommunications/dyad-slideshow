<?php

/**
 * Heart Slideshow Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Load values and assign defaults.
$post_id = get_the_ID();
$parent_id = wp_get_post_parent_id($post_id);
$post_title = get_the_title($post_id);
$parent_title =  accent2ascii(get_the_title($parent_id));

$add_scroll_indicator = false;
$container_classes = "heart-slideshow-block-container";
$parent_classes = "heart-slideshow dyad-slideshow ";
$options = get_field('options');

/* Add any custom classnames */
if (!empty($block['className'])) {
	$parent_classes .= ' ' . $block['className'];
}
// Create id attribute allowing for custom "anchor" value.
$id = $block['id'] . '-slideshow';
if (!empty($block['anchor'])) {
	$id = $block['anchor'];
}

$slideshow_type = $options["complex_slideshow"] ? " complex-slideshow" : "simple-slideshow";
if ($slideshow_type == " complex-slideshow") {
	$slideshow = get_field("complex-slideshow-gallery");
	$parent_classes .= $slideshow_type;
} else {
	$slideshow = get_field("slideshow");
	$parent_classes .= $slideshow_type;
}

$add_captions = $options["has_captions"] ? " has-captions" : "no-captions";
if ($add_captions == " has-caption") {
	$container_classes .= " has-captions";
	$parent_classes .= " has-captions";
}

$has_overlay = $options["has_overlay"] ? " has-overlay" : " no-overlay";
if ($has_overlay == " has-overlay") {
	$container_classes .= $has_overlay;
	$overlay_content = get_field("overlay_content");
}


?>

<section class="<?= $container_classes ?>">
	<?php if ($has_overlay == " has-overlay") : ?>
		<div class="overlay-container">
			<div class="content-container">
				<h2><?= $post_title ?></h2>
				<?= $overlay_content ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="<?= esc_attr($parent_classes); ?>" id="<?= esc_attr($id); ?>">
		<?php if ($slideshow_type == " complex-slideshow") : ?>
			<?php if (have_rows('complex_slideshow_gallery')) : ?>
				<?php while (have_rows('complex_slideshow_gallery')) : the_row();
					$slide_classes = "heart-slide";
					$images = get_sub_field('image_set');
					$image_count = count($images);
					if ($image_count > 1) $slide_classes .= " has-multiple-imgs";

					$ratio_inline_style = null;
					if ($image_count > 0) {
						// RATIO: add all widths, plus 15px per image, then divide by single height
						// $total_width = 15 * ($image_count - 1);
						$total_width = 0;
						$total_height = 0;
						foreach ($images as $image) {
							$w = $image['sizes']['large-width'];
							$h = $image['sizes']['large-height'];
							$total_width += $w;
							$total_height += $h;
						}
						$averaged_total_height = $total_height / $image_count;
						$total_ratio = $total_width / $averaged_total_height;
						$ratio_inline_style = "--slide-ratio: $total_ratio;--image-count: $image_count";
					}
				?>

					<div class='<?= $slide_classes ?>' data-image-count="<?= $image_count ?>" style="<?= $ratio_inline_style ?>">
						<div class="image-container">
							<?php
							foreach ($images as $image) :

								// Image Info
								$alt = $image['alt'];
								$image_id = $image['ID'];
								// Generate Srcset
								$srcset = wp_get_attachment_image_srcset($image_id);
								$fallback_src = wp_get_attachment_image_url($image_id, 'medium_large');

								// Image Ratio
								$w = $image['width'];
								$h = $image['height'];
								$r = round($w / $h, 4);
								$o = $w >= $h ? ' is-horizontal' : ' is-vertical';
								// Image Focal Point
								$focal_point = get_field('set_focal_point', $image_id);
								switch ($focal_point) {
									case 'custom':
										$focal_point = get_field('custom_focal_point', $image_id);
										break;

									case NULL:
									case '50-50':
										$focal_point = NULL;
										break;

									default:
										$focal_point = str_replace('-', '% ', $focal_point) . '%';
										break;
								}
								$focus_style = $focal_point ? "style=\"object-position: $focal_point; font-family: 'object-fit: cover; object-position: $focal_point;';\"" : NULL;

							?>
								<figure class='image-holder <?= $o ?>'>
									<div class="ratio">
										<img <?= ($focal_point) ? $focus_style : ""; ?> data-sizes="(min-width:768px) 100vw, 140vw" alt='<?= $alt; ?>' data-srcset='<?= esc_attr($srcset); ?>' data-src='<?= $fallback_src; ?>' />
									</div>

									<?php if ($add_captions == " has-captions") : ?>
										<figcaption><?= $caption ?></figcaption>
									<?php endif; ?>

								</figure>

							<?php endforeach; ?>
						</div>
					</div>
				<?php endwhile; ?>

			<?php endif; ?>

		<?php else :  ?>

			<?php foreach ($slideshow as $slide) : ?>
				<?php
				// Image Info
				$alt = $slide['alt'];
				$image_id = $slide['ID'];
				// Generate Srcset
				$srcset = wp_get_attachment_image_srcset($image_id);
				$fallback_src = wp_get_attachment_image_url($image_id, 'medium_large');

				if ($add_captions == " has-captions") $caption = wp_get_attachment_caption($image_id);
				$nav_color = get_field("nav_color", $image_id);

				// Image Ratio
				$w = $slide['width'];
				$h = $slide['height'];
				$r = round($w / $h, 4);
				$o = $w >= $h ? ' is-horizontal' : ' is-vertical';
				// Image Focal Point
				$focal_point = get_field('set_focal_point', $image_id);
				switch ($focal_point) {
					case 'custom':
						$focal_point = get_field('custom_focal_point', $image_id);
						break;

					case NULL:
					case '50-50':
						$focal_point = NULL;
						break;

					default:
						$focal_point = str_replace('-', '% ', $focal_point) . '%';
						break;
				}
				$focus_style = $focal_point ? "style=\"object-position: $focal_point; font-family: 'object-fit: cover; object-position: $focal_point;';\"" : NULL;
				?>
				<div class='heart-slide' data-nav-color="<?= $nav_color ?>">
					<figure class='image-holder <?= $o ?>'>
						<img <?= ($focal_point) ? $focus_style : ""; ?> data-sizes="(min-width:768px) 100vw, 140vw" alt='<?= $alt; ?>' data-srcset='<?= esc_attr($srcset); ?>' data-src='<?= $fallback_src; ?>' />
						<?php if ($add_captions == " has-captions") : ?>
							<figcaption><?= $caption ?></figcaption>
						<?php endif; ?>
					</figure>
				</div>
			<?php endforeach ?>
		<?php endif; ?>
	</div>

	<?php if ($add_captions == " has-captions") : ?>
		<figcaption class="slideshow-caption"><em></em></figcaption>
	<?php endif; ?>

	<?php if ($add_scroll_indicator) : ?>
		<p class="scroll-indicator">scroll</p>
	<?php endif; ?>
</section>