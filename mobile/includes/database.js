
var store = new Store();
store.init();

function Store(){
	
	this.init = function(){
		if (!localStorage) {
			localStorage.setItem('username', null);
			localStorage.setItem('password', null);
			localStorage.setItem('kpis', null);
			localStorage.setItem('tasks', null);
			localStorage.setItem('deliveries', null);
			localStorage.setItem('overdue', null);
			localStorage.setItem('lastupdate', null);
			localStorage.setItem('lastlogin', null);
			localStorage.setItem('lang', 'EN');
		}
	}
	
	this.get = function(key){
		var value = localStorage.getItem(key);
	    return value && JSON.parse(value);
	}
	
	this.set = function(key,value){
		localStorage.setItem(key,JSON.stringify(value));
	}
	
	this.clear = function(){
		localStorage.clear();
	}
	
}