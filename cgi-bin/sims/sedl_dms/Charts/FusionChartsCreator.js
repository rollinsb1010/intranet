/**
 * FusionChartsCreator: generate XML data document for FusionCharts 
 * from search query string passed as URL or GET. 
 * (we get this from document.location.search and pass as constructor paramater - rawData)
 * The query string may look like this - 
 *
 *   ?v=3&chart=[chartType=Column3D;chartWidth=800;chartHieght=600]&chartParams=[caption=Monthly Sales;subCaption=...]
 *   &labels=name1;name2;;labe14;...&data=[seriesName=Aomunt;parentYAxis=P]10;20;13;;34;..
 *   &data=[seriesName=Discount;parentYAxis=P]4;8;3;;;1|..&data=[seriesname=Quantity;parentYAxis=S]100;200;130;;40;..
 *   &trendlines=[startvalue=23;endvalue=26;color=FF00CC][startValue=3;endvalue=6;color=CCFF00]
 *
 */

if(typeof infosoftglobal=="undefined")var infosoftglobal=new Object();
if(typeof infosoftglobal.FusionChartsUtil=="undefined")infosoftglobal.FusionChartsUtil=new Object();
infosoftglobal.FusionChartsCreator=function(rawData){
this.FC=new ChartData();
rawData=rawData.replace(/\?/,"&")+"&";
this.separator=this.getSep(rawData);
this.parseVersion(rawData);
this.parseNumDS(rawData);
this.parseChartConfig(rawData);
switch(this.FC.specifications.dataType){
case"SS":
case"MS":
this.parseChartData(rawData);
break;
case"XY":
case"XYZ":
this.parseChartData(rawData);
break;
}
this.parseSliced(rawData);
this.parseToolText(rawData);
this.parseChartParams(rawData);
this.parseColors(rawData);
this.parseChartLabels(rawData);
this.parseDataAttribs(rawData);
this.parseCatAttribs(rawData);
this.parseVlines(rawData);
this.parseTrendlines(rawData);
this.parseStyleDefs(rawData);
this.parseStyleApps(rawData);
switch(this.FC.specifications.dataType){
case"SS":
this.FC.dataXML=this.buildSSXML();
break;
case"MS":
this.FC.dataXML=this.buildMSXML();
break;
case"XY":
case"XYZ":
this.buildVTrendLinesXML(rawData);
this.FC.dataXML=this.buildXYZXML();
break;
default:
this.FC.dataXML="<"+this.FC.verTags[this.FC.verTagIndex].chart+"></"+this.FC.verTags[this.FC.verTagIndex].chart+">";
}
this.parseDebugMode(rawData);
}
infosoftglobal.FusionChartsCreator.prototype={
getQSParam:function(QSData,paramName){
var strParam="";
var sIndex=QSData.toLowerCase().indexOf("&"+paramName+"=");
var lIndex=QSData.indexOf("&",sIndex+1);
strParam=(sIndex!=-1?QSData.substring(sIndex+paramName.length+2,lIndex):"");
return strParam;
},
getQSParamsMulti:function(QSData,paramName){
var arrDataParam=new Array();
var eachData="";
var lIndex=0;
do{
var sIndex=QSData.toLowerCase().indexOf("&"+paramName+"=",lIndex);
var lIndex=QSData.indexOf("&",sIndex+1);
if(sIndex!=-1){
eachData=QSData.substring(sIndex+paramName.length+2,lIndex);
eachData=eachData.replace(/^\[/,"");
eachData=eachData.replace(/\]$/,"");
arrDataParam.push(eachData);
}
}while(sIndex!=-1);
return arrDataParam;
},
getSep:function(rawData){
var sep=this.getQSParam(rawData,"sep");
if(sep==null||sep==""||typeof(sep)=="undefined")
sep=";";
return sep;
},
parseVlines:function(rawData){
var vlines=this.getQSParam(rawData,"vlines");
var boolVlines=new Array();
if(vlines!=""&&vlines!=null){
var arrVlines=vlines.split(this.separator);
for(var i in arrVlines)
boolVlines.push(parseInt(arrVlines[i])==1);
}
this.FC.vlines=boolVlines;
},
parseVersion:function(rawData){
var ver=this.getQSParam(rawData,"v").toLowerCase();
var found=false;
for(var i in this.FC.verTags){
if(this.FC.verTags[i].ver==ver){
this.FC.verTagIndex=i;
found=true;
break;
}
}
if(!found){
ver="3";
}
this.FC.version=ver;
},
parseNumDS:function(rawData){
var numDS=-1;
var arrChartData=this.getQSParamsMulti(rawData,"data");
numDS=arrChartData.length;
if(this.getAttribFromQSValue(rawData,"chart","chartType").search(/scatter|bubble/i)>=0)
numDS=this.getQSParamsMulti(rawData,"dataparams").length;
this.FC.numDS=numDS;
if(this.FC.numDS==0)
this.setDebugStr("The chart couldn't find any data-set to plot.","ERROR");
},
validateData:function(data){
	
if(data!=""){
if(data.search(new RegExp("[^-^0-9^,^.^\\"+this.separator+"]","g"))>=0){
this.setDebugStr("DATA VALIDATION "+data+" : Data Value has unwanted characters. Removing them.","Warning");
data=data.replace(new RegExp("[^-^0-9^,^.^\\"+this.separator+"]","g"),"");
}	
}
return data;
},
parseChartData:function(rawData){
var arrChartData=this.getQSParamsMulti(rawData,"data");
if(this.FC.numDS>0){
for(var i in arrChartData){
arrChartData[i]=arrChartData[i].replace(/\%20/g," ");
var splitData=arrChartData[i].split("]");
if(splitData.length>1 || this.FC.specifications.dataType=="SS")
	splitData[splitData.length-1]=this.validateData(this.trim(splitData[splitData.length-1]));
var arrDataValues=new Array();
var arrData=splitData[splitData.length-1].split(this.separator);
if(!(arrData.length==1&&arrData[0]=="")){
for(var i=0;i<arrData.length;i++){
arrDataValues.push({value:(isNaN(parseFloat(arrData[i]))?(""):(arrData[i])),params:""});
}
}
var params=(splitData[0].search(/\=/g)>=0?splitData[0]:"");
this.FC.numD=Math.max(this.FC.numD,arrDataValues.length);
var data={params:params,data:arrDataValues};
this.FC.dataset.push(data);
}
}
},
parseXYZChartData:function(rawData,isZData){
var dataset=new Array();
var data=new Object();
var splitData=new Array();
var params="";
var arrDataValues=new Array();
var arrChartData=this.getQSParamsMulti(rawData,"data");
for(var i in arrChartData){
arrChartData[i]=arrChartData[i].replace(/\%20/g," ");
splitData=arrChartData[i].split(/\]\s*\[/);
arrDataValues=new Array();
for(var j=0;j<splitData.length;j++){
splitData[j]=splitData[j].replace(/^\]+/,"");
splitData[j]=splitData[j].replace(/\]$/,"");
arrDataValues.push({value:"",params:splitData[j]});
}
this.FC.numD=Math.max(this.FC.numD,arrDataValues.length);
data={params:params,data:arrDataValues};
this.FC.dataset.push(data);
}
},
parseToolText:function(rawData){
var arrToolText=this.getQSParamsMulti(rawData,"tooltext");
var arrEachTT;
for(var i=0;i<Math.min(this.FC.dataset.length,arrToolText.length);i++){
arrEachTT=arrToolText[i].split(this.separator);
for(var j=0;j<Math.min(this.FC.dataset[i].data.length,arrEachTT.length);j++){
if(arrEachTT[j]!=""&&arrEachTT[j]!=null)
this.FC.dataset[i].data[j].params+=this.separator+"toolText="+arrEachTT[j];
}
}
},
getAttribFromQSValue:function(rawData,StrQSValue,AttribName){
var val="";
var pos,epos;
if(this.trim(rawData)!="")StrQSValue=this.getQSParam(rawData,StrQSValue);
pos=StrQSValue.search(new RegExp(AttribName+"\s*=","i"));
if(pos>=0){
pos=StrQSValue.indexOf("=",pos+1);
epos=StrQSValue.indexOf(this.separator,pos+1);
if(epos<0)epos=StrQSValue.length;
val=StrQSValue.substring(pos+1,epos);
}
return val;
},
parseChartConfig:function(rawData){
var strCConfig=this.getQSParam(rawData,"chart");
strCConfig=strCConfig.replace(/\%20/g,"");
strCConfig=strCConfig.replace(/\s/g,"");
strCConfig=strCConfig.replace(/\]|\[/g,"");
if(this.trim(strCConfig)=="")
this.setDebugStr("You have not specified chart name, chart width and chart height. Taking column 3D as default chart, chart width as 600 and height as 300.","Error");
var chartType="column3d";
var chartWidth="600";
var chartHeight="300";
var ct=this.getAttribFromQSValue(rawData,"chart","chartType").toLowerCase();
if(this.trim(ct)!="")chartType=ct; else  this.setDebugStr("You have not specified chart type. Setting the chart type as Column3D","Error");
var cw=parseInt(this.getAttribFromQSValue(rawData,"chart","chartWidth").replace(/[^0-9]/g,""));
if(!isNaN(cw))chartWidth=cw;
if(chartWidth<=150||isNaN(chartWidth)){
this.setDebugStr("The chart width ("+chartWidth+") you have specified is very small. Re-setting to 600.","Error");
chartWidth=600;
}
var ch=parseInt(this.getAttribFromQSValue(rawData,"chart","chartHeight").replace(/[^0-9]/g,""));
if(!isNaN(ch))chartHeight=ch;
if(chartHeight<=150||isNaN(chartHeight)){
this.setDebugStr("The chart height ("+chartHeight+") you have specified is very small. Re-setting to 300.","ERROR");
chartHeight=300;
}
var SWF=this.getChartSWF(chartType);
this.FC.specifications={type:SWF.friendlyName,SWF:SWF.fileName,width:chartWidth.toString(),height:chartHeight.toString(),dataType:SWF.dataType};
},
parseChartParams:function(rawData){
var strChartParams=this.getQSParam(rawData,"chartparams");
this.FC.params=this.buildAttribXML(strChartParams);

},
parseColors:function(rawData){
var strColors=this.getQSParam(rawData,"colors");
if(strColors!=""){
strColors=strColors.replace(/\%20/g,"");
strColors=strColors.replace(/\s/g,"");
strColors=strColors.replace(new RegExp("\\"+this.separator+"[\s]*\\"+this.separator,"g"),this.separator);
strColors=strColors.replace(new RegExp("^["+this.separator+"\\s]+"),"");
strColors=strColors.replace(new RegExp("["+this.separator+"\\s]+$"),"");
strColors=strColors.replace(new RegExp("\\"+this.separator,"g"),",");
this.FC.colors.addColors(strColors);
}
},
parseChartLabels:function(rawData){
var strLabels=this.getQSParam(rawData,"labels");
var strShowLabels=this.getQSParam(rawData,"showlabels");
this.FC.showLabels=(strShowLabels!="");
var numL=0;
var labels=new Array();
if(strLabels!=""){
strLabels=strLabels.replace(/\%20/g," ");
strShowLabels=strShowLabels.replace(new RegExp("[^0-1^\\"+this.separator+"]","g"),"");
var arrLabels=strLabels.split(this.separator);
numL=arrLabels.length;
var arrShowLabels=strShowLabels.split(this.separator);
for(var i in arrLabels){
var params="";
arrLabels[i]=arrLabels[i].replace(/'/ig,'%26apos;');
arrLabels[i]=arrLabels[i].replace(/"/ig,'&quot;');
if(arrShowLabels[i]==null)
arrShowLabels.push("1");
if(parseInt(arrShowLabels[i])==NaN)
arrShowLabels[i]="1";
labels.push({value:arrLabels[i],showLabel:arrShowLabels[i],color:"",params:params});
}
}
this.FC.numLabels=numL;
this.FC.labels=labels;
},
parseCatAttribs:function(rawData){
var strCatAttribs=this.getQSParam(rawData,"labelparams");
var arrCatAttribs=strCatAttribs.split(/\]\s*\[/);
var loopLimit=(this.FC.labels.length>0?Math.min(this.FC.labels.length,arrCatAttribs.length):arrCatAttribs.length);
for(var i=0;i<loopLimit;i++){
if(this.FC.labels.length<i+1)
this.FC.labels.push({value:"",params:""});
this.FC.labels[i].params+=";"+arrCatAttribs[i];
}
},
parseDataAttribs:function(rawData){
var arrDataAtribs=this.getQSParamsMulti(rawData,"dataparams");
var datasetLoop=arrDataAtribs.length;
var arrDatavaluesAttribs=new Array();
for(var j=0;j<datasetLoop;j++){
arrDataValuesAttribs=arrDataAtribs[j].split(/\]\s*\[/);
if(!this.FC.dataset[j])
this.FC.dataset.push({params:"",data:new Array()});
var loopLimit=arrDataValuesAttribs.length;
if(this.FC.specifications.dataType.search(/XY/i)>=0){if(this.FC.numD<loopLimit)this.FC.numD=loopLimit;}
for(var i=0;i<loopLimit;i++){
if(!this.FC.dataset[j].data[i])
this.FC.dataset[j].data.push({value:"",params:""});
this.FC.dataset[j].data[i].params+=";"+arrDataValuesAttribs[i];
}
}
},
parseSliced:function(rawData){
strSliced=this.getQSParam(rawData,"sliced");
if(strSliced!=""&&strSliced!=null){
strSliced=strSliced.replace(/\%20/g,"");
strSliced=strSliced.replace(/\s*/g,"");
strSliced=strSliced.replace(new RegExp("[^0-1^\\"+this.separator+"]","g"),"");
var arrSliced=strSliced.split(this.separator);
for(var i=0;i<Math.min(this.FC.dataset[0].data.length,arrSliced.length);i++){
if(arrSliced[i]!="0"&&arrSliced[i]!=null)
this.FC.dataset[0].data[i].params+=this.separator+"isSliced=1";
}
}
},
parseTrendlines:function(rawData){
var strTrendlines=this.getQSParam(rawData,"trendlines");
var arrTrendlines=strTrendlines.split(/\]\s*\[/);
var arrTLines=new Array();
for(var i in arrTrendlines){
if(this.buildAttribXML(arrTrendlines[i])!="")
arrTLines.push(this.buildAttribXML(arrTrendlines[i]));
}
this.FC.trendlines=arrTLines;
},
parseDebugMode:function(rawData){
var debug=this.getQSParam(rawData,"debugmode");
this.FC.debugMode=(isNaN(parseInt(debug))||parseInt(debug)!=1)?0:1;
if(this.FC.debugMode==1&&this.FC.errCount>0)
alert(this.FC.StrErr);
},
getChartSWF:function(strChartName){
var ct=new Object();
if(this.FC.numDS==0){
ct={ver:"3",fileName:"column3d.swf",dataType:"NULL",friendlyName:strChartName};
return ct;
}
var chartList=new Array();
chartList.push({ver:"3",SWF:"LogMSColumn2D.swf",chartFriendlyName:"logcolumn2d",dataType:"MS",rule:"this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"LogMSLine.swf",chartFriendlyName:"logline2d",dataType:"MS",rule:"this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"MSBar3D.swf",chartFriendlyName:"bar3d",dataType:"MS",rule:"this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"StackedBar3D.swf",chartFriendlyName:"stackedbar3d",dataType:"MS",rule:"this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Radar.swf",chartFriendlyName:"radar",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Funnel.swf",chartFriendlyName:"funnel",dataType:"SS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Pyramid.swf",chartFriendlyName:"pyramid",dataType:"SS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Spline.swf",chartFriendlyName:"spline2d",dataType:"SS",rule:" this.FC.numDS==1 "});
chartList.push({ver:"3",SWF:"MSSpline.swf",chartFriendlyName:"spline2d",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"SplineArea.swf",chartFriendlyName:"splinearea2d",dataType:"SS",rule:" this.FC.numDS==1 "});
chartList.push({ver:"3",SWF:"MSSplineArea.swf",chartFriendlyName:"splinearea2d",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"InverseMSArea.swf",chartFriendlyName:"inversearea2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"InverseMSColumn2D.swf",chartFriendlyName:"inversecolumn2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"InverseMSLine.swf",chartFriendlyName:"inverseline2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Waterfall2D.swf",chartFriendlyName:"waterfall",dataType:"SS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Scatter.swf",chartFriendlyName:"scatter",dataType:"XY",rule:"true"});
chartList.push({ver:"3",SWF:"Bubble.swf",chartFriendlyName:"bubble",dataType:"XYZ",rule:"true"});
chartList.push({ver:"3",SWF:"Column3D.swf",chartFriendlyName:"column3d",dataType:"SS",rule:" this.FC.numDS==1 "});
chartList.push({ver:"3",SWF:"Column2D.swf",chartFriendlyName:"column2d",dataType:"SS",rule:" this.FC.numDS==1 "});
chartList.push({ver:"3",SWF:"MSColumn3D.swf",chartFriendlyName:"column3d",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"MSColumn2D.swf",chartFriendlyName:"column2d",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"StackedColumn3D.swf",chartFriendlyName:"stackedcolumn3d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"StackedColumn2D.swf",chartFriendlyName:"stackedcolumn2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Pie2D.swf",chartFriendlyName:"pie2d",dataType:"SS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Pie3D.swf",chartFriendlyName:"pie3d",dataType:"SS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Doughnut2D.swf",chartFriendlyName:"doughnut2d",dataType:"SS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Doughnut3D.swf",chartFriendlyName:"doughnut3d",dataType:"SS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Doughnut2D.swf",chartFriendlyName:"donut2d",dataType:"SS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Doughnut3D.swf",chartFriendlyName:"donut3d",dataType:"SS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Line.swf",chartFriendlyName:"line2d",dataType:"SS",rule:" this.FC.numDS==1 "});
chartList.push({ver:"3",SWF:"MSLine.swf",chartFriendlyName:"line2d",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"Bar2D.swf",chartFriendlyName:"bar2d",dataType:"SS",rule:" this.FC.numDS==1 "});
chartList.push({ver:"3",SWF:"MSBar2D.swf",chartFriendlyName:"bar2d",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"StackedBar2D.swf",chartFriendlyName:"stackedbar2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"Area2D.swf",chartFriendlyName:"area2d",dataType:"SS",rule:" this.FC.numDS==1 "});
chartList.push({ver:"3",SWF:"MSArea.swf",chartFriendlyName:"area2d",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"StackedArea2D.swf",chartFriendlyName:"stackedarea2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"MSCombi2D.swf",chartFriendlyName:"combi2d",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"MSCombiDY2D.swf",chartFriendlyName:"combi2ddy",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"StackedColumn3DLineDY.swf",chartFriendlyName:"stackedcolumn3dlinedy",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"MSColumn3DLineDY.swf",chartFriendlyName:"column3dlinedy",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"MSColumnLine3D.swf",chartFriendlyName:"column3dline",dataType:"MS",rule:" this.FC.numDS>1 "});
chartList.push({ver:"3",SWF:"ScrollArea2D.swf",chartFriendlyName:"scrollarea2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"ScrollColumn2D.swf",chartFriendlyName:"scrollcolumn2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"ScrollLine2D.swf",chartFriendlyName:"scrollline2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"ScrollCombi2D.swf",chartFriendlyName:"scrollcombi2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"ScrollCombiDY2D.swf",chartFriendlyName:"scrollcombidy2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
chartList.push({ver:"3",SWF:"ScrollStackedColumn2D.swf",chartFriendlyName:"scrollstackedcolumn2d",dataType:"MS",rule:" this.FC.numDS>=1 "});
var searchResult=new Array();
for(var i in chartList){
if(strChartName.toLowerCase()==chartList[i].chartFriendlyName)
searchResult.push(i);
}
if(searchResult.length==0){
this.setDebugStr("You have specified an invalid chart name. Please refer to our documentation at www.fusioncharts.com/FileMaker to see possible list of chart types.","Error");
for(var i in chartList){
if(chartList[i].chartFriendlyName=="column3d"&&this.FC.version.toLowerCase()==chartList[i].ver)
searchResult.push(i);
}
}
for(var i in searchResult){
if(eval(chartList[searchResult[i]].rule)){
ct={fileName:chartList[searchResult[i]].SWF,dataType:chartList[searchResult[i]].dataType,friendlyName:chartList[searchResult[i]].chartFriendlyName};
break;
}
}
if(typeof(ct.fileName)=="undefined"){
this.setDebugStr("You have specified an invalid chart name. Please refer to our documentation at www.fusioncharts.com/FileMaker to see possible list of chart types.","ERROR");
for(var i in chartList){
if(chartList[i].chartFriendlyName==strChartName.toLowerCase()&&this.FC.version.toLowerCase()==chartList[i].ver){
ct={fileName:chartList[i].SWF,dataType:chartList[i].dataType,friendlyName:chartList[i].chartFriendlyName};
break;
}
}
}
return ct;
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
return params;
},
buildSSXML:function(){
this.FC.numD=this.FC.dataset[0].data.length;
var loopLimit=(this.FC.numLabels>0)?Math.max(this.FC.numLabels,this.FC.numD):this.FC.numD;
if(this.FC.numLabels==0)
this.setDebugStr("You have not specified category labels. Hence, the data values will show as blank data.","Warning");
else if(this.FC.numLabels>this.FC.numD)
this.setDebugStr("You have specified a greater number of category labels than data values. Hence, the extra data values will show as blank data.","Warning");
else if(this.FC.numLabels<this.FC.numD)
this.setDebugStr("You have specified a greater number of data values than category labels. Hence, the extra data values will not bear a label.","Warning");
var label=showLabel=value=color=valueParams="";
var XML="";
this.FC.colors.resetIndex();
for(var i=0;i<loopLimit;i++){
label=(this.FC.numLabels==0||i>=this.FC.labels.length)?"":this.FC.labels[i].value;
catParams=(this.FC.numLabels==0||i>=this.FC.labels.length)?"":this.FC.labels[i].params;
showLabel=(this.FC.showLabels==false||i>=this.FC.numLabels)?"":this.FC.verTags[this.FC.verTagIndex].showLabel+"='"+this.FC.labels[i].showLabel+"' ";
value=(this.FC.dataset[0].data.length<=0||i>=this.FC.dataset[0].data.length)?"":this.FC.dataset[0].data[i].value;
valueParams=this.buildAttribXML(catParams+";"+(this.FC.dataset[0].data.length<=0||i>=this.FC.dataset[0].data.length)?"":this.FC.dataset[0].data[i].params);
if(this.FC.colors==true)
color=" color='"+this.FC.colors.nextColor()+"' ";
if(i==0&&(this.FC.specifications.type.search(/line/i)>=0)&&this.FC.specifications.dataType=="SS")
this.FC.params+=" lineColor='"+color+"' ";
if(i==0&&(this.FC.specifications.type.search(/area/i)>=0)&&this.FC.specifications.dataType=="SS")
this.FC.params+=" areaBgColor='"+color+"' ";
XML+="<set "+this.FC.verTags[this.FC.verTagIndex].label+"='"+label+"' "+showLabel;
XML+="value='"+value+"'"+valueParams;
XML+=(this.FC.specifications.type.search(/area|line/i)>=0&&this.FC.colors!=true)?"":color;
XML+=" />";
if(this.FC.vlines.length!=0){
XML+=(typeof this.FC.vlines[i]!="undefined"&&this.FC.vlines[i]==true)?"<vline/>":"";
}
}
XML+=(this.FC.trendlines.length!=0?this.buildTrendlinesXML():"");
if((typeof this.FC.styleApp!="undefined")&&(typeof this.FC.styleDef!="undefined"))
XML+="<styles>"+this.getFV(this.FC.styleDef)+this.getFV(this.FC.styleApp)+"</styles>";
var final_XML="<"+this.FC.verTags[this.FC.verTagIndex].chart+this.FC.params+">"+XML+"</"+this.FC.verTags[this.FC.verTagIndex].chart+">";
return final_XML;
},
buildCategoriesXML:function(){
if(typeof this.FC.labels=="undefined")
this.FC.labels=new Array();
var loopLimit=(this.FC.numLabels>0)?this.FC.numLabels:this.FC.numD;
var XML=label=showLabel=color=catParams="";
XML+="<categories>";
for(var i=0;i<loopLimit;i++){
label=(this.FC.labels[i])?this.FC.labels[i].value:"";
showLabel=(this.FC.showLabels==false||i>=this.FC.numLabels)?"":this.FC.verTags[this.FC.verTagIndex].showLabel+"='"+this.FC.labels[i].showLabel+"' ";
catParams=(this.FC.labels[i])?this.FC.labels[i].params:"";
XML+="<category "+this.FC.verTags[this.FC.verTagIndex].label+"='"+label+"' "+showLabel+this.buildAttribXML(catParams)+"/>";
if(this.FC.vlines.length!=0){
XML+=(typeof this.FC.vlines[i]!="undefined"&&this.FC.vlines[i]==true)?"<vline/>":"";
}
}
XML+=" </categories>";
return XML;
},
buildMSXML:function(){
if(this.FC.numLabels==0)
this.setDebugStr("You have not specified category labels. Hence, the data values will show as blank data.","Warning");
else if(this.FC.numLabels>this.FC.numD)
this.setDebugStr("You have specified a greater number of labels than data values. Hence, the extra data values will be shown as blank values.","Warning");
else if(this.FC.numLabels<this.FC.numD)
this.setDebugStr("You have specified a greater number of data values than labels. Multi-series charts cannot show data without category labels. Hence, the chart wouldn't display extra data values and truncate the same.","Warning");
this.FC.colors.resetIndex();
var color=value=valueParams="";
var XML="<"+this.FC.verTags[this.FC.verTagIndex].chart+this.FC.params+">";
XML+=this.buildCategoriesXML();
var loopLimit=(this.FC.numLabels>0)?this.FC.numLabels:this.FC.numD;
for(var j=0;j<this.FC.numDS;j++){
if(this.FC.colors==true)
color=" color='"+this.FC.colors.nextColor()+"' ";
XML+="<dataset "+this.buildAttribXML(this.FC.dataset[j].params)+color+">";
for(var i=0;i<loopLimit;i++){
value="value='"+((this.FC.dataset[j].data.length<=0||i>=this.FC.dataset[j].data.length)?"":this.FC.dataset[j].data[i].value)+"' ";
valueParams=(this.FC.dataset[j].data.length<=0||i>=this.FC.dataset[j].data.length)?"":this.buildAttribXML(this.FC.dataset[j].data[i].params);
XML+=((this.FC.specifications.dataType.search(/XY/i)>=0&&this.trim(valueParams)=="")?"":"<set "+value+" "+valueParams+" />");
}
XML+="</dataset>";
}
XML+=(this.FC.trendlines.length!=0?this.buildTrendlinesXML():"");
if((typeof this.FC.styleApp!="undefined")&&(typeof this.FC.styleDef!="undefined"))
XML+="<styles>"+this.getFV(this.FC.styleDef)+this.getFV(this.FC.styleApp)+"</styles>";
XML+="</"+this.FC.verTags[this.FC.verTagIndex].chart+">";
return XML;
},
buildXYZXML:function(){
this.FC.numLabels=Math.max(this.FC.numLabels,this.FC.numD);
var XML=this.buildMSXML();
XML=XML.replace(/ value='' /g,"");
var rootClose="</"+this.FC.verTags[this.FC.verTagIndex].chart+">";
XML=XML.replace(rootClose,this.FC.vTrendLines+rootClose);
return XML;
},
buildVTrendLinesXML:function(rawData){
var XML="";
var vTrLines=this.getQSParam(rawData,"vtrendlines");
if(vTrLines.length!=0){
var arrVTrendlines=vTrLines.split(/\]\s*\[/);
XML+="<vTrendlines>";
for(var i in arrVTrendlines){
if(this.buildAttribXML(arrVTrendlines[i])!="")
XML+="<line "+this.buildAttribXML(arrVTrendlines[i])+" />";
}
XML+="</vTrendlines>";
}
this.FC.vTrendLines=XML;
},
buildTrendlinesXML:function(){
var XML="";
XML+="<trendlines>";
for(var i in this.FC.trendlines)
XML+="<line"+this.FC.trendlines[i]+" />";
XML+="</trendlines>";
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
this.FC.styleDef=strStyles;
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
this.FC.styleApp=strStylesA;
},
trim:function(str){
str=this.getFV(str);
if(str!=""){
str=str.replace(/^\s+/,"");
str=str.replace(/\s+$/,"");
}
return str;
},
getFV:function(v){
var bv="";
if(v==null||v==""||typeof v=="undefined")
return bv;
else
return v;
},
getSWF:function(){return this.FC.specifications.SWF;},
getWidth:function(){return this.FC.specifications.width;},
getHeight:function(){return this.FC.specifications.height;},
getXML:function(cXML){
var dXML=this.FC.dataXML;
var rootClose="</"+this.FC.verTags[this.FC.verTagIndex].chart+">";
if(cXML!=""||cXML!=null)
{
dXML=dXML.replace(rootClose,this.getFV(cXML)+rootClose);
}
return dXML;
},
version:function(){return this.FC.version;},
getDebugMode:function(){return this.FC.debugMode.toString();},
getChartMSG:function(){return"?"+this.FC.msg;},
setDebugStr:function(str,etype){
this.FC.errCount++;
this.FC.StrErr=this.FC.StrErr+(etype+" "+this.FC.errCount+" : "+str+"\n\n");
}
}
infosoftglobal.FusionChartsUtil.chartData=function(){
this.version="3";
this.verTags=new Array();
this.verTags.push({ver:"free",chart:"graph",label:"name",showlabels:"showNames",showLabel:"showName"});
this.verTags.push({ver:"3",chart:"chart",label:"label",showlabels:"showLabels",showLabel:"showLabel"});
this.verTagIndex=1;
this.numDS=0;
this.numD=0;
this.numLabels=0;
this.showLabels=false;
this.specifications=new Object();
this.params="";
this.labels=new Array();
this.dataset=new Array();
this.vlines=new Array();
this.trendlines=new Array();
this.styleDef="";
this.styleApp="";
this.DataXML="";
this.colors=new FCColors();
this.debugMode=0;
this.StrErr="";
this.errCount=0;
this.msg="ChartNoDataText=Please Provide Chart Data";
}
/* Aliases for easy usage */
var FusionChartsCreator=infosoftglobal.FusionChartsCreator;
var ChartData=infosoftglobal.FusionChartsUtil.chartData;