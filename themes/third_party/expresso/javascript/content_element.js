function element_display(cell)
{
	var textarea = cell.find('textarea');
	var field_id = textarea.data('field-id');
	var id = field_id + '_' + Math.floor(Math.random()*10000);
	var file_uploads = eval('expresso_file_upload_content_element_' + field_id);
	var config = eval('expresso_config_content_element_' + field_id);

	$(textarea).attr('id', id);
	
	expresso(id, file_uploads, config);
}


$(function()
{
	ContentElements.bind('expresso', 'display', function(cell) {
		element_display(cell);
	});

	ContentElements.bind('expresso', 'beforeSort', function(cell){
		var textarea = cell.find('textarea');
		var html = cell.find('iframe:first')[0].contentDocument.body.innerHTML;
		$(textarea).html(html);
	});
	
	ContentElements.bind('expresso', 'afterSort', function(cell) {
		var textarea = cell.find('textarea');
		textarea.remove();
		cell.find('.content_elements_tile_body').append(textarea);

		// remove old instance of ckeditor
		cell.find('.cke').remove();
		CKEDITOR.remove(CKEDITOR.instances[textarea.attr('id')]);

		element_display(cell);
	});
});