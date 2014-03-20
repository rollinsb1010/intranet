/**
 * FusionMapsCreator: generate XML data document for FusionMaps 
 * from search query string passed as URL or GET. 
 * (we get this from document.location.search and pass as constructor paramater - rawData)
 * The query string may look like this - 
 *
 *   ?map=[mapName=USA;mapWidth=800;mapHieght=600]&mapParams=[caption=Monthly Sales;subCaption=...]
 *   &data=[AR=100;HI=200]
 *   
 * LICENSING INFO: This JavaScript file should ONLY be used with FusionMaps Pro for FileMaker.
 */
if(typeof infosoftglobal=="undefined")var infosoftglobal=new Object();
if(typeof infosoftglobal.FusionMapsUtil=="undefined")infosoftglobal.FusionMapsUtil=new Object();
infosoftglobal.FusionMapsCreator=function(rawData){
this.FM=new MapData();
rawData=rawData.replace(/\?/,"&")+"&";
this.separator=this.getSep(rawData);
this.parseMapConfig(rawData);
this.parseMapParams(rawData);
this.parseLdDef(rawData);
this.parseColorRange(rawData);
this.parseMapData(rawData);
this.parseMarkers(rawData);
this.parseStyleDefs(rawData);
this.parseStyleApps(rawData);
this.FM.dataXML=this.buildMapXML();
this.parseDebugMode(rawData);
}
infosoftglobal.FusionMapsCreator.prototype={
getQSParam:function(QSData,paramName){
var strParam="";
var sIndex=QSData.toLowerCase().indexOf("&"+paramName+"=");
var lIndex=QSData.indexOf("&",sIndex+1);
strParam=(sIndex!=-1?QSData.substring(sIndex+paramName.length+2,lIndex):"");
strParam=strParam.replace(/^[\[\]]+/,"");
strParam=strParam.replace(/[\[\]]+$/,"");
return strParam;
},
getSep:function(rawData){
var sep=this.getQSParam(rawData,"sep");
if(sep==null||sep==""||typeof(sep)=="undefined")
sep=";";
return sep;
},
buildCustomAttributesXML:function(attrbNames,strAttrbs,elementName){
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
if(attrbNames[i]=="value")arrElementAttrbs[i]=this.trim(this.validateData(arrElementAttrbs[i].replace(/^\[|\]$/g,"")));
if(attrbNames[i]=="id")arrElementAttrbs[i]=this.trim(arrElementAttrbs[i]);
attrbs+=attrbNames[i]+"="+this.trim(arrElementAttrbs[i])+";";
}
}
attrbXML+="<"+elementName+" "+this.buildAttribXML(attrbs)+" />";
}
return attrbXML;
},
validateData:function(data){
if(data.search(new RegExp("[^-^0-9^,^.^\\"+this.separator+"]","g"))>=0){
this.setDebugStr("DATA VALIDATION "+data+" : Data Value has unwanted characters. Removing them.","Warning");
data=data.replace(/[^-^0-9^,^.]/g,"");
}
return data;
},
parseMapData:function(rawData){
var arrEntity=["id","value","color","displayValue","tooltext","link"];
var strMapData=this.getQSParam(rawData,"data");
var arrMapData=strMapData.split(/\]\s*\[/);
if(this.trim(strMapData).length!=0){
var mapDataXML="<data>";
for(var i in arrMapData){
arrMapData[i]=arrMapData[i].replace(/=/,this.separator);
if(this.trim(arrMapData[i]).length!=0){
mapDataXML+=this.buildCustomAttributesXML(arrEntity,arrMapData[i],"entity");
}
}
mapDataXML+="</data>";
}
this.FM.MapDataXML=mapDataXML;
},
parseLdDef:function(rawData){
var arrEntity=["internalId","newId","sName","lName"];
var strLdDef=this.getQSParam(rawData,"lddef");
var arrLdDef=strLdDef.split(/\]\s*\[/);
if(this.trim(strLdDef).length!=0){
var ldDefXML="<entityDef>";
for(var i in arrLdDef){
arrLdDef[i]=arrLdDef[i].replace(/=/,this.separator);
if(this.trim(arrLdDef[i]).length!=0){
ldDefXML+=this.buildCustomAttributesXML(arrEntity,arrLdDef[i],"entity");
}
}
ldDefXML+="</entityDef>";
}
this.FM.ldDefXML=ldDefXML;
},
parseMapConfig:function(rawData){
this.FM.config={mapname:"USA",SWF:"FCMap_USA.swf",mapwidth:"600",mapheight:"300"};
var strMapConfig=this.getQSParam(rawData,"map");
var arrMapConfig=strMapConfig.split(this.separator);
var config;
for(var i in arrMapConfig){
if(arrMapConfig[i]!=""){
config=arrMapConfig[i].split("=");
if(config[0]!=""){
if(this.getFV(config[1])==""&&i==0){
this.FM.config["mapname"]=this.trim(config[0]);
continue;
}
this.FM.config[this.trim(config[0]).toLowerCase()]=this.trim(config[1]);
}
}
}
this.getMapSWF();
},
getMapSWF:function(strMapName){
this.FM.config["SWF"]="FCMap_"+this.FM.config["mapname"]+".swf";
},
parseMapParams:function(rawData){
var strMapParams=this.getQSParam(rawData,"mapparams");
this.FM.mapParams=this.buildAttribXML(strMapParams);
},
parseDebugMode:function(rawData){
var debug=this.getQSParam(rawData,"debugmode");
this.FM.debugMode=(isNaN(parseInt(debug))||parseInt(debug)!=1)?0:1;
if(this.FM.debugMode==1&&this.FM.errCount>0)
alert(this.FM.StrErr);
},
buildAttribXML:function(strParams){
strParams=strParams.replace(/\%20/g," ");
strParams=strParams.replace(/\[\s*|\s*\]/g,"");
strParams=this.trim(strParams);
strParams=strParams.replace(new RegExp(this.separator+"\\s*","g"),this.separator);
strParams=strParams.replace(/'/g,'%26apos%3b');
strParams=strParams.replace(/"/g,'%26quot%3b');
var arrParams=strParams.split(this.separator);
var params="";
for(var i in arrParams){
var arrEachParam=arrParams[i].split("=");
if(arrEachParam[0]!=""){
params+=" "+arrEachParam[0].replace(/\s*/g,"")+"='"+arrEachParam[1]+"'";
}
}
return this.getFV(params);
},
buildMapXML:function(){
var XML="<map "+this.FM.mapParams+">";
XML+=this.getFV(this.FM.colorRangeXML);
XML+=this.getFV(this.FM.ldDefXML);
XML+=this.getFV(this.FM.MapDataXML);
XML+=this.getFV(this.FM.markersXML);
if((typeof this.FM.styleApp!="undefined")&&(typeof this.FM.styleDef!="undefined"))
XML+="<styles>"+this.getFV(this.FM.styleDef)+this.getFV(this.FM.styleApp)+"</styles>";
XML+="</map>";
return XML;
},
parseColorRange:function(rawData){
var arrColorRangeParams=["minValue","maxValue","color","displayValue"];
var strCR=this.getQSParam(rawData,"colorrange");
var XML="";
if(this.trim(strCR).length!=0){
XML+="<colorRange>";
XML+=this.buildCustomAttributesXML(arrColorRangeParams,strCR,"color");
XML+="</colorRange>";
}
this.FM.colorRangeXML=XML;
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
var strMdef=this.getQSParam(rawData,"markerdef");
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
var strMApp=this.getQSParam(rawData,"markerapp");
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
var strMShp=this.getQSParam(rawData,"markershapes");
var XML="";
if(this.trim(strMShp).length!=0){
XML+="<shapes>";
XML+=this.buildCustomAttributesXML(arrMShpAttribs,strMShp,"shape");
XML+="</shapes>";
}
return XML;
},
parseMarkersConn:function(rawData){
var arrMConnAttribs=["from","to","label","toolText","color","thickness","alpha","link","dashed","dhashedlen","dashGap"];
var strMConn=this.getQSParam(rawData,"markerconn");
var XML="";
if(this.trim(strMConn).length!=0){
XML+="<connectors>";
XML+=this.buildCustomAttributesXML(arrMConnAttribs,strMConn,"connector");
XML+="</connectors>";
}
return XML;
},
parseStyleDefs:function(rawData){
var strStyleDefs=this.getQSParam(rawData,"styledef");
var arrStyleDefs=strStyleDefs.split(/\]\s*\[/);
if(this.trim(arrStyleDefs.toString()).length!=0){
var strStyles="<definition>";
for(var i in arrStyleDefs){
if(this.buildAttribXML(arrStyleDefs[i])!=""){
strStyles+="<style "+this.buildAttribXML(arrStyleDefs[i])+" />";
}
}
strStyles+="</definition>";
}
this.FM.styleDef=strStyles;
},
parseStyleApps:function(rawData){
var strStyleApps=this.getQSParam(rawData,"styleapp");
var arrStyleApps=strStyleApps.split(/\]\s*\[/);
if(this.trim(arrStyleApps.toString()).length!=0){
var strStylesA="<application>";
for(var i in arrStyleApps){
if(this.buildAttribXML(arrStyleApps[i])!="")
strStylesA+="<apply "+this.buildAttribXML(arrStyleApps[i])+" />";
}
strStylesA+="</application>";
}
this.FM.styleApp=strStylesA;
},
getFV:function(v){
var bv="";
if(v==null||v==""||typeof v=="undefined")
return bv;
else
return v;
},
trim:function(str){
str=this.getFV(str);
if(str!=""||str!=null){
str=str.replace(/^\s+/,"");
str=str.replace(/\s+$/,"");
}
return str;
},
getSWF:function(){return this.FM.config.SWF;},
getWidth:function(){return this.FM.config.mapwidth;},
getHeight:function(){return this.FM.config.mapheight;},
getXML:function(cXML){
var dXML=this.FM.dataXML;
var rootClose="</map>";
if(cXML!=""||cXML!=null)
{
dXML=dXML.replace(rootClose,this.getFV(cXML)+rootClose);
}
return dXML;
},
getDebugMode:function(){return this.FM.debugMode.toString();},
getMapMSG:function(){return"?"+this.FM.msg;},
setDebugStr:function(str,etype){
this.FM.errCount++;
this.FM.StrErr=this.FM.StrErr+(etype+" "+this.FM.errCount+" : "+str+"\n\n");
}
}
infosoftglobal.FusionMapsUtil.MapData=function(){
this.config=new Object();
this.mapParams="";
this.MapDataXML="";
this.ldDefXML="";
this.markersXML="";
this.markersDefXML="";
this.markersAppXML="";
this.markersShapesXML="";
this.markersConnXML="";
this.colorRangeXML="";
this.styleDef="";
this.styleApp="";
this.DataXML="";
this.debugMode=0;
this.StrErr="";
this.errCount=0;
this.msg="MapNoDataText=Please Provide Map Data";
}
/* Aliases for easy usage */
var FusionMapsCreator=infosoftglobal.FusionMapsCreator;
var MapData=infosoftglobal.FusionMapsUtil.MapData;
