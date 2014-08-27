tinymce.PluginManager.add('customnextpage', function(editor) {

	// Replace Read More/Next Page tags with images
	editor.on( 'BeforeSetContent', function( e ) {
		if ( e.content ) {
			if ( e.content.indexOf( '[nextpage' ) !== -1 ) {
				e.content = e.content.replace( /\[nextpage(.*?)\]/g, function( match, moretext ) {
					moretext = moretext.replace( / title="(.*?)"/g, '$1' );
					return '<img src="' + tinymce.Env.transparentSrc + '" class="wp-custom-next-tag" ' +
						'title="' + moretext + '" data-mce-resize="false" data-mce-placeholder="1" />';
				});
			}
		}
	});

	// Replace images with tags
	editor.on( 'PostProcess', function( e ) {
		if ( e.get ) {
			e.content = e.content.replace(/<img[^>]+>/g, function( image ) {
				var match, moretext = '';

				if ( image.indexOf('wp-custom-next-tag') !== -1 ) {
					if ( match = image.match( /data-wp-more="([^"]+)"/ ) ) {
						moretext = match[1];
						moretext = ' title="' + moretext + '"';
					}
					image = '[nextpage' + moretext + ']';
				}

				return image;
			});
		}
	});

	function showDialog() {
		var data = {}, selection = editor.selection, dom = editor.dom, selectedElm, anchorElm, initialText, win, value;

		selectedElm = selection.getNode();
		anchorElm   = dom.getParent(selectedElm, 'img.wp-custom-next-tag');

		if ((value = dom.getAttrib(anchorElm, 'title'))) {
			data.title = value;
		}

		win = editor.windowManager.open({
			title : "Custom Nextpage Shortcode",
			body  : [{
				type  : 'textbox',
				name  : 'title',
				label : 'Title',
				value : data.title
			}],
			onsubmit: function( e ) {
				var title = e.data.title;
				if ( title ) {
					var shortcode = '[nextpage title="' + title + '"]';
				} else {
					var shortcode = '[nextpage]';
				}
				editor.insertContent( shortcode );
			}
		});
	}

	editor.addButton('customnextpage', {
		icon             : 'wp_more',
		tooltip          : 'Custom Nextpage Shortcode',
		onclick          : showDialog,
		context          : 'insert',
		stateSelector    : 'img.wp-custom-next-tag',
		prependToContext : true
	});

});
