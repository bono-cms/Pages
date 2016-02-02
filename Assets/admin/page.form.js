$(function(){
	$("[name='controller']").change(function(){
		$("[name='protected']").prop('checked', true);
	});
});