(function(){
	tinymce.create('tinymce.plugins.customnextpage', {
		createControl : function(id, controlManager) {
			if (id == 'customnextpage') {
				// creates the button
				var button = controlManager.createButton('customnextpage', {
					title  : 'Custom Nextpage Shortcode',
					image  : '../wp-content/plugins/custom-nextpage/images/wp_page.gif',
					onclick: function() {
						// triggers the thickbox
						var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
						W = W - 80;
						H = H - 84;
						tb_show( 'Custom Nextpage Shortcode', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=customnextpage-form' );
					}
				});

				return button;
			}

			return null;
		}
	});

	tinymce.PluginManager.add('customnextpage', tinymce.plugins.customnextpage);
	jQuery(function(){
		var form = jQuery('<div id="customnextpage-form"><table id="customnextpage-table" class="form-table">\
			<tr>\
				<th><label for="customnextpage-title">Title</label></th>\
				<td><input type="text" id="customnextpage-title" name="title" value="" /></td>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="customnextpage-submit" class="button-primary" value="Insert Custom Nextpage" name="submit" />\
		</p>\
		</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		form.find('#customnextpage-submit').click(function(){
			var shortcode = '[nextpage';

			var value = table.find('#customnextpage-title').val();

			if ( value )
				shortcode += ' title="' + value + '"';

			shortcode += ']';

			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			tb_remove();
		});
	});
})();