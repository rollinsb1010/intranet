/**
 * FusionMapsCreator: generate XML data document for FusionMaps 
 * from search query string passed as URL or GET. 
 * (we get this from document.location.search and pass as constructor paramater - rawData)
 * The query string may look like this - 
 *
 *   ?map=[mapName=USA;mapWidth=800;mapHieght=600]&mapParams=[caption=Monthly Sales;subCaption=...]
 *   &data=[AR=100;HI=200]
 *   
 */
if(typeof infosoftglobal=="undefined")var infosoftglobal=new Object();
if(typeof infosoftglobal.FusionMapsUtil=="undefined")infosoftglobal.FusionMapsUtil=new Object();

//Constructor : Class FusionMapsCreator(paramerter)
// rawData - acctepts FusionMaps Querystring
infosoftglobal.FusionMapsCreator=function(rawData){
	//instanciating Map Data container object
	this.FM=new MapData();
	//adding & at the begining and end of FMQS so that no problem ocurrs when searching with .indexOf("&")
	rawData=rawData.replace(/\?/,"&")+"&";
	
	//Parsing User Defined Separator
	this.separator=this.getSep(rawData);
	
	//Getting Map External Configuration from FMQS and storing in FM
	this.parseMapConfig(rawData);
	
	//Getting Map's Configuration parameters from FMQS and storing in FM
	this.parseMapParams(rawData);
	
	//Entry re-difinition
	this.parseLdDef(rawData);
	
	//Color Range definitions
	this.parseColorRange(rawData);
	
	//Getting Map Data from FMQS and storing in FM
	this.parseMapData(rawData);
	
	//getting markers
	this.parseMarkers(rawData);
	
	//Getting Styles and storing in FM
	this.parseStyleDefs(rawData);
	this.parseStyleApps(rawData);
	
	//Building  MapXML
	this.FM.dataXML=this.buildMapXML();
	
	//Show debug messages if debug mode is on
	this.parseDebugMode(rawData);
}

//----------------------------------------------//
// 			 class modules 						//
//----------------------------------------------//
infosoftglobal.FusionMapsCreator.prototype={


	//getQSParam accepts FMQS - rawData  and parses it and extracts & returns the specified parameter's value
	//QSData - rawData or query string 
	//paramName - name of the QS param whose valu is to be returned
	getQSParam:function(QSData,paramName){
		//finding the presence of the param name in FMQS
		var strParam="";
		var sIndex=QSData.toLowerCase().indexOf("&"+paramName+"=");
		var lIndex=QSData.indexOf("&",sIndex+1);
		//extracting the param value if found (sIndex != 1)
		strParam=(sIndex!=-1?QSData.substring(sIndex+paramName.length+2,lIndex):"");
		//removing beginning [ (s)
		strParam=strParam.replace(/^[\[\]]+/,"");
		//removing ending ] (s)
		strParam=strParam.replace(/[\[\]]+$/,"");
		
		//returning the param value
		return strParam;
	},


	// parses QS and extract the delimiter taken as QS param. default is ;
	getSep:function(rawData){
		//getting delimeter character
		var sep=this.getQSParam(rawData,"sep");
		//if not defined take the default ; character as delimiter
		if(sep==null||sep==""||typeof(sep)=="undefined")
			sep=";";
		return sep;
	},
	
	
	//
	buildCustomAttributesXML : function(attrbNames, strAttrbs,elementName){
		var attrbXML="";
		var attrbs="";

		var arrAttrbs=strAttrbs.split(/\]\s*\[/);
		for(var j in arrAttrbs){
			var arrElementAttrbs=arrAttrbs[j].split(this.separator);

			attrbs="";
			for(var i=0;i<arrElementAttrbs.length;i++){
				if(arrElementAttrbs[i].search(/\=/)>=0){
					attrbs+=arrElementAttrbs[i]+";";
				}
				else{
					if(attrbNames[i])
							 
							if(attrbNames[i]=="value") arrElementAttrbs[i]=this.trim(this.validateData(arrElementAttrbs[i]));	
							if(attrbNames[i]=="id") arrElementAttrbs[i]=this.trim(arrElementAttrbs[i]);																													   
							attrbs+=attrbNames[i]+"="+this.trim(arrElementAttrbs[i])+";";
				}
			}
			attrbXML+="<"+elementName +" "+this.buildAttribXML(attrbs)+" />";
		}
		return attrbXML;
	},
	
	
		
	validateData:function(data){
		//checking for unwanted data.Adding apropriate debug message
		if(data.search(new RegExp("[^-^0-9^,^.^\\"+this.separator+"]","g"))>=0){
				this.setDebugStr( "DATA VALIDATION "+ data + " : Data Value has unwanted characters. Removing them.","Warning");
			//clening unwanted characters. 
			data=data.replace(/[^-^0-9^,^.]/g,"");
		}
		
		return data;
	},
	
	
	//parses FMQS, extract data params (using getQSDataParams()) and stores in data containers
	parseMapData:function(rawData){
		var arrEntity=["id","value","color","displayValue","tooltext","link"];
		var strMapData = this.getQSParam(rawData,"data");
		var arrMapData=strMapData.split(/\]\s*\[/);
		if(this.trim(strMapData).length!=0){
			var mapDataXML="<data>";
			for(var i in arrMapData){
				arrMapData[i] = arrMapData[i].replace(/=/,this.separator);
		
				if(this.trim(arrMapData[i]).length!=0){
					mapDataXML+=this.buildCustomAttributesXML(arrEntity,arrMapData[i],"entity");
				}
			}
			mapDataXML+="</data>";
		}

		this.FM.MapDataXML = mapDataXML;
	},

	//
	parseLdDef : function(rawData){ 
		var arrEntity=["internalId","newId","sName","lName"];
		var strLdDef= this.getQSParam(rawData,"lddef");
		var arrLdDef =strLdDef.split(/\]\s*\[/);
		if(this.trim(strLdDef).length!=0){
			var ldDefXML="<entityDef>";
			for(var i in arrLdDef){
				arrLdDef[i] = arrLdDef[i].replace(/=/,this.separator);
		
				if(this.trim(arrLdDef[i]).length!=0){
					ldDefXML+=this.buildCustomAttributesXML(arrEntity,arrLdDef[i],"entity");
				}
			}
			ldDefXML+="</entityDef>";
		}
		this.FM.ldDefXML = ldDefXML;
	},

	
	//getting Map Configurations : extracting Map type, width, height and appropriate Map file
	parseMapConfig:function(rawData){
		this.FM.config={mapname:"USA",SWF:"FCMap_USA.swf",mapwidth:"600",mapheight:"300"};
		//extracting Map configurations from FMQS
		var strMapConfig=this.getQSParam(rawData,"map");
		var arrMapConfig = strMapConfig.split(this.separator);
		var config;
		for(var i in arrMapConfig){
			if(arrMapConfig[i]!=""){
				config=arrMapConfig[i].split("=");
				if(config[0]!=""){
					if(this.getFV(config[1])=="" && i==0) {
						this.FM.config["mapname"]=this.trim(config[0]);
						continue;
					}
					this.FM.config[this.trim(config[0]).toLowerCase()]=this.trim(config[1]);
				}
			}
		}
		this.getMapSWF();
	},
	


	//this method accepts MapType name and decides the appropriate map swf name
	getMapSWF:function(strMapName){
		this.FM.config["SWF"]="FCMap_"+this.FM.config["mapname"]+".swf";
	},


	//getting char attributes for Map root tag 
	parseMapParams:function(rawData){
		//extracting Map attributes from QS
		var strMapParams=this.getQSParam(rawData,"mapparams");
		//converting attributes into XML format
		this.FM.mapParams=this.buildAttribXML(strMapParams);
	},


	//extract debugMode param and set accrodingly
	parseDebugMode:function(rawData){
		// searching for &debugMode=... in QS 
		var debug=this.getQSParam(rawData,"debugmode");
		//if above is found i.e. debug is not null set debugMode to 1 (on) or 0 (off)
		this.FM.debugMode=(isNaN(parseInt(debug))||parseInt(debug)!=1)?0:1;
		
		//show all error messages (if debug mode is on) as alert
		if(this.FM.debugMode==1&&this.FM.errCount>0)
			alert(this.FM.StrErr);
	},




	//parses an attribute list and renders it to proper XML element atribute list 
	buildAttribXML:function(strParams){
		//changing all escaped space %20 = ' ' :as many browsers pass space escaped
		strParams=strParams.replace(/\%20/g," ");
		//removing  all [  and ]  chars 
		strParams=strParams.replace(/\[\s*|\s*\]/g,"");
		//trimming spaces
		strParams=this.trim(strParams);
		//removing any space afer the defined delimiter
		strParams=strParams.replace(new RegExp(this.separator+"\\s*","g"),this.separator);
		//XML encodeing all ' and " - to %26apos%3b( &apos; ) &  %26quot%3b (&quot;)
		strParams=strParams.replace(/'/g,'%26apos%3b');
		strParams=strParams.replace(/"/g,'%26quot%3b');
		
		//separately splitting all params to validata and add quoted to the values : 
		var arrParams=strParams.split(this.separator);
		//store the resultant XML attribute list
		var params="";
		for(var i in arrParams){
			//spliting each attribute to atrib name an value
			var arrEachParam=arrParams[i].split("=");
			if(arrEachParam[0]!=""){
				//adding quotes to the attrib values
				params+=" "+arrEachParam[0].replace(/\s*/g,"")+"='"+arrEachParam[1]+"'";
			}
		}
		//return parameter
		return this.getFV(params);
	},
	


	//Forms Single Series Map
	buildMapXML:function(){
		var XML ="<map "+ this.FM.mapParams+">";
			
		XML+=this.getFV(this.FM.colorRangeXML);
		XML+=this.getFV(this.FM.ldDefXML);
		XML+=this.getFV(this.FM.MapDataXML);
		XML+=this.getFV(this.FM.markersXML);

		if((typeof this.FM.styleApp!="undefined" )&& (typeof this.FM.styleDef!="undefined"))
			XML+="<styles>"+this.getFV(this.FM.styleDef)+this.getFV(this.FM.styleApp)+"</styles>";
		
		
		XML+="</map>";
		//return dataXML
		return XML;
		
	},

	parseColorRange:function(rawData){
		var arrColorRangeParams=["minValue","maxValue","color","displayValue"];
		var strCR= this.getQSParam(rawData,"colorrange");
		var XML="";

		if(this.trim(strCR).length!=0){
			XML+="<colorRange>";
			XML+=this.buildCustomAttributesXML(arrColorRangeParams,strCR,"color");
			XML+="</colorRange>";
		}
		this.FM.colorRangeXML = XML;
	
	},

	parseMarkers:function(rawData){
		var XML="";
		XML+=this.getFV(this.parseMarkersDef(rawData));
		XML+=this.getFV(this.parseMarkersShapes(rawData));
		XML+=this.getFV(this.parseMarkersApp(rawData));
		XML+=this.getFV(this.parseMarkersConn(rawData));
		if(this.trim(XML).length!=0){
			XML="<markers>"+XML;
			XML+="</markers>";
		}
		
		this.FM.markersXML=XML;
	},
	parseMarkersDef:function(rawData){

		var arrMDefAttribs=["x","y","id","label","labelPos"];
		
		var strMdef = this.getQSParam(rawData,"markerdef");
		var XML="";
		if(this.trim(strMdef).length!=0){
			XML+="<definition>";
			XML+=this.buildCustomAttributesXML(arrMDefAttribs,strMdef,"marker");
			XML+="</definition>";
		}
		return XML;
	},
	parseMarkersApp:function(rawData){
		var arrMAppAttribs=["id","shapeId","label","labelPos","toolText","link","scale"];
		
		var strMApp = this.getQSParam(rawData,"markerapp");
		var XML="";
		if(this.trim(strMApp).length!=0){
			XML+="<application>";
			XML+=this.buildCustomAttributesXML(arrMAppAttribs,strMApp,"marker");
			XML+="</application>";
		}
		return XML;
	},
	parseMarkersShapes:function(rawData){
		var arrMShpAttribs=["id","type","url","alpha"];
		
		var strMShp = this.getQSParam(rawData,"markershapes");
		var XML="";
		if(this.trim(strMShp).length!=0){
			XML+="<shapes>";
			XML+=this.buildCustomAttributesXML(arrMShpAttribs,strMShp,"shape");
			XML+="</shapes>";
		}
		return XML;
	
	},
	parseMarkersConn:function(rawData){//fromId,toId,label,,,
		var arrMConnAttribs=["from","to","label","toolText","color","thickness","alpha","link","dashed","dhashedlen","dashGap"];
		
		var strMConn = this.getQSParam(rawData,"markerconn");
		var XML="";
		if(this.trim(strMConn).length!=0){
			XML+="<connectors>";
			XML+=this.buildCustomAttributesXML(arrMConnAttribs,strMConn,"connector");
			XML+="</connectors>";
		}
		return XML;
	},

	parseStyleDefs:function(rawData){
		//extracting style definitions from FMQS
		var strStyleDefs=this.getQSParam(rawData,"styledef");
		
		//splitting multiple styles definitions (if defined ) into array
		var arrStyleDefs=strStyleDefs.split(/\]\s*\[/);

		if(this.trim(arrStyleDefs.toString()).length!=0){
			//initializing string to store Style Defs XML 
			var strStyles="<definition>";
			
			//iterating through each style def
			for(var i in arrStyleDefs){
				if(this.buildAttribXML(arrStyleDefs[i])!=""){
					//convert to proper XML ready attribute
					strStyles+="<style "+this.buildAttribXML(arrStyleDefs[i])+" />";
				}
			}
			strStyles+="</definition>";
		}
		//store style def in FM
		this.FM.styleDef=strStyles;
	},



	parseStyleApps:function(rawData){
		//extracting style application from FMQS
		var strStyleApps=this.getQSParam(rawData,"styleapp");
		
		//splitting multiple styles app(if defined ) into array
		var arrStyleApps=strStyleApps.split(/\]\s*\[/);
		if(this.trim(arrStyleApps.toString()).length!=0){
			//initializing string to store Style app XML 
			var strStylesA="<application>";
			
			//iterating through each style app
			for(var i in arrStyleApps){
				if(this.buildAttribXML(arrStyleApps[i])!="")
					//convert to proper XML ready attribute
					strStylesA+="<apply "+this.buildAttribXML(arrStyleApps[i])+" />";
			}
			
			strStylesA+="</application>";
		}
		//store style def in FM
		this.FM.styleApp=strStylesA;
	},




	//getFV function that converts all null and undefined value to ""
	getFV: function(v){
		var bv="";
		if(v==null || v=="" || typeof v=="undefined")
			return bv;
		else
			return v;
	
	},




	//fucntion to trim spaces
	trim:function(str){
		str=this.getFV(str);
		if(str!=""||str!=null){
			str=str.replace(/^\s+/,"");
			str=str.replace(/\s+$/,"");
		}
		return str;
	},
	
	
	//public method returning appropriate Map swf file name
	getSWF:function(){return this.FM.config.SWF;},
	
	//public method returning Map width
	getWidth:function(){return this.FM.config.mapwidth;},
	
	//public method returning Map height 
	getHeight:function(){return this.FM.config.mapheight;},
	
	//public method  returning Map dataXML
	getXML:function(cXML){
		var dXML =this.FM.dataXML;
		var rootClose ="</map>";
		if(cXML!="" || cXML!=null)
		{	
		
			dXML = dXML.replace(rootClose,this.getFV(cXML)+rootClose);
		}
		return dXML;
	},
	
	
	//public method to return debug mode flag value
	getDebugMode:function(){return this.FM.debugMode.toString();},
	
	
	//public method to return Map message
	getMapMSG:function(){return"?"+this.FM.msg;},
	
	
	//concatinates error messages in a string to be shown in debug mode 
	setDebugStr:function(str,etype){
		//increase error counter by 1
		this.FM.errCount++;
		//concatinate error messsage
		this.FM.StrErr = this.FM.StrErr +  (etype+" "+this.FM.errCount+" : "+str+"\n\n");
	}
}


/*---< start >-----FUSIONMapS - CHAT DATA-----------------------------------------------------*/
infosoftglobal.FusionMapsUtil.MapData=function(){
	//container to store Map's external configurations in object :
	//
	this.config=new Object();//{mapname:"USA",SWF:"FCMap_USA.swf",mapwidth:"600",mapheight:"300"}

	//stores the Map root elements's attributes
	this.mapParams="";
	
	//stored  XML of data element of a map
	this.MapDataXML="";
	
	//stored  XML of data element of a map
	this.ldDefXML="";
	
	//stored  markers XML of data element of a map
	this.markersXML="";
	this.markersDefXML="";
	this.markersAppXML="";
	this.markersShapesXML="";
	this.markersConnXML="";

	//stored  XML of data element of a map
	this.colorRangeXML="";
	
	//Style Definitions
	this.styleDef ="";
	//Style Application 
	this.styleApp = "";
	
	
	//container to store Map's dataXML 
	this.DataXML="";
	
	//debug mode : show alert message on error (also debug window in version 3 )
	//flag => 1/0: ON/OFF 
	this.debugMode=0;
	
	//stores all error messages
	this.StrErr="";
	
	//error counter :  no of errors
	this.errCount=0;
	
	//msg stores special messages shown ny the Map
	this.msg="MapNoDataText=Please Provide Map Data";
}


/*---< end >-----FUSIONMapS - CHAT DATA-----------------------------------------------------*/

/* Aliases for easy usage */
var FusionMapsCreator=infosoftglobal.FusionMapsCreator;
var MapData=infosoftglobal.FusionMapsUtil.MapData;
