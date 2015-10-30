
$(function(){
	
	$.wysiwyg.init(['page[content]']);
	
	$("[name='controller']").change(function(){
		$("[name='protected']").prop('checked', true);
	});
});