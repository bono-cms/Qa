$(function(){
	$("[name='qa[date_asked]'], [name='qa[date_answered]']").datepicker();
	$.wysiwyg.init(['qa[answer]']);
});