function kliknuti(obrazek , detail, ztraty)
	{
		document.getElementById( "obrazek" ).src = "./skins/default/hlaseni/" + obrazek;
		
		document.getElementById( "detail_hlaseni" ).innerHTML = detail;
		
		document.getElementById( "ztraty_hlaseni" ).innerHTML = ztraty;
		
	}