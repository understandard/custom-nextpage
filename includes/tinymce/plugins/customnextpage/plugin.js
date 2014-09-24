tinymce.PluginManager.add('customnextpage', function(editor) {

	// Replace nextpage with images
	editor.on( 'BeforeSetContent', function( e ) {
		if ( e.content ) {
			if ( e.content.indexOf( '[nextpage' ) !== -1 ) {
				url   = tinymce.PluginManager.urls.customnextpage;
				image = '<img src="' + url + '/img/custom-next-page.png" class="custom-next-tag" alt="" data-mce-resize="false" data-mce-placeholder="1" /><br>';
				e.content = e.content.replace( /\[nextpage(.*?)\]/g, function( a ) {
					if ( a.indexOf( 'title=' ) !== -1 ) {
						title = a.match( /title="([^"]+)"/ );
						return image.replace( /alt="(.*?)"/g, 'alt="' + title[1] + '"' );
					}
					return image;
				});
			}
			if ( e.content.indexOf( '<a' ) !== -1 ) {
				return '';
			}

		}
	});

	// Replace images with nextpage
	editor.on( 'PostProcess', function( e ) {
		if ( e.get ) {
			e.content = e.content.replace( /<img[^>]+>/g, function( image ) {
				var match, moretext = '';

				if ( image.indexOf( 'custom-next-tag' ) !== -1 ) {
					if ( match = image.match( /alt="([^"]+)"/ ) ) {
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
		var data = {}, selection = editor.selection, dom = editor.dom, selectedElm, anchorElm, win, value;

		selectedElm = selection.getNode();
		anchorElm   = dom.getParent(selectedElm, 'img.custom-next-tag');

		if ((value = dom.getAttrib(anchorElm, 'alt'))) {
			data.title = value;
		}

		win = editor.windowManager.open({
			title : editor.getLang( 'customnextpage.popupTitle', 'Custom Nextpage Shortcode' ),
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
		icon             : 'customnextpage',
		tooltip          : editor.getLang( 'customnextpage.buttonTitle', 'Custom Nextpage Shortcode' ),
		onclick          : showDialog,
		context          : 'insert',
		stateSelector    : 'img.custom-next-tag',
		prependToContext : true
	});

});
