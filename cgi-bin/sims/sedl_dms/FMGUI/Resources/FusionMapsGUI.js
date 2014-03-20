
if(typeof infosoftglobal=="undefined")var infosoftglobal=new Object();
if(typeof infosoftglobal.FusionMapsGUI=="undefined")infosoftglobal.FusionMapsGUI=new Object();
infosoftglobal.FusionMapsGUI=function(){
if(!document.getElementById){return;}
this.mapId="MainMap";
this.mapPath="../Maps/";
this.mapIndex=-1;
this.gForm=document['guiFORM'];
this.mapList=new Array();
this.entities=new Array();
this.markers=new Array();
this.markerPos=new Array();
this.chooseMode=false;
this.defineMapList();
}
infosoftglobal.FusionMapsGUI.prototype.getReferenceToMap=function(){
this.mapObj=infosoftglobal.FusionMapsUtil.getMapObject(this.mapId);
this.entities=this.mapObj.getEntityList();
}
infosoftglobal.FusionMapsGUI.prototype.defineMapList=function(){
this.mapList.push({isMap:false,title:"Please select a map below",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"------------------------------------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"World Map",swf:"FCMap_World.swf",width:750,height:400});
this.mapList.push({isMap:true,title:"World Map (8 Regions)",swf:"FCMap_World8.swf",width:570,height:290});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"--------- US & Counties ---------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"USA (Counties)",swf:"FCMap_USA.swf",width:750,height:460});
this.mapList.push({isMap:true,title:"Alabama",swf:"FCMap_Alabama.swf",width:575,height:875});
this.mapList.push({isMap:true,title:"Alaska",swf:"FCMap_Alaska.swf",width:685,height:555});
this.mapList.push({isMap:true,title:"Arizona",swf:"FCMap_Arizona.swf",width:600,height:690});
this.mapList.push({isMap:true,title:"Arkansas",swf:"FCMap_Arkansas.swf",width:720,height:650});
this.mapList.push({isMap:true,title:"California",swf:"FCMap_California.swf",width:620,height:705});
this.mapList.push({isMap:true,title:"Colorado",swf:"FCMap_Colorado.swf",width:810,height:670});
this.mapList.push({isMap:true,title:"Connecticut",swf:"FCMap_Connecticut.swf",width:760,height:570});
this.mapList.push({isMap:true,title:"Delaware",swf:"FCMap_Delaware.swf",width:375,height:850});
this.mapList.push({isMap:true,title:"Florida",swf:"FCMap_Florida.swf",width:650,height:600});
this.mapList.push({isMap:true,title:"Georgia",swf:"FCMap_Georgia.swf",width:610,height:700});
this.mapList.push({isMap:true,title:"Hawaii",swf:"FCMap_Hawaii.swf",width:770,height:490});
this.mapList.push({isMap:true,title:"Idaho",swf:"FCMap_Idaho.swf",width:575,height:900});
this.mapList.push({isMap:true,title:"Illinois",swf:"FCMap_Illinois.swf",width:550,height:910});
this.mapList.push({isMap:true,title:"Indiana",swf:"FCMap_Indiana.swf",width:580,height:890});
this.mapList.push({isMap:true,title:"Iowa",swf:"FCMap_Iowa.swf",width:760,height:500});
this.mapList.push({isMap:true,title:"Kansas",swf:"FCMap_Kansas.swf",width:760,height:400});
this.mapList.push({isMap:true,title:"Kentucky",swf:"FCMap_Kentucky.swf",width:910,height:430});
this.mapList.push({isMap:true,title:"Lousiana",swf:"FCMap_Louisiana.swf",width:710,height:610});
this.mapList.push({isMap:true,title:"Maine",swf:"FCMap_Maine.swf",width:600,height:870});
this.mapList.push({isMap:true,title:"Maryland",swf:"FCMap_Maryland.swf",width:750,height:400});
this.mapList.push({isMap:true,title:"Massachusetts",swf:"FCMap_Massachusetts.swf",width:770,height:490});
this.mapList.push({isMap:true,title:"Michigan",swf:"FCMap_Michigan.swf",width:600,height:700});
this.mapList.push({isMap:true,title:"Minnesota",swf:"FCMap_Minnesota.swf",width:620,height:670});
this.mapList.push({isMap:true,title:"Mississippi",swf:"FCMap_Mississippi.swf",width:570,height:900});
this.mapList.push({isMap:true,title:"Missouri",swf:"FCMap_Missouri.swf",width:680,height:600});
this.mapList.push({isMap:true,title:"Montana",swf:"FCMap_Montana.swf",width:775,height:450});
this.mapList.push({isMap:true,title:"Nebraska",swf:"FCMap_Nebraska.swf",width:775,height:375});
this.mapList.push({isMap:true,title:"Nevada",swf:"FCMap_Nevada.swf",width:630,height:880});
this.mapList.push({isMap:true,title:"New Hampshire",swf:"FCMap_NewHampshire.swf",width:300,height:550});
this.mapList.push({isMap:true,title:"New Jersey",swf:"FCMap_NewJersey.swf",width:485,height:890});
this.mapList.push({isMap:true,title:"New Mexico",swf:"FCMap_NewMexico.swf",width:600,height:690});
this.mapList.push({isMap:true,title:"New York",swf:"FCMap_NewYork.swf",width:775,height:600});
this.mapList.push({isMap:true,title:"North Carolina",swf:"FCMap_NorthCarolina.swf",width:900,height:400});
this.mapList.push({isMap:true,title:"North Dakota",swf:"FCMap_NorthDakota.swf",width:770,height:470});
this.mapList.push({isMap:true,title:"Ohio",swf:"FCMap_Ohio.swf",width:620,height:670});
this.mapList.push({isMap:true,title:"Oklahoma",swf:"FCMap_Oklahoma.swf",width:780,height:380});
this.mapList.push({isMap:true,title:"Oregon",swf:"FCMap_Oregon.swf",width:760,height:570});
this.mapList.push({isMap:true,title:"Pennsylvania",swf:"FCMap_Pennsylvania.swf",width:770,height:460});
this.mapList.push({isMap:true,title:"Rhode Island",swf:"FCMap_RhodeIsland.swf",width:600,height:900});
this.mapList.push({isMap:true,title:"South Carolina",swf:"FCMap_SouthCarolina.swf",width:760,height:550});
this.mapList.push({isMap:true,title:"South Dakota",swf:"FCMap_SouthDakota.swf",width:770,height:500});
this.mapList.push({isMap:true,title:"Tennessee",swf:"FCMap_Tennessee.swf",width:770,height:210});
this.mapList.push({isMap:true,title:"Texas",swf:"FCMap_Texas.swf",width:980,height:920});
this.mapList.push({isMap:true,title:"Utah",swf:"FCMap_Utah.swf",width:620,height:780});
this.mapList.push({isMap:true,title:"Vermont",swf:"FCMap_Vermont.swf",width:570,height:900});
this.mapList.push({isMap:true,title:"Virginia",swf:"FCMap_Virginia.swf",width:890,height:490});
this.mapList.push({isMap:true,title:"Washington",swf:"FCMap_Washington.swf",width:780,height:520});
this.mapList.push({isMap:true,title:"West Virginia",swf:"FCMap_WestVirginia.swf",width:710,height:610});
this.mapList.push({isMap:true,title:"Wisconsin",swf:"FCMap_Wisconsin.swf",width:650,height:690});
this.mapList.push({isMap:true,title:"Wyoming",swf:"FCMap_Wyoming.swf",width:710,height:610});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"----------- US Regions ----------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"USA (All Regions)",swf:"FCMap_USARegion.swf",width:710,height:510});
this.mapList.push({isMap:true,title:"USA Central Region",swf:"FCMap_USACentralRegion.swf",width:250,height:350});
this.mapList.push({isMap:true,title:"USA South East Region",swf:"FCMap_USASouthEastRegion.swf",width:460,height:360});
this.mapList.push({isMap:true,title:"USA South West Region",swf:"FCMap_USASouthWestRegion.swf",width:270,height:230});
this.mapList.push({isMap:true,title:"USA North East Region",swf:"FCMap_USANorthEastRegion.swf",width:440,height:400});
this.mapList.push({isMap:true,title:"USA North West Region",swf:"FCMap_USANorthWestRegion.swf",width:360,height:320});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"--------- North America ---------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"North America",swf:"FCMap_NorthAmerica.swf",width:675,height:675});
this.mapList.push({isMap:true,title:"North America (W/o Central)",swf:"FCMap_NorthAmerica_WOCentral.swf",width:660,height:660});
this.mapList.push({isMap:true,title:"Antigua",swf:"FCMap_Antigua.swf",width:510,height:380});
this.mapList.push({isMap:true,title:"Bahamas",swf:"FCMap_Bahamas.swf",width:530,height:570});
this.mapList.push({isMap:true,title:"Barbados",swf:"FCMap_Barbados.swf",width:210,height:270});
this.mapList.push({isMap:true,title:"British Columbia",swf:"FCMap_BritishColumbia.swf",width:570,height:530});
this.mapList.push({isMap:true,title:"Canada",swf:"FCMap_Canada.swf",width:660,height:560});
this.mapList.push({isMap:true,title:"Cuba",swf:"FCMap_Cuba.swf",width:600,height:240});
this.mapList.push({isMap:true,title:"Dominican Republic",swf:"FCMap_DominicanRepublic.swf",width:410,height:300});
this.mapList.push({isMap:true,title:"Dominica",swf:"FCMap_Dominica.swf",width:160,height:280});
this.mapList.push({isMap:true,title:"Greenland",swf:"FCMap_Greenland.swf",width:320,height:550});
this.mapList.push({isMap:true,title:"Grenada",swf:"FCMap_Grenada.swf",width:260,height:300});
this.mapList.push({isMap:true,title:"Haiti",swf:"FCMap_Haiti.swf",width:360,height:290});
this.mapList.push({isMap:true,title:"Jamaica",swf:"FCMap_Jamaica.swf",width:420,height:180});
this.mapList.push({isMap:true,title:"Mexico",swf:"FCMap_Mexico.swf",width:770,height:450});
this.mapList.push({isMap:true,title:"Ontario",swf:"FCMap_Ontario.swf",width:615,height:600});
this.mapList.push({isMap:true,title:"Puerto Rico",swf:"FCMap_PuertoRico.swf",width:670,height:350});
this.mapList.push({isMap:true,title:"Quebec",swf:"FCMap_Quebec.swf",width:360,height:420});
this.mapList.push({isMap:true,title:"Saint Kitts & Nevis",swf:"FCMap_SaintKittsandNevis.swf",width:360,height:340});
this.mapList.push({isMap:true,title:"Saint Lucia",swf:"FCMap_SaintLucia.swf",width:280,height:530});
this.mapList.push({isMap:true,title:"Saint Vincent & The Grenadines",swf:"FCMap_SaintVincentandtheGrenadines.swf",width:240,height:530});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"--------- South America ---------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"South America",swf:"FCMap_SouthAmerica.swf",width:600,height:700});
this.mapList.push({isMap:true,title:"Argentina",swf:"FCMap_Argentina.swf",width:350,height:700});
this.mapList.push({isMap:true,title:"Bolivia",swf:"FCMap_Bolivia.swf",width:310,height:350});
this.mapList.push({isMap:true,title:"Brazil",swf:"FCMap_Brazil.swf",width:650,height:600});
this.mapList.push({isMap:true,title:"Chile",swf:"FCMap_Chile.swf",width:210,height:580});
this.mapList.push({isMap:true,title:"Colombia",swf:"FCMap_Colombia.swf",width:430,height:580});
this.mapList.push({isMap:true,title:"Ecuador",swf:"FCMap_Ecuador.swf",width:460,height:410});
this.mapList.push({isMap:true,title:"Paraguay",swf:"FCMap_Paraguay.swf",width:310,height:330});
this.mapList.push({isMap:true,title:"Peru",swf:"FCMap_Peru.swf",width:370,height:520});
this.mapList.push({isMap:true,title:"Suriname",swf:"FCMap_Suriname.swf",width:410,height:430});
this.mapList.push({isMap:true,title:"Uruguay",swf:"FCMap_Uruguay.swf",width:310,height:350});
this.mapList.push({isMap:true,title:"Venezuela",swf:"FCMap_Venezuela.swf",width:560,height:490});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"-------- Central America --------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"Central America",swf:"FCMap_CentralAmerica.swf",width:610,height:470});
this.mapList.push({isMap:true,title:"Central America (w/ Caribbean)",swf:"FCMap_CentralAmericawithCaribbean.swf",width:660,height:560});
this.mapList.push({isMap:true,title:"Belize",swf:"FCMap_Belize.swf",width:160,height:260});
this.mapList.push({isMap:true,title:"Costa Rica",swf:"FCMap_CostaRica.swf",width:320,height:310});
this.mapList.push({isMap:true,title:"El Salvador",swf:"FCMap_ElSalvador.swf",width:330,height:225});
this.mapList.push({isMap:true,title:"Guatemala",swf:"FCMap_Guatemala.swf",width:450,height:450});
this.mapList.push({isMap:true,title:"Honduras",swf:"FCMap_Honduras.swf",width:420,height:260});
this.mapList.push({isMap:true,title:"Nicaragua",swf:"FCMap_Nicaragua.swf",width:410,height:380});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"------- Europe & Countries ------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"Europe",swf:"FCMap_Europe.swf",width:620,height:600});
this.mapList.push({isMap:true,title:"Europe (All Countries)",swf:"FCMap_EuropewithCountries.swf",width:620,height:600});
this.mapList.push({isMap:true,title:"Austria",swf:"FCMap_Austria.swf",width:580,height:320});
this.mapList.push({isMap:true,title:"Albania",swf:"FCMap_Albania.swf",width:190,height:410});
this.mapList.push({isMap:true,title:"Andorra",swf:"FCMap_Andorra.swf",width:490,height:400});
this.mapList.push({isMap:true,title:"Bosnia-Herzegovina",swf:"FCMap_BosniaHerzegovina.swf",width:490,height:470});
this.mapList.push({isMap:true,title:"Belarus",swf:"FCMap_Belarus.swf",width:320,height:260});
this.mapList.push({isMap:true,title:"Belgium",swf:"FCMap_Belgium.swf",width:400,height:330});
this.mapList.push({isMap:true,title:"Bulgaria",swf:"FCMap_Bulgaria.swf",width:580,height:400});
this.mapList.push({isMap:true,title:"Croatia",swf:"FCMap_Croatia.swf",width:520,height:520});
this.mapList.push({isMap:true,title:"Cyprus",swf:"FCMap_Cyprus.swf",width:630,height:390});
this.mapList.push({isMap:true,title:"Czech Republic",swf:"FCMap_CzechRepublic.swf",width:810,height:470});
this.mapList.push({isMap:true,title:"Denmark",swf:"FCMap_Denmark.swf",width:700,height:550});
this.mapList.push({isMap:true,title:"England",swf:"FCMap_England.swf",width:500,height:600});
this.mapList.push({isMap:true,title:"Estonia",swf:"FCMap_Estonia.swf",width:600,height:400});
this.mapList.push({isMap:true,title:"Finland",swf:"FCMap_Finland.swf",width:280,height:470});
this.mapList.push({isMap:true,title:"France",swf:"FCMap_France.swf",width:650,height:575});
this.mapList.push({isMap:true,title:"Germany",swf:"FCMap_Germany.swf",width:475,height:600});
this.mapList.push({isMap:true,title:"Greece",swf:"FCMap_Greece.swf",width:420,height:430});
this.mapList.push({isMap:true,title:"Hungary",swf:"FCMap_Hungary.swf",width:520,height:340});
this.mapList.push({isMap:true,title:"Ireland",swf:"FCMap_Ireland.swf",width:425,height:550});
this.mapList.push({isMap:true,title:"Italy",swf:"FCMap_Italy.swf",width:500,height:575});
this.mapList.push({isMap:true,title:"Iceland",swf:"FCMap_Iceland.swf",width:510,height:360});
this.mapList.push({isMap:true,title:"Latvia",swf:"FCMap_Latvia.swf",width:530,height:320});
this.mapList.push({isMap:true,title:"Liechtenstein",swf:"FCMap_Liechtenstein.swf",width:320,height:610});
this.mapList.push({isMap:true,title:"Lithuania",swf:"FCMap_Lithuania.swf",width:800,height:620});
this.mapList.push({isMap:true,title:"Luxembourg",swf:"FCMap_Luxembourg.swf",width:560,height:780});
this.mapList.push({isMap:true,title:"Malta",swf:"FCMap_Malta.swf",width:360,height:310});
this.mapList.push({isMap:true,title:"Moldova",swf:"FCMap_Moldova.swf",width:350,height:440});
this.mapList.push({isMap:true,title:"Montenegro",swf:"FCMap_Montenegro.swf",width:520,height:590});
this.mapList.push({isMap:true,title:"Netherland",swf:"FCMap_Netherland.swf",width:500,height:575});
this.mapList.push({isMap:true,title:"Norway",swf:"FCMap_Norway.swf",width:530,height:570});
this.mapList.push({isMap:true,title:"Norway (Regions)",swf:"FCMap_NorwayRegion.swf",width:600,height:550});
this.mapList.push({isMap:true,title:"Poland",swf:"FCMap_Poland.swf",width:420,height:400});
this.mapList.push({isMap:true,title:"Portugal",swf:"FCMap_Portugal.swf",width:260,height:520});
this.mapList.push({isMap:true,title:"Romania",swf:"FCMap_Romania.swf",width:480,height:350});
this.mapList.push({isMap:true,title:"San Marino",swf:"FCMap_SanMarino.swf",width:330,height:400});
this.mapList.push({isMap:true,title:"Scotland",swf:"FCMap_Scotland.swf",width:575,height:575});
this.mapList.push({isMap:true,title:"Slovakia",swf:"FCMap_Slovakia.swf",width:620,height:320});
this.mapList.push({isMap:true,title:"Slovenia",swf:"FCMap_Slovenia.swf",width:570,height:390});
this.mapList.push({isMap:true,title:"Spain",swf:"FCMap_Spain.swf",width:560,height:400});
this.mapList.push({isMap:true,title:"Spain (Provinces)",swf:"FCMap_SpainProvinces.swf",width:820,height:580});
this.mapList.push({isMap:true,title:"Sweden",swf:"FCMap_Sweden.swf",width:300,height:600});
this.mapList.push({isMap:true,title:"Switzerland",swf:"FCMap_Switzerland.swf",width:520,height:350});
this.mapList.push({isMap:true,title:"Turkey",swf:"FCMap_Turkey.swf",width:720,height:330});
this.mapList.push({isMap:true,title:"UK",swf:"FCMap_UK.swf",width:270,height:430});
this.mapList.push({isMap:true,title:"Ukraine",swf:"FCMap_Ukraine.swf",width:560,height:390});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"------- Europe By Regions ------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"Europe (All Regions)",swf:"FCMap_EuropeRegion.swf",width:440,height:410});
this.mapList.push({isMap:true,title:"North European Region",swf:"FCMap_NorthEuropeanRegion.swf",width:280,height:260});
this.mapList.push({isMap:true,title:"South European Region",swf:"FCMap_SouthEuropeanRegion.swf",width:600,height:225});
this.mapList.push({isMap:true,title:"East European Region",swf:"FCMap_EastEuropeanRegion.swf",width:220,height:320});
this.mapList.push({isMap:true,title:"West European Region",swf:"FCMap_WestEuropeanRegion.swf",width:280,height:320});
this.mapList.push({isMap:true,title:"Central European Region",swf:"FCMap_CentralEuropeanRegion.swf",width:300,height:260});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"---------------- UK ----------------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"UK",swf:"FCMap_UK.swf",width:270,height:430});
this.mapList.push({isMap:true,title:"England (Region)",swf:"FCMap_EnglandRegion.swf",width:410,height:470});
this.mapList.push({isMap:true,title:"North Ireland",swf:"FCMap_NorthIreland.swf",width:420,height:350});
this.mapList.push({isMap:true,title:"Scotland (Region)",swf:"FCMap_ScotlandRegion.swf",width:400,height:470});
this.mapList.push({isMap:true,title:"Wales",swf:"FCMap_Wales.swf",width:340,height:410});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"--------------- Asia ---------------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"Asia",swf:"FCMap_Asia.swf",width:650,height:650});
this.mapList.push({isMap:true,title:"Asia (No Mid-east)",swf:"FCMap_Asia3.swf",width:650,height:650});
this.mapList.push({isMap:true,title:"Armenia",swf:"FCMap_Armenia.swf",width:500,height:510});
this.mapList.push({isMap:true,title:"Azerbaijan",swf:"FCMap_Azerbaijan.swf",width:600,height:480});
this.mapList.push({isMap:true,title:"Bangladesh",swf:"FCMap_Bangladesh.swf",width:460,height:620});
this.mapList.push({isMap:true,title:"China",swf:"FCMap_China.swf",width:500,height:420});
this.mapList.push({isMap:true,title:"Georgia",swf:"FCMap_AsiaGeorgia.swf",width:580,height:300});
this.mapList.push({isMap:true,title:"India",swf:"FCMap_India.swf",width:625,height:650});
this.mapList.push({isMap:true,title:"Indonesia",swf:"FCMap_Indonesia.swf",width:800,height:300});
this.mapList.push({isMap:true,title:"Japan",swf:"FCMap_Japan.swf",width:575,height:625});
this.mapList.push({isMap:true,title:"Malaysia",swf:"FCMap_Malaysia.swf",width:600,height:275});
this.mapList.push({isMap:true,title:"North Korea",swf:"FCMap_NorthKorea.swf",width:530,height:560});
this.mapList.push({isMap:true,title:"Russia",swf:"FCMap_Russia.swf",width:675,height:450});
this.mapList.push({isMap:true,title:"South Korea",swf:"FCMap_SouthKorea.swf",width:550,height:570});
this.mapList.push({isMap:true,title:"Taiwan",swf:"FCMap_Taiwan.swf",width:450,height:560});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"----------- Middle East -----------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"Middle East",swf:"FCMap_MiddleEast.swf",width:620,height:480});
this.mapList.push({isMap:true,title:"Afghanistan",swf:"FCMap_Afghanistan.swf",width:620,height:480});
this.mapList.push({isMap:true,title:"Bahrain",swf:"FCMap_Bahrain.swf",width:160,height:230});
this.mapList.push({isMap:true,title:"Israel",swf:"FCMap_Israel.swf",width:200,height:500});
this.mapList.push({isMap:true,title:"Iraq",swf:"FCMap_Iraq.swf",width:320,height:330});
this.mapList.push({isMap:true,title:"United Arab Emirates",swf:"FCMap_UAE.swf",width:580,height:480});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"------------- Oceania -------------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"Oceania",swf:"FCMap_Oceania.swf",width:675,height:525});
this.mapList.push({isMap:true,title:"Australia",swf:"FCMap_Australia.swf",width:275,height:215});
this.mapList.push({isMap:true,title:"New Zealand",swf:"FCMap_NewZealand.swf",width:410,height:600});
this.mapList.push({isMap:false,title:"  ",swf:"",width:0,height:0});
this.mapList.push({isMap:false,title:"-------- Africa & Countries -------",swf:"",width:0,height:0});
this.mapList.push({isMap:true,title:"Africa",swf:"FCMap_Africa.swf",width:670,height:620});
this.mapList.push({isMap:true,title:"Egypt",swf:"FCMap_Egypt.swf",width:410,height:420});
this.mapList.push({isMap:true,title:"Kenya",swf:"FCMap_Kenya.swf",width:300,height:340});
this.mapList.push({isMap:true,title:"South Africa",swf:"FCMap_SouthAfrica.swf",width:460,height:500});
this.mapList.push({isMap:true,title:"Mozambique",swf:"FCMap_Mozambique.swf",width:310,height:510});
}
infosoftglobal.FusionMapsGUI.prototype.loadMap=function(index){
if(this.mapList[index].isMap){
this.mapIndex=index;
var map=new FusionMaps(this.mapPath+this.mapList[index].swf,this.mapId,this.mapList[index].width,this.mapList[index].height,"0","1");
map.setDataXML("<map animation='0' showBevel='0' showShadow='0' fillColor='F1f1f1' borderColor='000000'/>");
map.render("mapdiv");
var dv=document.getElementById("mapNameDiv");
dv.innerHTML="<span class='text'>Map of "+this.mapList[index].title+"</span>";
}
}
infosoftglobal.FusionMapsGUI.prototype.previewMap=function(){
var strXML=this.getFullXMLData();
if((markerWinOpened==true||this.markers.length>0)&&navigator.plugins&&navigator.mimeTypes&&navigator.mimeTypes.length){
isReload=true;
markerWinOpened=false;
var map=new FusionMaps(this.mapPath+this.mapList[this.mapIndex].swf,this.mapId,this.mapList[this.mapIndex].width,this.mapList[this.mapIndex].height,"0","1");
map.setDataXML(strXML);
map.render("mapdiv");
}else{
this.mapObj.setDataXML(strXML);
}
}
infosoftglobal.FusionMapsGUI.prototype.updateMapfromXML=function(){
if(markerWinOpened==true&&navigator.plugins&&navigator.mimeTypes&&navigator.mimeTypes.length){
isReload=true;
var map=new FusionMaps(this.mapPath+this.mapList[this.mapIndex].swf,this.mapId,this.mapList[this.mapIndex].width,this.mapList[this.mapIndex].height,"0","1");
alert(this.getValue("xmlDataFull"));
map.setDataXML(this.getValue("xmlDataFull"));
map.render("mapdiv");
}else{
alert(this.getValue("xmlDataFull"));
this.mapObj.setDataXML(this.getValue("xmlDataFull"),false);
}
}
infosoftglobal.FusionMapsGUI.prototype.enableChooseMode=function(){
if(typeof this.mapObj.enableChooseMode=="function"){
this.mapObj.enableChooseMode();
this.chooseMode=true;
}
}
infosoftglobal.FusionMapsGUI.prototype.disableChooseMode=function(){
if(this.chooseMode==true){
this.mapObj.disableChooseMode();
this.chooseMode=false;
}
}
infosoftglobal.FusionMapsGUI.prototype.renderMapSelectionBox=function(divRef){
var dv=document.getElementById(divRef);
var selectHTML="<select name='mapSelector' class='select' onChange=\"javascript:changeMap(document['guiFORM'].mapSelector.value);\">";
var i;
for(i=0;i<this.mapList.length;i++){
selectHTML=selectHTML+"<option value='"+String(i)+"'>"+this.mapList[i].title;
}
selectHTML=selectHTML+"</select>";
dv.innerHTML=selectHTML;
}
infosoftglobal.FusionMapsGUI.prototype.isMapIndex=function(index){
return this.mapList[index].isMap;
}
infosoftglobal.FusionMapsGUI.prototype.createTabs=function(){
var dv=document.getElementById("tabDiv");
dv.style.display="inline";
this.updateEntityForm();
}
infosoftglobal.FusionMapsGUI.prototype.clearForms=function(){
document.getElementById('maintab').tabber.tabShow(0);
var dv=document.getElementById("entityFormDiv");
dv.innerHTML="<span class='text'><B>Please wait while the new map is loading.</B></span>";
while(getTableLen("tblMarker")>0){
deleteLastRow("tblMarker");
}
this.gForm.xmlDataFull.value="";
this.gForm.xmlEntityTemplate.value="";
this.gForm.xmlMarkerFull.value="";
this.gForm.xmlMarkerDef.value="";
var dv=document.getElementById("tabDiv");
dv.style.display="none";
}
infosoftglobal.FusionMapsGUI.prototype.updateEntityForm=function(){
var fHTML="<table width='95%' align='center' cellpadding='2' cellspacing='2' style='border:1px #CCCCCC solid;'>";
var fHTML=fHTML+"<tr bgColor='#E0E0E0'><td width='25%' class='header' valign='top'>Entity Name</td><td width='8%' class='header' valign='top'>Id</td><td width='12%' class='header' valign='top' align='center'>Value</td><td width='16%' class='header' valign='top' align='center'>Display Value</td><td width='12%' class='header' valign='top'>&nbsp;Tool-tip</td><td width='15%' class='header' valign='top'>&nbsp;Link</td><td width='15%' class='header' valign='top' align='center'>Color</td></tr>";
for(i=1;i<this.entities.length;i++){
if(i%2==1){
fHTML=fHTML+"<tr bgColor='#F5F5F5'>";
}else{
fHTML=fHTML+"<tr>";
}
fHTML=fHTML+"<td width='25%' class='text' valign='middle'>"+String(this.entities[i].lName)+"</td>";
fHTML=fHTML+"<td width='8%' class='text' valign='middle'>"+String(this.entities[i].id)+"</td>";
fHTML=fHTML+"<td width='12%' valign='middle' align='center'><input type='text' class='textbox' size='6' name='eValue"+String(i)+"' onBlur='if(this.value!=\"\" && isNaN(this.value)) {alert(\"You cannot enter a non-numeric value for an entity\"); this.focus();}'/> </td>";
fHTML=fHTML+"<td width='16%' valign='middle' align='center'><input type='text' class='textbox' size='12' name='eDisplayValue"+String(i)+"' /> </td>";
fHTML=fHTML+"<td width='12%' valign='middle' align='center'><input type='text' class='textbox' size='10' name='eToolText"+String(i)+"' /> </td>";
fHTML=fHTML+"<td width='14%' valign='middle' align='center'><input type='text' class='textbox' size='12' name='eLink"+String(i)+"' /> </td>";
fHTML=fHTML+"<td width='15%' align='center' valign='middle'><input type='text' class='textbox' size='6' name='eColor"+String(i)+"' />&nbsp;<input type='button' value='...' style='width:20;' class='select' onClick=\"javascript:openColorPicker(document['guiFORM'].eColor"+String(i)+");\"></td>";
fHTML=fHTML+"</tr>";
}
fHTML=fHTML+"</table><BR>";
var dv=document.getElementById("entityFormDiv");
dv.innerHTML=fHTML;
}
infosoftglobal.FusionMapsGUI.prototype.renderXMLCode=function(){
this.gForm.xmlDataFull.value=this.getFullFMQS();
this.gForm.xmlEntityTemplate.value=this.getEntityFMQSTemplate();
if(this.markers.length>0){
var strXML="";
strXML+=this.getMarkerDefFMQS();
strXML+=this.getMarkerAppFMQS();
this.gForm.xmlMarkerFull.value=strXML;
var strXML="";
strXML+=this.getMarkerDefFMQS();
this.gForm.xmlMarkerDef.value=strXML;
}else{
this.gForm.xmlMarkerFull.value="";
this.gForm.xmlMarkerDef.value="";
}
}
infosoftglobal.FusionMapsGUI.prototype.addMarker=function(mX,mY,mId,mLabel,mLabelPos,mShow){
mId=this.encodeStr(mId);
mLabel=this.encodeStr(mLabel);
this.markers.push(mId);
this.markerPos.push({x:mX,y:mY});
var markerRow=appendRowAtEnd("tblMarker");
var idCell=markerRow.insertCell(0);
idCell.width="10%";
idCell.valign="top";
idCell.bgColor="#f5f5f5";
idCell.innerHTML="<span class='text'>"+mId+"</span>";
var labelCell=markerRow.insertCell(1);
labelCell.width="30%";
labelCell.valign="top";
labelCell.innerHTML="<input type='text' class='textbox' name='mLabel_"+mId+"' value='"+mLabel+"' size='25'>";
var labelPosCell=markerRow.insertCell(2);
labelPosCell.width="15%";
labelPosCell.valign="top";
labelPosCell.align="center";
labelPosCell.innerHTML="<select name='mLabelPos_"+mId+"' class='select'><option value='top' "+this.isSelected("top",mLabelPos)+">Top<option value='bottom'"+this.isSelected("bottom",mLabelPos)+">Bottom<option value='center'"+this.isSelected("center",mLabelPos)+">Center<option value='left'"+this.isSelected("left",mLabelPos)+">Left<option value='right'"+this.isSelected("right",mLabelPos)+">Right</select>";
var showCell=markerRow.insertCell(3);
showCell.width="15%";
showCell.valign="top";
showCell.align="center";
showCell.innerHTML="<input type='checkbox' name='mShow_"+mId+"' "+((mShow)?"checked":"")+">";
var shapeCell=markerRow.insertCell(4);
shapeCell.width="15%";
shapeCell.valign="top";
shapeCell.align="center";
shapeCell.innerHTML="<select name='mShape_"+mId+"' class='select'><option value='circle'>Circle<option value='arc'>Arc<option value='triangle'>Triangle<option value='diamond'>Diamond</select>";
var deleteCell=markerRow.insertCell(5);
deleteCell.width="10%";
deleteCell.valign="top";
deleteCell.align="center";
deleteCell.innerHTML="<input type='button' class='select' value='X' name='mDelete_"+mId+"' onClick='javaScript:deleteMarker(\""+mId+"\");'>";
}
infosoftglobal.FusionMapsGUI.prototype.deleteMarker=function(markerId){
var index=this.getMarkerIndexFromId(markerId);
this.markers.splice(index,1);
this.markerPos.splice(index,1);
deleteTableRow("tblMarker",index+1);
}
infosoftglobal.FusionMapsGUI.prototype.getMarkers=function(){
return this.markers;
}
infosoftglobal.FusionMapsGUI.prototype.getMarkerIndexFromId=function(mId){
var index=-1;
for(i=0;i<this.markers.length;i++){
if(this.markers[i]==mId){
index=i;
break;
}
}
return index;
}
infosoftglobal.FusionMapsGUI.prototype.getFullXMLData=function(){
var strXML="<map "+this.getMapElementAtts()+" >\n";
strXML=strXML+this.getDataAsXML();
if(this.markers.length>0){
strXML=strXML+"\t<markers>\n";
strXML=strXML+"\t\t<definition>\n"+this.getMarkerDefXML(true)+"\t\t</definition>\n";
strXML=strXML+"\t\t<application>\n"+this.getMarkerAppXML(true)+"\t\t</application>\n";
strXML=strXML+"\t</markers>\n";
}
strXML=strXML+"</map>";
return strXML;
}
infosoftglobal.FusionMapsGUI.prototype.getMapElementAtts=function(){
var atts="";
atts=atts+this.buildAttString("animation","mAnimation",false,"1");
atts=atts+this.buildAttString("showShadow","mShowShadow",false,"1");
atts=atts+this.buildAttString("showBevel","mShowBevel",false,"1");
atts=atts+this.buildAttString("showLegend","mShowLegend",false,"1");
atts=atts+this.buildAttString("showLabels","mShowLabels",false,"1");
atts=atts+this.buildAttString("showMarkerLabels","mShowMarkerLabels",false,"2");
atts=atts+this.buildAttString("useSNameInToolTip","mUseSNameInToolTip",false,"0");
atts=atts+this.buildAttString("includeNameInLabels","mIncludeNameInLabels",false,"1");
atts=atts+this.buildAttString("includeValueInLabels","mIncludeValueInLabels",false,"0");
atts=atts+this.buildAttString("fillColor","mFillColor",true,"");
atts=atts+this.buildAttString("borderColor","mBorderColor",true,"");
atts=atts+this.buildAttString("connectorColor","mConnectorColor",true,"");
atts=atts+this.buildAttString("hoverColor","mHoverColor",true,"");
atts=atts+this.buildAttString("canvasBorderColor","mCanvasBorderColor",true,"");
atts=atts+this.buildAttString("baseFont","mBaseFont",true,"");
atts=atts+this.buildAttString("baseFontSize","mBaseFontSize",true,"");
atts=atts+this.buildAttString("baseFontColor","mBaseFontColor",true,"");
atts=atts+this.buildAttString("markerBorderColor","mMarkerBorderColor",true,"");
atts=atts+this.buildAttString("markerBgColor","mMarkerBgColor",true,"");
atts=atts+this.buildAttString("markerRadius","mMarkerRadius",true,"");
atts=atts+this.buildAttString("legendPosition","mLegendPosition",false,"");
atts=atts+this.buildAttString("useHoverColor","mUseHoverColor",false,"2");
atts=atts+this.buildAttString("showToolTip","mShowToolTip",false,"1");
atts=atts+this.buildAttString("showMarkerToolTip","mShowMarkerToolTip",false,"2");
atts=atts+this.buildAttString("formatNumberScale","mFormatNumberScale",false,"1");
atts=atts+this.buildAttString("numberPrefix","mNumberPrefix",true,"");
atts=atts+this.buildAttString("numberSuffix","mNumberSuffix",true,"");
return atts;
}
infosoftglobal.FusionMapsGUI.prototype.getDataAsXML=function(){
var dataXML="\t<data>\n";
for(i=1;i<this.entities.length;i++){
var entityEl="\t\t<entity id='"+this.entities[i].id+"' ";
entityEl=entityEl+this.buildAttString("value","eValue"+i,false,"");
entityEl=entityEl+this.buildAttString("displayValue","eDisplayValue"+i,true,"");
entityEl=entityEl+this.buildAttString("toolText","eToolText"+i,true,"");
entityEl=entityEl+this.buildAttString("link","eLink"+i,true,"");
entityEl=entityEl+this.buildAttString("color","eColor"+i,true,"");
entityEl=entityEl+" />\n";
dataXML=dataXML+entityEl;
}
dataXML=dataXML+"\t</data>\n";
return dataXML;
}
infosoftglobal.FusionMapsGUI.prototype.getEntityXMLTemplate=function(){
var i;
var entityXML="<data>\n";
for(i=1;i<this.entities.length;i++){
entityXML=entityXML+"\t<entity id='"+String(this.entities[i].id)+"' value='' />\n";
}
entityXML=entityXML+"</data>";
return entityXML;
}
infosoftglobal.FusionMapsGUI.prototype.getMarkerDefXML=function(threeTabs){
var defXML="";
var i;
var id;
for(i=0;i<this.markers.length;i++){
id=this.markers[i];
var markerXML=((threeTabs==true)?"\t\t\t":"\t\t")+"<marker id='"+id+"' x='"+this.markerPos[i].x+"' y='"+this.markerPos[i].y+"' ";
markerXML=markerXML+this.buildAttString("label","mLabel_"+id,true,"");
markerXML=markerXML+this.buildAttString("labelPos","mLabelPos_"+id,false,"top");
markerXML=markerXML+" />\n";
defXML=defXML+markerXML;
}
return defXML;
}
infosoftglobal.FusionMapsGUI.prototype.getMarkerAppXML=function(threeTabs){
var appXML="";
var i;
var id;
var show;
for(i=0;i<this.markers.length;i++){
id=this.markers[i];
show=this.getValue("mShow_"+id,false);
if(show=="1"){
var markerXML=((threeTabs==true)?"\t\t\t":"\t\t")+"<marker id='"+id+"' ";
markerXML=markerXML+this.buildAttString("shapeId","mShape_"+id,false,"");
markerXML=markerXML+" />\n";
appXML=appXML+markerXML;
}
}
return appXML;
}
infosoftglobal.FusionMapsGUI.prototype.buildAttString=function(attName,elementName,encodeSafe,defaultValue){
var attString="";
var val=this.getValue(elementName,encodeSafe);
if(val!=defaultValue){
attString=attName+"='"+val+"' ";
}
return attString;
}
infosoftglobal.FusionMapsGUI.prototype.getValue=function(elementName,encodeSafe){
var el=this.gForm[elementName];
var rtnVal;
switch(el.type){
case"text":
rtnVal=el.value;
break;
case"textarea":
rtnVal=el.value;
break;
case"select-one":
rtnVal=el.value;
break;
case"checkbox":
rtnVal=(el.checked)?"1":"0";
encodeSafe=false;
break;
default:
rtnVal="";
encodeSafe=false;
break;
}
if(encodeSafe==true){
rtnVal=this.encodeStr(rtnVal);
}
return rtnVal;
}
infosoftglobal.FusionMapsGUI.prototype.encodeStr=function(str){
str=str.replace(/'/g,"&apos;");
str=str.replace(/</g,"&lt;");
str=str.replace(/>/g,"&gt;");
return str;
}
infosoftglobal.FusionMapsGUI.prototype.isSelected=function(selectString,matchString){
if(selectString==matchString){
return" selected ";
}else{
return"";
}
}
infosoftglobal.FusionMapsGUI.prototype.reInit=function(){
this.mapIndex=-1;
this.chooseMode=false;
this.entities=new Array();
this.markers=new Array();
this.markerPos=new Array();
}
infosoftglobal.FusionMapsGUI.prototype.updateMapFromFMQS=function(){
var customXML="";
var FMQS=unescape(this.getValue("xmlDataFull"));
var FMC=new FusionMapsCreator(FMQS);
var dataXML=FMC.getXML(customXML);
if(markerWinOpened==true&&navigator.plugins&&navigator.mimeTypes&&navigator.mimeTypes.length){
isReload=true;
var map=new FusionMaps(this.mapPath+this.mapList[this.mapIndex].swf,this.mapId,this.mapList[this.mapIndex].width,this.mapList[this.mapIndex].height,"0","1");
map.setDataXML(dataXML);
map.render("mapdiv");
}else{
this.mapObj.setDataXML(dataXML,false);
}
FMC=null;
}
infosoftglobal.FusionMapsGUI.prototype.getFMQSMapConfig=function(){
return"?map=[mapName="+this.mapList[this.mapIndex].swf.replace(/FCMap_|.swf/ig,"")+";mapWidth="+this.mapList[this.mapIndex].width+";mapHeight="+this.mapList[this.mapIndex].height+"]";
}
infosoftglobal.FusionMapsGUI.prototype.getFullFMQS=function(){
var FMQS=this.getFMQSMapConfig()+"\n";
FMQS+="&mapParams=["+this.getFMQSMapElementAtts()+"]\n";
FMQS=FMQS+this.getDataFMQS();
if(this.markers.length>0){
FMQS=FMQS+this.getMarkerDefFMQS()+"\n";
FMQS=FMQS+this.getMarkerAppFMQS()+"\n";
}
return FMQS;
}
infosoftglobal.FusionMapsGUI.prototype.getFMQSMapElementAtts=function(){
var atts="";
atts=atts+this.buildFMQSAttString("animation","mAnimation",false,"1");
atts=atts+this.buildFMQSAttString("showShadow","mShowShadow",false,"1");
atts=atts+this.buildFMQSAttString("showBevel","mShowBevel",false,"1");
atts=atts+this.buildFMQSAttString("showLegend","mShowLegend",false,"1");
atts=atts+this.buildFMQSAttString("showLabels","mShowLabels",false,"1");
atts=atts+this.buildFMQSAttString("showMarkerLabels","mShowMarkerLabels",false,"2");
atts=atts+this.buildFMQSAttString("useSNameInToolTip","mUseSNameInToolTip",false,"0");
atts=atts+this.buildFMQSAttString("includeNameInLabels","mIncludeNameInLabels",false,"1");
atts=atts+this.buildFMQSAttString("includeValueInLabels","mIncludeValueInLabels",false,"0");
atts=atts+this.buildFMQSAttString("fillColor","mFillColor",true,"");
atts=atts+this.buildFMQSAttString("borderColor","mBorderColor",true,"");
atts=atts+this.buildFMQSAttString("connectorColor","mConnectorColor",true,"");
atts=atts+this.buildFMQSAttString("hoverColor","mHoverColor",true,"");
atts=atts+this.buildFMQSAttString("canvasBorderColor","mCanvasBorderColor",true,"");
atts=atts+this.buildFMQSAttString("baseFont","mBaseFont",true,"");
atts=atts+this.buildFMQSAttString("baseFontSize","mBaseFontSize",true,"");
atts=atts+this.buildFMQSAttString("baseFontColor","mBaseFontColor",true,"");
atts=atts+this.buildFMQSAttString("markerBorderColor","mMarkerBorderColor",true,"");
atts=atts+this.buildFMQSAttString("markerBgColor","mMarkerBgColor",true,"");
atts=atts+this.buildFMQSAttString("markerRadius","mMarkerRadius",true,"");
atts=atts+this.buildFMQSAttString("legendPosition","mLegendPosition",false,"");
atts=atts+this.buildFMQSAttString("useHoverColor","mUseHoverColor",false,"2");
atts=atts+this.buildFMQSAttString("showToolTip","mShowToolTip",false,"1");
atts=atts+this.buildFMQSAttString("showMarkerToolTip","mShowMarkerToolTip",false,"2");
atts=atts+this.buildFMQSAttString("formatNumberScale","mFormatNumberScale",false,"1");
atts=atts+this.buildFMQSAttString("numberPrefix","mNumberPrefix",true,"");
atts=atts+this.buildFMQSAttString("numberSuffix","mNumberSuffix",true,"");
atts=atts.replace(/^;+|;+$/g,"");
atts=atts.replace(/;+/,";");
return atts;
}
infosoftglobal.FusionMapsGUI.prototype.getDataFMQS=function(){
var dataFMQS="&data=";
for(i=1;i<this.entities.length;i++){
var entityEl=this.entities[i].id+"=";
entityEl+=this.getValue("eValue"+i,false)+";";
entityEl+=this.buildFMQSAttString("displayValue","eDisplayValue"+i,true,"")+";";
entityEl+=this.buildFMQSAttString("toolText","eToolText"+i,true,"")+";";
entityEl+=this.buildFMQSAttString("link","eLink"+i,true,"")+";";
entityEl+=this.buildFMQSAttString("color","eColor"+i,true,"");
entityEl=entityEl.replace(/^;+|;+$/g,"");
entityEl=entityEl.replace(/;+/g,";");
dataFMQS=dataFMQS+"["+entityEl+"]";
}
return dataFMQS;
}
infosoftglobal.FusionMapsGUI.prototype.getEntityFMQSTemplate=function(){
var i;
var entityFMQS="&data=";
for(i=1;i<this.entities.length;i++){
entityFMQS=entityFMQS+"["+String(this.entities[i].id)+"= ]";
}
return entityFMQS;
}
infosoftglobal.FusionMapsGUI.prototype.getMarkerDefFMQS=function(){
var mDef=this.markers.length>0?"&markerDef=":"";
var i;
var id;
var marker=""
for(i=0;i<this.markers.length;i++){
id=this.markers[i];
var marker="id="+id+";x="+this.markerPos[i].x+";y="+this.markerPos[i].y+";";
marker+=this.buildFMQSAttString("label","mLabel_"+id,true,"");
marker+=this.buildFMQSAttString("labelPos","mLabelPos_"+id,false,"top");
marker=marker.replace(/^;+|;+$/g,"");
mDef+=("["+marker+"]");
}
mDef=mDef.replace(/;+/,";");
return mDef;
}
infosoftglobal.FusionMapsGUI.prototype.getMarkerAppFMQS=function(){
var mApp="";
var i;
var id;
var show;
for(i=0;i<this.markers.length;i++){
id=this.markers[i];
show=this.getValue("mShow_"+id,false);
if(show=="1"){
var marker="id="+id+";";
marker+=this.buildFMQSAttString("shapeId","mShape_"+id,false,"");
marker=marker.replace(/^;+|;+$/g,"");
mApp+=("["+marker+"]");
}
}
mApp=mApp.replace(/;+/,";");
if(this.trim(mApp)!="")mApp="&markerApp="+mApp;
return mApp;
}
infosoftglobal.FusionMapsGUI.prototype.buildFMQSAttString=function(attName,elementName,encodeSafe,defaultValue){
var attString="";
var val=this.getValue(elementName,encodeSafe);
if(val!=defaultValue){
attString=attName+"="+val+";";
}
return attString;
}
infosoftglobal.FusionMapsGUI.prototype.getFV=function(val){
var ret="";
if(typeof val!="undefined"&&val!=null&&val!="")
ret=val;
return ret;
}
infosoftglobal.FusionMapsGUI.prototype.trim=function(str){
var str=this.getFV(str);
str=str.replace(/^\s+/,"");
str=str.replace(/\s+$/,"");
return str;
}
var FusionMapsGUI=infosoftglobal.FusionMapsGUI;