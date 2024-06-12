<div class="dashboard-list-box">
	<div class="pad-top30">
		
		<p>Submission received!</p>
		<p><?php // Successful

		switch ( get_post_status( $data->id ) ) {
			case 'publish' :
				esc_html_e( 'Your listing has been published.', 'listeo_core' );
			break;				
			case 'pending_payment' :
				esc_html_e( 'Your listing has been saved and is pending payment. It will be published once the order is completed', 'listeo_core' );
			break;			
			case 'pending' :
			case 'draft' :
				esc_html_e( 'Your listing has been saved. Our verification team will review your submission as soon as possible. Depending on the information you provided and our backlog, it can take anywhere between 2 and 72 hours for your listing to go live. Sometimes it might take as long as 5 working days.', 'listeo_core' );
			break;
			default :
				esc_html_e( 'Your changes have been saved.', 'listeo_core' );
			break;
		} ?>
		</p>
		<?php if(get_post_status( $data->id ) == 'publish') : ?>
			<a class="button margin-top-30" href="<?php echo get_permalink( $data->id ); ?>"><?php  esc_html_e( 'View &rarr;', 'listeo_core' );  ?></a>
		<?php endif; ?>
	</div>
</div>

