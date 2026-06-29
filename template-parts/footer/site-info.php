<?php
/**
 * Displays footer site info
 *
 * @package WordPress
 * @subpackage 吾侪主题&wuchaiwp
 * @since 吾侪主题&wuchaiwp 1.0
 * @version 1.0
 */

?>
<div class="site-info">
    
	<?php
	if ( function_exists( 'the_privacy_policy_link' ) ) {
		the_privacy_policy_link( '', '<span role="separator" aria-hidden="true"></span>' );
	}
	?>
	<a href="<?php echo esc_url( __( 'http://wuchai.net/', '吾侪主题&wuchaiwp' ) ); ?>" class="imprint">
		<?php
			/* translators: %s: WordPress */
		printf( __( 'Proudly powered by %s', '吾侪主题&wuchaiwp' ), '吾侪主题&wuchaiwp' );
		?>
	</a>
	
	
	<div style="margin-top:10px;magin-bottom:10px">
<table>
<tr>
<?php //echo io_display_site_age('2025-08-24', '自2025年08月24日起，主页已运行 %d 天'); ?>
主页访问统计：<?php the_views() ?>
</tr>
</table>
</div>


	
</div><!-- .site-info -->
