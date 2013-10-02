
function identifikace(){
	if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)){ //test for MSIE x.x;
	 var ieversion=new Number(RegExp.$1) // capture x.x portion and store as a number
	 if (ieversion<7)
	  alert("Váš internetový prohlížeč není podporován. Omlouváme se, ale hra se na mnoha místech zobrazí nekorektně.\n Přepněte se prosím do jakéhokoliv modernějšího prohlížeče.");

	}
}