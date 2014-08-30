(function($){
	QTags.addButton( 'custom_nextpage', 'Custom Nextpage', CustomNextpageCallback, '', '', 'Custom Nextpage', 999 );
	function CustomNextpageCallback( e, c, ed ) {
		title = prompt('Enter Title');
		if ( title ) {
			value = '[nextpage title="' + title + '"]';
			this.tagStart = value;
			QTags.TagButton.prototype.callback.call(this, e, c, ed);
		} else {
			value = '[nextpage]';
			this.tagStart = value;
			QTags.TagButton.prototype.callback.call(this, e, c, ed);
		}
		return;
	}
})(jQuery);
