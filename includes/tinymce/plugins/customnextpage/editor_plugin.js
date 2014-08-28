(function($){

	tinymce.create( 'tinymce.plugins.CustomNextpage', {

		getInfo : function() {
			return {
				longname  : 'Custom Next Page Plugin',
				author    : 'Webnist',
				authorurl : 'http://profiles.wordpress.org/webnist',
				infourl   : 'http://profiles.wordpress.org/webnist',
				version   : '0.9.1'
			};
		},

		init : function( ed, url ) {
			var t = this, inputs = {}, image, shortCode, selectedElm, nodeName, anchorElm;

			image         = '<img src="' + url + '/img/custom-next-page.png" class="custom-next-tag mceItemNoResize" alt="" /><br>';
			shortCode     = '[nextpage]';
			inputs.dialog = $('#customnextpage-dialog');
			inputs.submit = $('#customnextpage-submit');
			inputs.cancel = $('#customnextpage-cancel');
			inputs.title  = $('#customnextpage-title-field');

			// Replace nextpage with images
			ed.onBeforeSetContent.add( function( ed, o ) {
				if ( o.content )
					o.content = t._do_code( o.content, image );
			});

			// Replace images with nextpage
			ed.onPostProcess.add( function( ed, o ) {
				if ( o.get )
					o.content = t._get_code( o.content, shortCode );
			});

			ed.addButton( 'customnextpage', {
				cmd   : 'customnextpage_cmd',
				title : ed.getLang( 'customnextpage.buttonTitle', 'Custom Nextpage Shortcode' ),
				image : url + '/img/custom-next-page-icon.png'
			});

			ed.addCommand( 'customnextpage_cmd', function() {
				selectedElm = this.selection.getNode();
				nodeName    = selectedElm.nodeName;
				if ( nodeName === 'IMG' ) {
					anchorElm = ed.dom.getParent( selectedElm, 'img.custom-next-tag' );
					value     = ed.dom.getAttrib( anchorElm, 'alt' );
					inputs.title.val( value );
				}
				ed.windowManager.open({
					id       : 'customnextpage-dialog',
					title    : ed.getLang( 'customnextpage.popupTitle', 'Custom Nextpage Shortcode' ),
					height   : 'auto',
					wpDialog : true
				}, {
					plugin_url : url
				});
			});

			inputs.submit.click( function(e){
				e.preventDefault();

				if ( alt = inputs.title.val() )
					image = image.replace( /alt="(.*?)"/g, 'alt="' + alt + '"' );

				ed.execCommand( "mceInsertContent", false, image );
				inputs.dialog.wpdialog('close');
				inputs.title.val('');
				ed.focus();
				return false;
			});

			inputs.cancel.click( function(e){
				e.preventDefault();
				inputs.dialog.wpdialog('close');
				ed.focus();
			});
			inputs.dialog.keydown(t.keydown);

			ed.onNodeChange.add( function( ed, cm, n ) {
				cm.setActive( 'customnextpage', n.nodeName === 'IMG' && ed.dom.hasClass( n, 'custom-next-tag' ) );
			});
		},
		_do_code : function( co, image ) {
			return co.replace(/\[nextpage(.*?)\]/g, function( a ) {
				if ( a.indexOf( 'title=' ) !== -1 ) {
					title = a.match( /title="([^"]+)"/ );
					return image.replace( /alt="(.*?)"/g, 'alt="' + title[1] + '"' );
				}
				return image;
			});
		},
		_get_code : function( co, shortCode ) {
			return co.replace(/<img[^>]+>/g, function( a ) {
				if ( a.indexOf( 'custom-next-tag' ) !== -1 ) {
					title = a.match( /alt="([^"]+)"/ );
					if ( title ) {
						return '[nextpage title="' + title[1] + '"]';
					}
				}
				return shortCode;
			});
		},
		keydown : function( event ) {
			var ed = tinyMCEPopup.editor, fn, key = $.ui.keyCode;
			if ( event.which !== key.ESCAPE )
				return;

			$('#customnextpage-dialog').wpdialog('close');
			$('#customnextpage-title-field').val('');
			ed.focus();
			event.preventDefault();
		}

	});

	// register plugin
	tinymce.PluginManager.add( 'customnextpage', tinymce.plugins.CustomNextpage );
})(jQuery);
