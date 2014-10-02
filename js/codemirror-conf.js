var jsEditor = CodeMirror.fromTextArea(document.getElementById('custom-next-page[style]'), {
	mode: 'text/css',
	lineNumbers: true,
	matchBrackets: true,
	extraKeys: {"Ctrl-Space": "autocomplete"}
});
jsEditor.save();