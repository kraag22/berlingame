

function clickLand( zem )
{
  //odznac aktivnu krajinu a pripadne oznac novu...
  	if (aktivni_zem != 0)
  	{
  	  	odznacZem ( aktivni_zem );
		skrySipky( aktivni_zem );
  	}
	if (aktivni_zem == zem)
	{
 			aktivni_zem = 0;
 	}
 	else{
 	  	oznacZem( zem );
		zobrazSipky( zem );
  		aktivni_zem = zem;
 	}
 	 	
 	//sipky ku krajinam, na ktore mozem utocit...
 	
	
	return false;
}
  
function loading ()
{
if (loading_current < loading_max){	
	loading_current += 0.5;
	document.getElementById("loading").innerHTML = "Loading " 
				+ Math.round(loading_current) + "%";
	}
}

function loading_set (current, max)
{
loading_current = current;
loading_max = max;
}

function oznacZem ( zem ) 
{
	prev_img = document.getElementById( "img_c" + zem ).src;
	document.getElementById( "img_c" + zem ).src = obrazky[ zem + "_over" ].src;
}

function odznacZem ( zem ) 
{
	document.getElementById( "img_c" + zem ).src = prev_img;
}

function skrySipky( zem )
{
	if(document.getElementById( "arr" + zem ))
	{
		document.getElementById( "arr" + zem ).style.display = "none";
	}
}

function zobrazSipky( zem )
{
	if(document.getElementById( "arr" + zem ))
	{
		document.getElementById( "arr" + zem ).style.display = "block";
	}
}

function mouseOverLand( zem, zobrazit)
  {

  //if (aktivni_zem != zem){
		zem = zem + "_over";
	  if ( zobrazit==0 ){
	  document.getElementById( "img_c" + zem ).src = obrazky["prazdno"].src;
	  }
	  else{
	  document.getElementById( "img_c" + zem ).src = obrazky[ zem ].src;
	  }
  //}
  }
  
function LoadImages( dir )
{
	obrazky["prazdno"] = new Image;
	obrazky["prazdno"].src = dir + "prazdno.png";
	obrazky["1"] = new Image;
	obrazky["1"].src = dir + "highlighted/1.png";
	obrazky["1_over"] = new Image;
	obrazky["1_over"].src = dir + "over/1.png";
	obrazky["2"] = new Image;
	obrazky["2"].src = dir + "highlighted/2.png";
	obrazky["2_over"] = new Image;
	obrazky["2_over"].src = dir + "over/2.png";
	obrazky["3"] = new Image;
	obrazky["3"].src = dir + "highlighted/3.png";
	obrazky["3_over"] = new Image;
	obrazky["3_over"].src = dir + "over/3.png";
	obrazky["4"] = new Image;
	obrazky["4"].src = dir + "highlighted/4.png";
	obrazky["4_over"] = new Image;
	obrazky["4_over"].src = dir + "over/4.png";
	obrazky["5"] = new Image;
	obrazky["5"].src = dir + "highlighted/5.png";
	obrazky["5_over"] = new Image;
	obrazky["5_over"].src = dir + "over/5.png";
	obrazky["6"] = new Image;
	obrazky["6"].src = dir + "highlighted/6.png";
	obrazky["6_over"] = new Image;
	obrazky["6_over"].src = dir + "over/6.png";
	obrazky["7"] = new Image;
	obrazky["7"].src = dir + "highlighted/7.png";
	obrazky["7_over"] = new Image;
	obrazky["7_over"].src = dir + "over/7.png";
	obrazky["8"] = new Image;
	obrazky["8"].src = dir + "highlighted/8.png";
	obrazky["8_over"] = new Image;
	obrazky["8_over"].src = dir + "over/8.png";
	obrazky["9"] = new Image;
	obrazky["9"].src = dir + "highlighted/9.png";
	obrazky["9_over"] = new Image;
	obrazky["9_over"].src = dir + "over/9.png";
	obrazky["10"] = new Image;
	obrazky["10"].src = dir + "highlighted/10.png";
	obrazky["10_over"] = new Image;
	obrazky["10_over"].src = dir + "over/10.png";

	obrazky["11"] = new Image;
	obrazky["11"].src = dir + "highlighted/11.png";
	obrazky["11_over"] = new Image;
	obrazky["11_over"].src = dir + "over/11.png";
	obrazky["12"] = new Image;
	obrazky["12"].src = dir + "highlighted/12.png";
	obrazky["12_over"] = new Image;
	obrazky["12_over"].src = dir + "over/12.png";
	obrazky["13"] = new Image;
	obrazky["13"].src = dir + "highlighted/13.png";
	obrazky["13_over"] = new Image;
	obrazky["13_over"].src = dir + "over/13.png";
	obrazky["14"] = new Image;
	obrazky["14"].src = dir + "highlighted/14.png";
	obrazky["14_over"] = new Image;
	obrazky["14_over"].src = dir + "over/14.png";
	
	obrazky["15"] = new Image;
	obrazky["15"].src = dir + "highlighted/15.png";
	obrazky["15_over"] = new Image;
	obrazky["15_over"].src = dir + "over/15.png";
	obrazky["16"] = new Image;
	obrazky["16"].src = dir + "highlighted/16.png";
	obrazky["16_over"] = new Image;
	obrazky["16_over"].src = dir + "over/16.png";
	obrazky["17"] = new Image;
	obrazky["17"].src = dir + "highlighted/17.png";
	obrazky["17_over"] = new Image;
	obrazky["17_over"].src = dir + "over/17.png";
	obrazky["18"] = new Image;
	obrazky["18"].src = dir + "highlighted/18.png";
	obrazky["18_over"] = new Image;
	obrazky["18_over"].src = dir + "over/18.png";
	obrazky["19"] = new Image;
	obrazky["19"].src = dir + "highlighted/19.png";
	obrazky["19_over"] = new Image;
	obrazky["19_over"].src = dir + "over/19.png";
	obrazky["20"] = new Image;
	obrazky["20"].src = dir + "highlighted/20.png";
	obrazky["20_over"] = new Image;
	obrazky["20_over"].src = dir + "over/20.png";
	
	obrazky["21"] = new Image;
	obrazky["21"].src = dir + "highlighted/21.png";
	obrazky["21_over"] = new Image;
	obrazky["21_over"].src = dir + "over/21.png";
	obrazky["22"] = new Image;
	obrazky["22"].src = dir + "highlighted/22.png";
	obrazky["22_over"] = new Image;
	obrazky["22_over"].src = dir + "over/22.png";
	obrazky["23"] = new Image;
	obrazky["23"].src = dir + "highlighted/23.png";
	obrazky["23_over"] = new Image;
	obrazky["23_over"].src = dir + "over/23.png";
	obrazky["24"] = new Image;
	obrazky["24"].src = dir + "highlighted/24.png";
	obrazky["24_over"] = new Image;
	obrazky["24_over"].src = dir + "over/24.png";
	obrazky["25"] = new Image;
	obrazky["25"].src = dir + "highlighted/25.png";
	obrazky["25_over"] = new Image;
	obrazky["25_over"].src = dir + "over/25.png";
	obrazky["26"] = new Image;
	obrazky["26"].src = dir + "highlighted/26.png";
	obrazky["26_over"] = new Image;
	obrazky["26_over"].src = dir + "over/26.png";
	obrazky["27"] = new Image;
	obrazky["27"].src = dir + "highlighted/27.png";
	obrazky["27_over"] = new Image;
	obrazky["27_over"].src = dir + "over/27.png";
	obrazky["28"] = new Image;
	obrazky["28"].src = dir + "highlighted/28.png";
	obrazky["28_over"] = new Image;
	obrazky["28_over"].src = dir + "over/28.png";
	obrazky["29"] = new Image;
	obrazky["29"].src = dir + "highlighted/29.png";
	obrazky["29_over"] = new Image;
	obrazky["29_over"].src = dir + "over/29.png";
	obrazky["30"] = new Image;
	obrazky["30"].src = dir + "highlighted/30.png";
	obrazky["30_over"] = new Image;
	obrazky["30_over"].src = dir + "over/30.png";

	obrazky["31"] = new Image;
	obrazky["31"].src = dir + "highlighted/31.png";
	obrazky["31_over"] = new Image;
	obrazky["31_over"].src = dir + "over/31.png";
	obrazky["32"] = new Image;
	obrazky["32"].src = dir + "highlighted/32.png";
	obrazky["32_over"] = new Image;
	obrazky["32_over"].src = dir + "over/32.png";
	obrazky["33"] = new Image;
	obrazky["33"].src = dir + "highlighted/33.png";
	obrazky["33_over"] = new Image;
	obrazky["33_over"].src = dir + "over/33.png";
	obrazky["34"] = new Image;
	obrazky["34"].src = dir + "highlighted/34.png";
	obrazky["34_over"] = new Image;
	obrazky["34_over"].src = dir + "over/34.png";
	obrazky["35"] = new Image;
	obrazky["35"].src = dir + "highlighted/35.png";
	obrazky["35_over"] = new Image;
	obrazky["35_over"].src = dir + "over/35.png";
	obrazky["36"] = new Image;
	obrazky["36"].src = dir + "highlighted/36.png";
	obrazky["36_over"] = new Image;
	obrazky["36_over"].src = dir + "over/36.png";
	obrazky["37"] = new Image;
	obrazky["37"].src = dir + "highlighted/37.png";
	obrazky["37_over"] = new Image;
	obrazky["37_over"].src = dir + "over/37.png";
	obrazky["38"] = new Image;
	obrazky["38"].src = dir + "highlighted/38.png";
	obrazky["38_over"] = new Image;
	obrazky["38_over"].src = dir + "over/38.png";
	obrazky["39"] = new Image;
	obrazky["39"].src = dir + "highlighted/39.png";
	obrazky["39_over"] = new Image;
	obrazky["39_over"].src = dir + "over/39.png";
	obrazky["40"] = new Image;
	obrazky["40"].src = dir + "highlighted/40.png";
	obrazky["40_over"] = new Image;
	obrazky["40_over"].src = dir + "over/40.png";
	
	obrazky["41"] = new Image;
	obrazky["41"].src = dir + "highlighted/41.png";
	obrazky["41_over"] = new Image;
	obrazky["41_over"].src = dir + "over/41.png";
	obrazky["42"] = new Image;
	obrazky["42"].src = dir + "highlighted/42.png";
	obrazky["42_over"] = new Image;
	obrazky["42_over"].src = dir + "over/42.png";
	obrazky["43"] = new Image;
	obrazky["43"].src = dir + "highlighted/43.png";
	obrazky["43_over"] = new Image;
	obrazky["43_over"].src = dir + "over/43.png";
	obrazky["44"] = new Image;
	obrazky["44"].src = dir + "highlighted/44.png";
	obrazky["44_over"] = new Image;
	obrazky["44_over"].src = dir + "over/44.png";
	obrazky["45"] = new Image;
	obrazky["45"].src = dir + "highlighted/45.png";
	obrazky["45_over"] = new Image;
	obrazky["45_over"].src = dir + "over/45.png";
	obrazky["46"] = new Image;
	obrazky["46"].src = dir + "highlighted/46.png";
	obrazky["46_over"] = new Image;
	obrazky["46_over"].src = dir + "over/46.png";
	obrazky["47"] = new Image;
	obrazky["47"].src = dir + "highlighted/47.png";
	obrazky["47_over"] = new Image;
	obrazky["47_over"].src = dir + "over/47.png";
	obrazky["48"] = new Image;
	obrazky["48"].src = dir + "highlighted/48.png";
	obrazky["48_over"] = new Image;
	obrazky["48_over"].src = dir + "over/48.png";
	obrazky["49"] = new Image;
	obrazky["49"].src = dir + "highlighted/49.png";
	obrazky["49_over"] = new Image;
	obrazky["49_over"].src = dir + "over/49.png";
	obrazky["50"] = new Image;
	obrazky["50"].src = dir + "highlighted/50.png";
	obrazky["50_over"] = new Image;
	obrazky["50_over"].src = dir + "over/50.png";

	obrazky["51"] = new Image;
	obrazky["51"].src = dir + "highlighted/51.png";
	obrazky["51_over"] = new Image;
	obrazky["51_over"].src = dir + "over/51.png";
	obrazky["52"] = new Image;
	obrazky["52"].src = dir + "highlighted/52.png";
	obrazky["52_over"] = new Image;
	obrazky["52_over"].src = dir + "over/52.png";
	obrazky["53"] = new Image;
	obrazky["53"].src = dir + "highlighted/53.png";
	obrazky["53_over"] = new Image;
	obrazky["53_over"].src = dir + "over/53.png";
	obrazky["54"] = new Image;
	obrazky["54"].src = dir + "highlighted/54.png";
	obrazky["54_over"] = new Image;
	obrazky["54_over"].src = dir + "over/54.png";
	obrazky["55"] = new Image;
	obrazky["55"].src = dir + "highlighted/55.png";
	obrazky["55_over"] = new Image;
	obrazky["55_over"].src = dir + "over/55.png";
	obrazky["56"] = new Image;
	obrazky["56"].src = dir + "highlighted/56.png";
	obrazky["56_over"] = new Image;
	obrazky["56_over"].src = dir + "over/56.png";
	obrazky["57"] = new Image;
	obrazky["57"].src = dir + "highlighted/57.png";
	obrazky["57_over"] = new Image;
	obrazky["57_over"].src = dir + "over/57.png";
	obrazky["58"] = new Image;
	obrazky["58"].src = dir + "highlighted/58.png";
	obrazky["58_over"] = new Image;
	obrazky["58_over"].src = dir + "over/58.png";
	obrazky["59"] = new Image;
	obrazky["59"].src = dir + "highlighted/59.png";
	obrazky["59_over"] = new Image;
	obrazky["59_over"].src = dir + "over/59.png";
	obrazky["60"] = new Image;
	obrazky["60"].src = dir + "highlighted/60.png";
	obrazky["60_over"] = new Image;
	obrazky["60_over"].src = dir + "over/60.png";
	
	obrazky["61"] = new Image;
	obrazky["61"].src = dir + "highlighted/61.png";
	obrazky["61_over"] = new Image;
	obrazky["61_over"].src = dir + "over/61.png";
	obrazky["62"] = new Image;
	obrazky["62"].src = dir + "highlighted/62.png";
	obrazky["62_over"] = new Image;
	obrazky["62_over"].src = dir + "over/62.png";
	obrazky["63"] = new Image;
	obrazky["63"].src = dir + "highlighted/63.png";
	obrazky["63_over"] = new Image;
	obrazky["63_over"].src = dir + "over/63.png";
	obrazky["64"] = new Image;
	obrazky["64"].src = dir + "highlighted/64.png";
	obrazky["64_over"] = new Image;
	obrazky["64_over"].src = dir + "over/64.png";
	obrazky["65"] = new Image;
	obrazky["65"].src = dir + "highlighted/65.png";
	obrazky["65_over"] = new Image;
	obrazky["65_over"].src = dir + "over/65.png";
	obrazky["66"] = new Image;
	obrazky["66"].src = dir + "highlighted/66.png";
	obrazky["66_over"] = new Image;
	obrazky["66_over"].src = dir + "over/66.png";
	obrazky["67"] = new Image;
	obrazky["67"].src = dir + "highlighted/67.png";
	obrazky["67_over"] = new Image;
	obrazky["67_over"].src = dir + "over/67.png";
	obrazky["68"] = new Image;
	obrazky["68"].src = dir + "highlighted/68.png";
	obrazky["68_over"] = new Image;
	obrazky["68_over"].src = dir + "over/68.png";
	obrazky["69"] = new Image;
	obrazky["69"].src = dir + "highlighted/69.png";
	obrazky["69_over"] = new Image;
	obrazky["69_over"].src = dir + "over/69.png";
	obrazky["70"] = new Image;
	obrazky["70"].src = dir + "highlighted/70.png";
	obrazky["70_over"] = new Image;
	obrazky["70_over"].src = dir + "over/70.png";

	obrazky["71"] = new Image;
	obrazky["71"].src = dir + "highlighted/71.png";
	obrazky["71_over"] = new Image;
	obrazky["71_over"].src = dir + "over/71.png";
	obrazky["72"] = new Image;
	obrazky["72"].src = dir + "highlighted/72.png";
	obrazky["72_over"] = new Image;
	obrazky["72_over"].src = dir + "over/72.png";
	obrazky["73"] = new Image;
	obrazky["73"].src = dir + "highlighted/73.png";
	obrazky["73_over"] = new Image;
	obrazky["73_over"].src = dir + "over/73.png";
	obrazky["74"] = new Image;
	obrazky["74"].src = dir + "highlighted/74.png";
	obrazky["74_over"] = new Image;
	obrazky["74_over"].src = dir + "over/74.png";
	obrazky["75"] = new Image;
	obrazky["75"].src = dir + "highlighted/75.png";
	obrazky["75_over"] = new Image;
	obrazky["75_over"].src = dir + "over/75.png";

	obrazky["76"] = new Image;
	obrazky["76"].src = dir + "highlighted/76.png";
	obrazky["76_over"] = new Image;
	obrazky["76_over"].src = dir + "over/76.png";
	obrazky["77"] = new Image;
	obrazky["77"].src = dir + "highlighted/77.png";
	obrazky["77_over"] = new Image;
	obrazky["77_over"].src = dir + "over/77.png";
	obrazky["78"] = new Image;
	obrazky["78"].src = dir + "highlighted/78.png";
	obrazky["78_over"] = new Image;
	obrazky["78_over"].src = dir + "over/78.png";
	obrazky["79"] = new Image;
	obrazky["79"].src = dir + "highlighted/79.png";
	obrazky["79_over"] = new Image;
	obrazky["79_over"].src = dir + "over/79.png";
	obrazky["80"] = new Image;
	obrazky["80"].src = dir + "highlighted/80.png";
	obrazky["80_over"] = new Image;
	obrazky["80_over"].src = dir + "over/80.png";

	obrazky["81"] = new Image;
	obrazky["81"].src = dir + "highlighted/81.png";
	obrazky["81_over"] = new Image;
	obrazky["81_over"].src = dir + "over/81.png";
	obrazky["82"] = new Image;
	obrazky["82"].src = dir + "highlighted/82.png";
	obrazky["82_over"] = new Image;
	obrazky["82_over"].src = dir + "over/82.png";
	obrazky["83"] = new Image;
	obrazky["83"].src = dir + "highlighted/83.png";
	obrazky["83_over"] = new Image;
	obrazky["83_over"].src = dir + "over/83.png";
	obrazky["84"] = new Image;
	obrazky["84"].src = dir + "highlighted/84.png";
	obrazky["84_over"] = new Image;
	obrazky["84_over"].src = dir + "over/84.png";
	obrazky["85"] = new Image;
	obrazky["85"].src = dir + "highlighted/85.png";
	obrazky["85_over"] = new Image;
	obrazky["85_over"].src = dir + "over/85.png";
	obrazky["86"] = new Image;
	obrazky["86"].src = dir + "highlighted/86.png";
	obrazky["86_over"] = new Image;
	obrazky["86_over"].src = dir + "over/86.png";
	obrazky["87"] = new Image;
	obrazky["87"].src = dir + "highlighted/87.png";
	obrazky["87_over"] = new Image;
	obrazky["87_over"].src = dir + "over/87.png";
	obrazky["88"] = new Image;
	obrazky["88"].src = dir + "highlighted/88.png";
	obrazky["88_over"] = new Image;
	obrazky["88_over"].src = dir + "over/88.png";
	obrazky["89"] = new Image;
	obrazky["89"].src = dir + "highlighted/89.png";
	obrazky["89_over"] = new Image;
	obrazky["89_over"].src = dir + "over/89.png";
	obrazky["90"] = new Image;
	obrazky["90"].src = dir + "highlighted/90.png";
	obrazky["90_over"] = new Image;
	obrazky["90_over"].src = dir + "over/90.png";
	
	obrazky["91"] = new Image;
	obrazky["91"].src = dir + "highlighted/91.png";
	obrazky["91_over"] = new Image;
	obrazky["91_over"].src = dir + "over/91.png";
	obrazky["92"] = new Image;
	obrazky["92"].src = dir + "highlighted/92.png";
	obrazky["92_over"] = new Image;
	obrazky["92_over"].src = dir + "over/92.png";
	obrazky["93"] = new Image;
	obrazky["93"].src = dir + "highlighted/93.png";
	obrazky["93_over"] = new Image;
	obrazky["93_over"].src = dir + "over/93.png";
	obrazky["94"] = new Image;
	obrazky["94"].src = dir + "highlighted/94.png";
	obrazky["94_over"] = new Image;
	obrazky["94_over"].src = dir + "over/94.png";
	obrazky["95"] = new Image;
	obrazky["95"].src = dir + "highlighted/95.png";
	obrazky["95_over"] = new Image;
	obrazky["95_over"].src = dir + "over/95.png";
	obrazky["96"] = new Image;
	obrazky["96"].src = dir + "highlighted/96.png";
	obrazky["96_over"] = new Image;
	obrazky["96_over"].src = dir + "over/96.png";
	obrazky["97"] = new Image;
	obrazky["97"].src = dir + "highlighted/97.png";
	obrazky["97_over"] = new Image;
	obrazky["97_over"].src = dir + "over/97.png";
	obrazky["98"] = new Image;
	obrazky["98"].src = dir + "highlighted/98.png";
	obrazky["98_over"] = new Image;
	obrazky["98_over"].src = dir + "over/98.png";
	obrazky["99"] = new Image;
	obrazky["99"].src = dir + "highlighted/99.png";
	obrazky["99_over"] = new Image;
	obrazky["99_over"].src = dir + "over/99.png";
	obrazky["100"] = new Image;
	obrazky["100"].src = dir + "highlighted/100.png";
	obrazky["100_over"] = new Image;
	obrazky["100_over"].src = dir + "over/100.png";
	
	obrazky["101"] = new Image;
	obrazky["101"].src = dir + "highlighted/101.png";
	obrazky["101_over"] = new Image;
	obrazky["101_over"].src = dir + "over/101.png";
	obrazky["102"] = new Image;
	obrazky["102"].src = dir + "highlighted/102.png";
	obrazky["102_over"] = new Image;
	obrazky["102_over"].src = dir + "over/102.png";
	obrazky["103"] = new Image;
	obrazky["103"].src = dir + "highlighted/103.png";
	obrazky["103_over"] = new Image;
	obrazky["103_over"].src = dir + "over/103.png";
	obrazky["104"] = new Image;
	obrazky["104"].src = dir + "highlighted/104.png";
	obrazky["104_over"] = new Image;
	obrazky["104_over"].src = dir + "over/104.png";
	obrazky["105"] = new Image;
	obrazky["105"].src = dir + "highlighted/105.png";
	obrazky["105_over"] = new Image;
	obrazky["105_over"].src = dir + "over/105.png";
	obrazky["106"] = new Image;
	obrazky["106"].src = dir + "highlighted/106.png";
	obrazky["106_over"] = new Image;
	obrazky["106_over"].src = dir + "over/106.png";
	obrazky["107"] = new Image;
	obrazky["107"].src = dir + "highlighted/107.png";
	obrazky["107_over"] = new Image;
	obrazky["107_over"].src = dir + "over/107.png";
	obrazky["108"] = new Image;
	obrazky["108"].src = dir + "highlighted/108.png";
	obrazky["108_over"] = new Image;
	obrazky["108_over"].src = dir + "over/108.png";
	obrazky["109"] = new Image;
	obrazky["109"].src = dir + "highlighted/109.png";
	obrazky["109_over"] = new Image;
	obrazky["109_over"].src = dir + "over/109.png";
	obrazky["110"] = new Image;
	obrazky["110"].src = dir + "highlighted/110.png";
	obrazky["110_over"] = new Image;
	obrazky["110_over"].src = dir + "over/110.png";
	
	obrazky["111"] = new Image;
	obrazky["111"].src = dir + "highlighted/111.png";
	obrazky["111_over"] = new Image;
	obrazky["111_over"].src = dir + "over/111.png";
	obrazky["112"] = new Image;
	obrazky["112"].src = dir + "highlighted/112.png";
	obrazky["112_over"] = new Image;
	obrazky["112_over"].src = dir + "over/112.png";
	obrazky["113"] = new Image;
	obrazky["113"].src = dir + "highlighted/113.png";
	obrazky["113_over"] = new Image;
	obrazky["113_over"].src = dir + "over/113.png";
	obrazky["114"] = new Image;
	obrazky["114"].src = dir + "highlighted/114.png";
	obrazky["114_over"] = new Image;
	obrazky["114_over"].src = dir + "over/114.png";
	
	obrazky["115"] = new Image;
	obrazky["115"].src = dir + "highlighted/115.png";
	obrazky["115_over"] = new Image;
	obrazky["115_over"].src = dir + "over/115.png";
	obrazky["116"] = new Image;
	obrazky["116"].src = dir + "highlighted/116.png";
	obrazky["116_over"] = new Image;
	obrazky["116_over"].src = dir + "over/116.png";
	obrazky["117"] = new Image;
	obrazky["117"].src = dir + "highlighted/117.png";
	obrazky["117_over"] = new Image;
	obrazky["117_over"].src = dir + "over/117.png";
	obrazky["118"] = new Image;
	obrazky["118"].src = dir + "highlighted/118.png";
	obrazky["118_over"] = new Image;
	obrazky["118_over"].src = dir + "over/118.png";
	obrazky["119"] = new Image;
	obrazky["119"].src = dir + "highlighted/119.png";
	obrazky["119_over"] = new Image;
	obrazky["119_over"].src = dir + "over/119.png";
	obrazky["120"] = new Image;
	obrazky["120"].src = dir + "highlighted/120.png";
	obrazky["120_over"] = new Image;
	obrazky["120_over"].src = dir + "over/120.png";
	
	obrazky["121"] = new Image;
	obrazky["121"].src = dir + "highlighted/121.png";
	obrazky["121_over"] = new Image;
	obrazky["121_over"].src = dir + "over/121.png";
	obrazky["122"] = new Image;
	obrazky["122"].src = dir + "highlighted/122.png";
	obrazky["122_over"] = new Image;
	obrazky["122_over"].src = dir + "over/122.png";
	obrazky["123"] = new Image;
	obrazky["123"].src = dir + "highlighted/123.png";
	obrazky["123_over"] = new Image;
	obrazky["123_over"].src = dir + "over/123.png";
	obrazky["124"] = new Image;
	obrazky["124"].src = dir + "highlighted/124.png";
	obrazky["124_over"] = new Image;
	obrazky["124_over"].src = dir + "over/124.png";
	obrazky["125"] = new Image;
	obrazky["125"].src = dir + "highlighted/125.png";
	obrazky["125_over"] = new Image;
	obrazky["125_over"].src = dir + "over/125.png";
	obrazky["126"] = new Image;
	obrazky["126"].src = dir + "highlighted/126.png";
	obrazky["126_over"] = new Image;
	obrazky["126_over"].src = dir + "over/126.png";
	obrazky["127"] = new Image;
	obrazky["127"].src = dir + "highlighted/127.png";
	obrazky["127_over"] = new Image;
	obrazky["127_over"].src = dir + "over/127.png";
	obrazky["128"] = new Image;
	obrazky["128"].src = dir + "highlighted/128.png";
	obrazky["128_over"] = new Image;
	obrazky["128_over"].src = dir + "over/128.png";
	obrazky["129"] = new Image;
	obrazky["129"].src = dir + "highlighted/129.png";
	obrazky["129_over"] = new Image;
	obrazky["129_over"].src = dir + "over/129.png";
	obrazky["130"] = new Image;
	obrazky["130"].src = dir + "highlighted/130.png";
	obrazky["130_over"] = new Image;
	obrazky["130_over"].src = dir + "over/130.png";
	
	obrazky["131"] = new Image;
	obrazky["131"].src = dir + "highlighted/131.png";
	obrazky["131_over"] = new Image;
	obrazky["131_over"].src = dir + "over/131.png";
	obrazky["132"] = new Image;
	obrazky["132"].src = dir + "highlighted/132.png";
	obrazky["132_over"] = new Image;
	obrazky["132_over"].src = dir + "over/132.png";
	obrazky["133"] = new Image;
	obrazky["133"].src = dir + "highlighted/133.png";
	obrazky["133_over"] = new Image;
	obrazky["133_over"].src = dir + "over/133.png";
	obrazky["134"] = new Image;
	obrazky["134"].src = dir + "highlighted/134.png";
	obrazky["134_over"] = new Image;
	obrazky["134_over"].src = dir + "over/134.png";
	obrazky["135"] = new Image;
	obrazky["135"].src = dir + "highlighted/135.png";
	obrazky["135_over"] = new Image;
	obrazky["135_over"].src = dir + "over/135.png";
	obrazky["136"] = new Image;
	obrazky["136"].src = dir + "highlighted/136.png";
	obrazky["136_over"] = new Image;
	obrazky["136_over"].src = dir + "over/136.png";
	obrazky["137"] = new Image;
	obrazky["137"].src = dir + "highlighted/137.png";
	obrazky["137_over"] = new Image;
	obrazky["137_over"].src = dir + "over/137.png";
	obrazky["138"] = new Image;
	obrazky["138"].src = dir + "highlighted/138.png";
	obrazky["138_over"] = new Image;
	obrazky["138_over"].src = dir + "over/138.png";
	obrazky["139"] = new Image;
	obrazky["139"].src = dir + "highlighted/139.png";
	obrazky["139_over"] = new Image;
	obrazky["139_over"].src = dir + "over/139.png";
	obrazky["140"] = new Image;
	obrazky["140"].src = dir + "highlighted/140.png";
	obrazky["140_over"] = new Image;
	obrazky["140_over"].src = dir + "over/140.png";
	
	obrazky["141"] = new Image;
	obrazky["141"].src = dir + "highlighted/141.png";
	obrazky["141_over"] = new Image;
	obrazky["141_over"].src = dir + "over/141.png";
	obrazky["142"] = new Image;
	obrazky["142"].src = dir + "highlighted/142.png";
	obrazky["142_over"] = new Image;
	obrazky["142_over"].src = dir + "over/142.png";
	obrazky["143"] = new Image;
	obrazky["143"].src = dir + "highlighted/143.png";
	obrazky["143_over"] = new Image;
	obrazky["143_over"].src = dir + "over/143.png";
	obrazky["144"] = new Image;
	obrazky["144"].src = dir + "highlighted/144.png";
	obrazky["144_over"] = new Image;
	obrazky["144_over"].src = dir + "over/144.png";
	obrazky["145"] = new Image;
	obrazky["145"].src = dir + "highlighted/145.png";
	obrazky["145_over"] = new Image;
	obrazky["145_over"].src = dir + "over/145.png";
	obrazky["146"] = new Image;
	obrazky["146"].src = dir + "highlighted/146.png";
	obrazky["146_over"] = new Image;
	obrazky["146_over"].src = dir + "over/146.png";
	obrazky["147"] = new Image;
	obrazky["147"].src = dir + "highlighted/147.png";
	obrazky["147_over"] = new Image;
	obrazky["147_over"].src = dir + "over/147.png";
	obrazky["148"] = new Image;
	obrazky["148"].src = dir + "highlighted/148.png";
	obrazky["148_over"] = new Image;
	obrazky["148_over"].src = dir + "over/148.png";
	obrazky["149"] = new Image;
	obrazky["149"].src = dir + "highlighted/149.png";
	obrazky["149_over"] = new Image;
	obrazky["149_over"].src = dir + "over/149.png";
	obrazky["150"] = new Image;
	obrazky["150"].src = dir + "highlighted/150.png";
	obrazky["150_over"] = new Image;
	obrazky["150_over"].src = dir + "over/150.png";
	
	loading_set(80,100);

	

}

