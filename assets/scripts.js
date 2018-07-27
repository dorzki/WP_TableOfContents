( function ( $ ) {

	$( '.post_toc li a' )
		.on( 'click', function ( e ) {
			e.preventDefault();

			$( 'html, body' ).animate( {
				scrollTop : $( $( this ).attr( 'href' ) ).offset().top
			} );

		} );

} )( jQuery );