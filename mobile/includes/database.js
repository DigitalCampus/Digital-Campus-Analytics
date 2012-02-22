
var store = new Store();
store.init();


function Store(){
	
	this.init = function(){
		if (!localStorage) {
			localStorage.setItem('username', null);
			localStorage.setItem('password', null);
			localStorage.setItem('tasks', null);
			localStorage.setItem('deliveries', null);
			localStorage.setItem('overdue', null);
			localStorage.setItem('lastupdate', null);
		}
	}
	
	this.get = function(key){
		return localStorage.getItem(key);
	}
	
	this.set = function(key,value){
		localStorage.setItem(key,value);
	}
	
	this.clear = function(){
		localStorage.clear();
	}
	
}