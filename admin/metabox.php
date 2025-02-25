<?php

/**
 * Meta boxes functions for the plugin.
 *
 * @package    Theme_Junkie_Portfolio_Content
 * @since      0.1.0
 * @author     Theme Junkie
 * @copyright  Copyright (c) 2014, Theme Junkie
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

/* Register meta boxes. */
add_action('add_meta_boxes', 'tjpc_add_meta_boxes');

/* Save meta boxes. */
add_action('save_post', 'tjpc_meta_boxes_save', 10, 2);

/**
 * Registers new meta boxes for the 'portfolio_item' post editing screen in the admin.
 *
 * @since  0.1.0
 * @access public
 * @link   http://codex.wordpress.org/Function_Reference/add_meta_box
 */
function tjpc_add_meta_boxes() {

	/* Check if current screen is Portfolio page. */
	if ('portfolio' != get_current_screen()->post_type)
		return;

	add_meta_box(
		'tjpc-metaboxes-portfolio',
		__('Project Settings', 'theme-junkie-portfolio-content'),
		'tjpc_metaboxes_display',
		'portfolio',
		'normal',
		'high'
	);
}

/**
 * Displays the content of the meta boxes.
 *
 * @param  object  $post
 * @since  0.1.0
 * @access public
 */
function tjpc_metaboxes_display($post) {

	wp_nonce_field(basename(__FILE__), 'tjpc-metaboxes-portfolio-nonce'); ?>

	<div id="tjpc-block">

		<div class="tjpc-label">
			<label for="tjpc-portfolio-gallery">
				<strong><?php _e('Project Gallery', 'theme-junkie-portfolio-content'); ?></strong><br />
				<span class="description"><?php _e('Upload the project image gallery.', 'theme-junkie-portfolio-content'); ?></span>
			</label>
		</div>

		<div class="tjpc-input">

			<a href="#" class="tjpc-open-media button" title="<?php esc_attr_e('Add Images', 'theme-junkie-portfolio-content'); ?>"><?php _e('Add Images', 'theme-junkie-portfolio-content'); ?></a>

			<?php $image_id = get_post_meta($post->ID, 'tj_image_ids', true); ?>
			<?php $ids = array_filter(explode(',', $image_id)); ?>

			<ul id="tjpc-images-list">
				<?php if ($ids) { ?>
					<?php foreach ($ids as $id) { ?>
						<li class="tjpc-image" data-image-id="<?php echo $id; ?>">
							<?php echo wp_get_attachment_image($id, 'thumbnail'); ?>
							<a href="#" class="tjpc-delete" title="<?php esc_attr_e('Remove image', 'theme-junkie-portfolio-content'); ?>">
								<div class="dashicons dashicons-no"></div>
							</a>
						</li>
					<?php } ?>
				<?php } ?>
			</ul>

			<input type="hidden" name="tjpc-portfolio-gallery-ids" id="tjpc-portfolio-gallery-ids" value="<?php echo get_post_meta($post->ID, 'tj_image_ids', true); ?>" />

		</div>

	</div><!-- #tjpc-block -->

	<div id="tjpc-block">

		<div class="tjpc-label">
			<label for="tjpc-portfolio-short-desc">
				<strong><?php _e('Short Description', 'theme-junkie-portfolio-content'); ?></strong><br />
				<span class="description"><?php _e('A short description of the project.', 'theme-junkie-portfolio-content'); ?></span>
			</label>
		</div>

		<div class="tjpc-input">
			<input type="text" name="tjpc-portfolio-short-desc" id="tjpc-portfolio-short-desc" value="<?php echo sanitize_text_field(get_post_meta($post->ID, 'tj_portfolio_short_desc', true)); ?>" size="30" style="width: 99%;" placeholder="<?php esc_attr_e('A short project description.', 'theme-junkie-portfolio-content'); ?>" />
		</div>

	</div><!-- #tjpc-block -->

	<div id="tjpc-block">

		<div class="tjpc-label">
			<label for="tjpc-portfolio-client">
				<strong><?php _e('Client Name', 'theme-junkie-portfolio-content'); ?></strong><br />
				<span class="description"><?php _e('The client name of the project.', 'theme-junkie-portfolio-content'); ?></span>
			</label>
		</div>

		<div class="tjpc-input">
			<input type="text" name="tjpc-portfolio-client" id="tjpc-portfolio-client" value="<?php echo sanitize_text_field(get_post_meta($post->ID, 'tj_portfolio_client', true)); ?>" size="30" style="width: 99%;" placeholder="<?php esc_attr_e('Company Name', 'theme-junkie-portfolio-content'); ?>" />
		</div>

	</div><!-- #tjpc-block -->

	<div id="tjpc-block">

		<div class="tjpc-label">
			<label for="tjpc-portfolio-url">
				<strong><?php _e('Project Link', 'theme-junkie-portfolio-content'); ?></strong><br />
				<span class="description"><?php _e('The link of the project.', 'theme-junkie-portfolio-content'); ?></span>
			</label>
		</div>

		<div class="tjpc-input">
			<input type="text" name="tjpc-portfolio-url" id="tjpc-portfolio-url" value="<?php echo esc_url(get_post_meta($post->ID, 'tj_portfolio_link', true)); ?>" size="30" style="width: 99%;" placeholder="<?php echo esc_attr('http://'); ?>" />
		</div>

	</div><!-- #tjpc-block -->

	<div id="tjpc-block">

		<div class="tjpc-label">
			<label for="tjpc-portfolio-video">
				<strong><?php _e('Video Embedded Code', 'theme-junkie-portfolio-content'); ?></strong><br />
				<span class="description"><?php _e('Put the video URL here. Image Gallery will be hidden.', 'theme-junkie-portfolio-content'); ?></span>
			</label>
		</div>
		<div class="tjpc-input">
			<input type="text" name="tjpc-portfolio-video" id="tjpc-portfolio-video" value="<?php echo esc_url(get_post_meta($post->ID, 'tj_video_embed_portfolio', true)); ?>" size="30" style="width: 99%;" placeholder="<?php echo esc_attr('http://'); ?>" />
		</div>

	</div><!-- #tjpc-block -->

<?php
}

/**
 * Saves the metadata for the portfolio item info meta box.
 *
 * @param  int     $post_id
 * @param  object  $post
 * @since  0.1.0
 * @access public
 */
function tjpc_meta_boxes_save($post_id, $post) {

	if (!isset($_POST['tjpc-metaboxes-portfolio-nonce']) || !wp_verify_nonce($_POST['tjpc-metaboxes-portfolio-nonce'], basename(__FILE__)))
		return;

	if (!current_user_can('edit_post', $post_id))
		return;

	$meta = array(
		'tj_image_ids'             => wp_filter_post_kses($_POST['tjpc-portfolio-gallery-ids']),
		'tj_portfolio_short_desc'  => wp_filter_post_kses($_POST['tjpc-portfolio-short-desc']),
		'tj_portfolio_client'      => wp_filter_post_kses($_POST['tjpc-portfolio-client']),
		'tj_portfolio_link'        => esc_url($_POST['tjpc-portfolio-url']),
		'tj_video_embed_portfolio' => esc_url($_POST['tjpc-portfolio-video'])
	);

	foreach ($meta as $meta_key => $new_meta_value) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta($post_id, $meta_key, true);

		/* If there is no new meta value but an old value exists, delete it. */
		if (current_user_can('delete_post_meta', $post_id, $meta_key) && '' == $new_meta_value && $meta_value)
			delete_post_meta($post_id, $meta_key, $meta_value);

		/* If a new meta value was added and there was no previous value, add it. */
		elseif (current_user_can('add_post_meta', $post_id, $meta_key) && $new_meta_value && '' == $meta_value)
			add_post_meta($post_id, $meta_key, $new_meta_value, true);

		/* If the new meta value does not match the old value, update it. */
		elseif (current_user_can('edit_post_meta', $post_id, $meta_key) && $new_meta_value && $new_meta_value != $meta_value)
			update_post_meta($post_id, $meta_key, $new_meta_value);
	}
}
