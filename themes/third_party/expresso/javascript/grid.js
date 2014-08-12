function grid_display(cell)
{
	var textarea = cell.find('textarea');
	var field_id = textarea.data('field-id');
	var id = field_id + '_' + cell.data('row-id') + '_' + cell.data('column-id')+'_' + Math.floor(Math.random()*10000);
	var file_uploads = eval('expresso_file_upload_grid_field_id_' + field_id);
	var config = eval('expresso_config_grid_field_id_' + field_id);

	$(textarea).attr('id', id);
	
	expresso(id, file_uploads, config);
}


$(function()
{
	Grid.bind('expresso', 'display', function(cell) {
		grid_display(cell);
	});
	
	Grid.bind('expresso', 'beforeSort', function(cell){
		var textarea = cell.find('textarea');
		var html = cell.find('iframe:first')[0].contentDocument.body.innerHTML;
		$(textarea).html(html);
	});
	
	Grid.bind('expresso', 'afterSort', function(cell) {
		var textarea = cell.find('textarea');
		cell.empty().append(textarea);

		// remove old instance of ckeditor
		CKEDITOR.remove(CKEDITOR.instances[textarea.attr('id')]);

		grid_display(cell);
	});
});