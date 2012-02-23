
function getString(key){
	if(!store.get('lang')){
		store.set('lang','EN');
	}
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


