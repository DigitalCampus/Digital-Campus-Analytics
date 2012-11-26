
var lang = ['EN','TI'];

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
	$('[name^=ethio]').each(function(index){
		$(this).text(getString($(this).attr('name')));
	});
	$('[name^=healthpoint]').each(function(index){
		$(this).text(getString($(this).attr('name')));
	});
	$('[name^=risk]').each(function(index){
		$(this).text(getString($(this).attr('name')));
	});
	$('[name^=district]').each(function(index){
		$(this).text(getString($(this).attr('name')));
	});
	$('[name^=tabia]').each(function(index){
		$(this).text(getString($(this).attr('name')));
	});
}

function changeLang(){
	store.set('lang',$("#changelang option:selected").val());
	changeInterfaceLang();
}

function convertDate(date){
	var gc = $.calendars.instance();
	var d = gc.parseDate('yyyy-mm-dd*',date);
	var jd = d.toJD();
	var ec = $.calendars.instance('ethiopian').fromJD(jd); 
	// now form the date in the format (esp for switching between languages)
	var date = [];
	date['greg'] = d.formatDate('D d M Y');
	date['ethio'] = "<span name='ethio.day."+ec.dayOfWeek()+"'>" + getString('ethio.day.'+ec.dayOfWeek()) + "</span> " +
					ec.formatDate('d') +
					" <span name='ethio.month."+ec.monthOfYear()+"'>" + getString('ethio.month.'+ec.monthOfYear()) + "</span> " +
					ec.formatDate('Y');
	return date;
}
