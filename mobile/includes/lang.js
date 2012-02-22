
function getString(key){
	return lang[store.get('lang')][key];
	
}

function changeInterfaceLang(){
	// change main interface elements
	$('[name=lang]').each(function(index){
		$(this).text(getString($(this).attr('id')));
	});
	
	$('[name^=protocol]').each(function(index){
		$(this).text(getString($(this).attr('name')));
	});
}

function changeLang(){
	store.set('lang',$("#changelang option:selected").val());
	changeInterfaceLang();
}


var lang = ['EN','TI'];

lang['EN'] = [];
lang['EN']['mobile_app_title'] = "Health Analytics";

lang['EN']['menu_deliveries'] = "Deliveries";
lang['EN']['menu_kpi'] = "Statistics";
lang['EN']['menu_overdue'] = "Overdue";
lang['EN']['menu_tasks'] = "Tasks";

lang['EN']['protocol.ancfollow'] = "ANC Follow Up";
lang['EN']['protocol.anclabtest'] = "ANC Lab Test";
lang['EN']['protocol.delivery'] = "Delivery";
lang['EN']['protocol.pnc'] = "PNC";

lang['TI'] = [];
lang['TI']['mobile_app_title'] = "Health Analytics in TI";

lang['TI']['menu_deliveries'] = "Deliveries TI";
lang['TI']['menu_kpi'] = "Statistics TI";
lang['TI']['menu_overdue'] = "Overdue TI";
lang['TI']['menu_tasks'] = "Tasks TI";

lang['TI']['protocol.ancfollow'] = "ANC Follow Up TI";
lang['TI']['protocol.anclabtest'] = "ANC Lab Test TI";
lang['TI']['protocol.delivery'] = "Delivery TI";
lang['TI']['protocol.pnc'] = "PNC TI";