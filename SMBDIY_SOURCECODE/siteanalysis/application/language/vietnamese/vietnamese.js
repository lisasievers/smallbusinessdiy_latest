if ($.fn.pagination){
	$.fn.pagination.defaults.beforePageText = 'Trang';
	$.fn.pagination.defaults.afterPageText = 'của {pages}';
	$.fn.pagination.defaults.displayMsg = 'Hiển thị {from} đến {to} của {total} mặt hàng';
}
if ($.fn.datagrid){
	$.fn.datagrid.defaults.loadMsg = 'Xử lý, vui lòng đợi ...';
}
if ($.fn.treegrid && $.fn.datagrid){
	$.fn.treegrid.defaults.loadMsg = $.fn.datagrid.defaults.loadMsg;
}
if ($.messager){
	$.messager.defaults.ok = 'Được';
	$.messager.defaults.cancel = 'hủy bỏ';
}
$.map(['validatebox','textbox','filebox','searchbox',
		'combo','combobox','combogrid','combotree',
		'datebox','datetimebox','numberbox',
		'spinner','numberspinner','timespinner','datetimespinner'], function(plugin){
	if ($.fn[plugin]){
		$.fn[plugin].defaults.missingMessage = 'Trường này là bắt buộc.';
	}
});
if ($.fn.validatebox){
	$.fn.validatebox.defaults.rules.email.message = 'Vui lòng nhập một địa chỉ email hợp lệ.';
	$.fn.validatebox.defaults.rules.url.message = 'Vui lòng nhập một URL hợp lệ.';
	$.fn.validatebox.defaults.rules.length.message = 'Vui lòng nhập một giá trị giữa {0} và {1}.';
	$.fn.validatebox.defaults.rules.remote.message = 'Please fix this field.';
}
if ($.fn.calendar){
	$.fn.calendar.defaults.weeks = ['S','M','T','W','T','F','S'];
	$.fn.calendar.defaults.months = ['tháng Giêng', 'Tháng hai', 'tháng Ba', 'tháng tư', 'có thể', 'tháng sáu', 
	'tháng bảy', 'tháng Tám', 'Tháng Chín', 'Tháng Mười', 'tháng mười một', 'tháng mười hai'];
}
if ($.fn.datebox){
	$.fn.datebox.defaults.currentText = 'Hôm nay';
	$.fn.datebox.defaults.closeText = 'Gần';
	$.fn.datebox.defaults.okText = 'Được';
}

if ($.fn.datetimebox && $.fn.datebox){
	$.extend($.fn.datetimebox.defaults,{
		currentText: $.fn.datebox.defaults.currentText,
		closeText: $.fn.datebox.defaults.closeText,
		okText: $.fn.datebox.defaults.okText
	});
}
