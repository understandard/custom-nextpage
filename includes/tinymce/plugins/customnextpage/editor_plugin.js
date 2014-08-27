(function($){

	tinymce.create( 'tinymce.plugins.CustomNextpage', {

		init : function( ed, url ) {
			var t = this, inputs = {}, rivers = {}, ed, River, Query, shortcode = '[nextpage]';
			inputs.dialog   = $('#customnextpage-dialog');
			inputs.submit   = $('#customnextpage-submit');
			inputs.title    = $('#customnextpage-title-field');
			nextHtml = '<img src="' + url + '/img/custom-next-page.png" class="custom-next-tag" />';

			ed.addButton( 'customnextpage', {
				cmd   : 'customnextpage_cmd',
				title : ed.getLang( 'customnextpage.buttonTitle', 'Custom Nextpage Shortcode' )
			});

			ed.addCommand( 'customnextpage_cmd', function() {
				ed.windowManager.open({
					id       : 'customnextpage-dialog',
					width    : 480,
					title    : ed.getLang( 'customnextpage.popupTitle', 'Custom Nextpage Shortcode' ),
					height   : 'auto',
					wpDialog : true,
					modal: true,
				}, {
					plugin_url : url
				});
			});
			inputs.submit.click( function(e){
				e.preventDefault();

				if ( inputs.title.val() )
					nextHtml = '<img src="' + url + '/img/custom-next-page.png" class="custom-next-tag mceItemNoResize" alt="' + inputs.title.val() + '" />';

				ed.execCommand( "mceInsertContent", false, nextHtml );
				inputs.dialog.wpdialog('close');
				inputs.title.val('');
				ed.focus();
				return false;
			});

			t._handleMoreBreak(ed, url);

		},
		_handleMoreBreak : function(ed, url) {
			nextHtml = '<img src="' + url + '/img/custom-next-page.png" class="custom-next-tag" />';

			// Replace morebreak with images
			ed.onBeforeSetContent.add(function(ed, o) {
				if ( o.content ) {
					if ( o.content.indexOf( '[nextpage' ) !== -1 ) {
						o.content = o.content.replace( /\[nextpage(.*?)\]/g, function( match, nexttext ) {
							nexttext = nexttext.replace( / title="(.*?)"/g, '$1' );
							nextHtml = '<img src="' + url + '/img/custom-next-page.png" class="custom-next-tag" alt="' + nexttext + '" />';
							return nextHtml;
						});
					}
				}

			});

			// Replace images with morebreak
			ed.onPostProcess.add(function(ed, o) {
				if ( o.get ) {
					o.content = o.content.replace(/<img[^>]+>/g, function( im ) {
						var match, moretext = '';

						if ( im.indexOf('custom-next-tag') !== -1 ) {
							if ( match = im.match( /alt="([^"]+)"/ ) ) {
								moretext = match[1];
								moretext = ' title="' + moretext + '"';
							}
							im = '[nextpage' + moretext + ']';
						}

						return im;
					});
				}
			});

			// Set active buttons if user selected pagebreak or more break
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('wp_page', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'wp-custom-next-tag'));
			});
		}

	});
	// register plugin
	tinymce.PluginManager.add( 'customnextpage', tinymce.plugins.CustomNextpage );
})(jQuery);
