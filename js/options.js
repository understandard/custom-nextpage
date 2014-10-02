(function($){
	$(window).load(function() {
		if ( $('select#styletype')[0] ) {
			var val = $('select#styletype').val();
			if ( 1 == val ) {
				$('select#styletype').parents('tr').next('tr').show();
			} else {
				$('select#styletype').parents('tr').next('tr').hide();
			}
			$('select#styletype').change(function() {
				val = $(this).val();
				if ( 1 == val ) {
					$(this).parents('tr').next('tr').show();
				} else {
					$(this).parents('tr').next('tr').hide();
				}
			});
		}
		if ( $('div#custom-next-page-options')[0] ) {
			$('div#custom-next-page-options').children('form').children('h3').each( function( i ) {
				var wrapId     = 'options-wrap-' + ( i + 1 );
				var optionName = $(this).text();
				if ( i === 0 ) {
					$('div#custom-next-page-options').children('form').before('<ul id="options-nav"><li data-nav="' + wrapId + '" class="current">' + optionName + '</li></ul>');
					$(this).next('table').wrap('<div id="' + wrapId + '" class="options-wrap options-wrap-current"></div>');
				} else {
					$('ul#options-nav').append('<li data-nav="' + wrapId + '">' + optionName + '</li>');
					$(this).next('table').wrap('<div id="' + wrapId + '" class="options-wrap"></div>');
				}
				$(this).prependTo('#' + wrapId);
			});
		}
		if ( $('ul#options-nav')[0] ) {
			$('ul#options-nav').on( 'click', 'li', function() {
				if ( !$(this).hasClass( 'current' ) ) {
					var target = $(this).attr('data-nav');
					$(this).nextAll( 'li' ).removeClass( 'current' );
					$(this).prevAll( 'li' ).removeClass( 'current' );
					$('div#custom-next-page-options').children('form').children( '.options-wrap' ).removeClass( 'options-wrap-current' );
					$(this).addClass( 'current' );
					$('div#custom-next-page-options').children('form').children( '#' + target ).addClass( 'options-wrap-current' );
				}
			});
		}
	});
})(jQuery);
