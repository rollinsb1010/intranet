infosoftglobal.FusionChartsUtil.FCColors=function(strColors){
this.userColor=false;
this.arr_FCColors=new Array();
if(strColors!=null)this.addColors(strColors);
this.totalColors=this.arr_FCColors.length;
this.FCColorIndex=-1;
}
infosoftglobal.FusionChartsUtil.FCColors.prototype={

nextColor:function(){
var strColor="";
this.FCColorIndex++;
strColor=this.arr_FCColors[this.FCColorIndex%this.totalColors];
return strColor;
},

addColors:function(strColors){
strColors=strColors.replace(/\s/ig,"");
strColors=strColors.replace(/[^a-f^0-9^\,]/ig,"");
var arrUserColors=strColors.split(",");
for(var i in arrUserColors)
this.arr_FCColors.push(arrUserColors[i]);

this.totalColors=this.arr_FCColors.length;
this.userColor=true;

},

resetIndex:function(){
this.FCColorIndex=-1;
},
toString:function(){
	return this.userColor;
}
}
var FCColors=infosoftglobal.FusionChartsUtil.FCColors;
