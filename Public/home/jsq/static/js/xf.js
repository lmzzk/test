;(function(){
var dt_rate = 0;//素材切换时间
var zl_width = 640;//素材宽
var zl_height = 200;//素材高

var adslist = [];
adslist[0] = [10,'','https://biz.tc4ever.com/eros-front/promoteChannel/midPage?uuid=3ba99c314b0f4f95bbda9f1831098d03'];
adslist[1] = [10,'','https://biz.tc4ever.com/eros-front/promoteChannel/midPage?uuid=3ba99c314b0f4f95bbda9f1831098d03'];
adslist[2] = [10,'','https://biz.tc4ever.com/eros-front/promoteChannel/midPage?uuid=3ba99c314b0f4f95bbda9f1831098d03'];
adslist[3] = [10,'','https://biz.tc4ever.com/eros-front/promoteChannel/midPage?uuid=3ba99c314b0f4f95bbda9f1831098d03'];

var zl_imglist = [
'https://www.ant2.cn/static/js/11.png',
'https://www.ant2.cn/static/js/33.png',
'https://www.ant2.cn/static/js/44.png',
'https://www.ant2.cn/static/js/55.png',
'https://www.ant2.cn/static/js/66.png',
'https://www.ant2.cn/static/js/22.png'//最后不能加,
];


var bd = bodyDimensions();
var w_rows=1;
var w_cols = 1;//图片张数
var w_ads_size = w_cols*w_rows;
var w_width = w_cols==1 ? 100 : parseInt(100/w_cols)-1;
var w_zone_height = bd.clientWidth*zl_height/zl_width;
var w_ads_height = w_zone_height;

var os = checkMobile();

function random(arr){
var w = 0;
for(var i=0;i<arr.length;i++){
w += arr[i][0];
}
var r = Math.floor(Math.random()*w);
var tw = 0;
for(var i=0;i<arr.length;i++){
tw += arr[i][0];
if(r<tw){
showZLLB(arr[i][2]);
break;
}
}
}

var __imglist = zl_imglist.sort(randomsort), __imglen = __imglist.length, __imgcurrent = 0;
function showZLLB(zlurl){
if(os==3) return false;
var id = 'z'+Math.random().toString(36).substring(2);
showBottomAds(id,zlurl);
setTimeout(function() {
render(id,zlurl);
var t = setInterval(function() {
render(id,zlurl);
}, 15000);
}, 200);
}
function render(id,zlurl) {
    var html = "";
    for (var i = 0; i < w_rows; i++) {
        html += '<ul style="margin:0px; padding:0px;overflow:hidden;width:100%;">';
        for (var j = 0; j < w_cols; j++) {
var imgurl = __imglist[__imgcurrent];
__imgcurrent++;if(__imgcurrent>=__imglen) __imgcurrent=0;
if(w_cols==1){
if(os==1 && navigator.userAgent.indexOf('UCBrowser') > -1){
html += '<li style="list-style:none; height:'+w_ads_height+'px;background-color:#FFFFFF;"><img src="' + imgurl + '"onclick="yrAClick_b(\'zlurl_'+id+'\',\''+zlurl+'\')" width="100%" height="' + (w_ads_height - 2) + '" style="border:0;" /><a target="_blank" id="zlurl_'+id+'"></a></li>';
}else{
html += '<li style="width:100%;list-style:none; height:'+w_ads_height+'px;background-color:#FFFFFF; overflow:hidden;text-align:center;"><img src="' + imgurl + '"onclick="yrAClick_b(\'zlurl_'+id+'\',\''+zlurl+'\')" width="100%" height="' + (w_ads_height - 2) + '" style="border:0;" /><a target="_blank" id="zlurl_'+id+'"></a></li>';
}
            }else{
            html += '<li style="width:49%;list-style:none;float: left; margin-right:2px; height:'+w_ads_height+'px;background:#FFFFFF; overflow:hidden;text-align:center;line-height:20px;"><img src="' + imgurl + '" onclick="yrAClick_b(\'zlurl_'+id+'\',\''+zlurl+'\')" width="99%" height="' + (w_ads_height - 2) + '" style="border:0;" /><a target="_blank" id="zlurl_'+id+'"></a></li>';
            }
        }
        html += "</ul>";
    }
document.getElementById(id).innerHTML=html;
}
window.yrAClick_b = function(id,url){
    var tagas = document.getElementById(id);
    tagas.href = url;
    tagas.click();
}
function bodyDimensions(win){win=win||window;var doc=win.document;var cw=doc.compatMode=="BackCompat"?doc.body.clientWidth:doc.documentElement.clientWidth;var ch=doc.compatMode=="BackCompat"?doc.body.clientHeight:doc.documentElement.clientHeight;var sl=Math.max(doc.documentElement.scrollLeft,doc.body.scrollLeft);var st=Math.max(doc.documentElement.scrollTop,doc.body.scrollTop);var sw=Math.max(doc.documentElement.scrollWidth,doc.body.scrollWidth);var sh=Math.max(doc.documentElement.scrollHeight,doc.body.scrollHeight);var w=Math.max(sw,cw);var h=Math.max(sh,ch);return {"clientWidth":cw,"clientHeight":ch,"scrollLeft":sl,"scrollTop":st,"scrollWidth":sw,"scrollHeight":sh,"width":w,"height":h}};
function randomsort(a, b) { return Math.random()>0.5 ? -1 : 1; }

function showBottomAds(id,zlurl){
//if(os==3) return false;
var zone_h = w_zone_height;
var div_id = id+'DV';
var div_holder ='HR';

var add_bottomholder = document.createElement('div');
var add_1 = document.createElement('div');
var add_2 = document.createElement('div');
var add_closediv = document.createElement('div');
var add_closediv_a = document.createElement('a');
add_bottomholder.id = div_holder;
add_bottomholder.style.cssText = 'width:auto;height:auto;border:0 none;';
add_1.id = div_id;
add_1.style.cssText = 'position: fixed; z-Index: 12777777;left:0px;bottom:0px;width:100%;height:'+zone_h+'px; ';
add_2.id = id;

add_closediv.style.cssText = 'position: absolute; width: 15px; height: 15px; z-Index: 1777777; top:0px; right: 0px;';
add_closediv_a.style.cssText = 'cursor:pointer;';
add_closediv_a.onclick = function(){ document.getElementById(div_id).style.display='none';document.getElementById(div_holder).style.display='none'; };
add_closediv_a.innerHTML = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAUCAYAAACJfM0wAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAfRJREFUeNpiZGBg0GSgAWBioBFgBmJRGCc7O1ucjY3t3+PHj/+QYsiRI0d8REREPh89evQLsrgmDD99+nT5z58/H3l7e5sii+PDW7duTfn169eT69ev96LJoSr88ePHHZDhZWVlToQMvX///uw/f/68//DhwyF0OYww5uDg8P3379/P5ubmhUDDJXF5//jx476SkpKuX758udzW1taATQ1W13z//v020OWPExMTbdHlbt682Q9y6adPn07i0o8zVYSEhEQBXf5j5syZy4GGi8DE9+7d6yIvLx8INPRcfn5+CS79jITS8bdv3zawsLBwBwIBMHi89fT08oHx8JCHhycSnz5GYjII0PB1TExMnMDYfwnEb0tLS9vnz5//huIMcvHixfmsrKwi3NzcmsCgmUTIUIwMgg2cPn06SF9fP+Xly5e7mJmZmezs7GKePHmy7cKFC98IGY4znQIjKvf3799vgUFxE5Y6vn79ehUYHM8nTpzoTyCdY5cAer8FGEn33717txddjkjDMQXnzJkTAnTpK1Ba1tTUNMSmBmj4ZZDhra2tHkQZfO7cuQZQxnj//v0BQmUG1PAXOLI/glNTU+MGUggqL4gIQzAGZukLoGSIbjgLciwCXfnrwYMHK9XU1GYQW2SCMsq9e/cKyMogg6oGAQgwAK8UInW+KGpJAAAAAElFTkSuQmCC" border="0" width="15" />';
add_closediv.appendChild(add_closediv_a);
add_1.appendChild(add_closediv);
add_1.appendChild(add_2);
if (document.compatMode == "BackCompat") {
var doc = document.body;
}else{ 
var doc = document.documentElement;
}
//document.getElementsByTagName("body")[0].setAttribute('style','padding-bottom:'+zone_h+'px;');
var doc = document.body;
doc.appendChild(add_bottomholder);
doc.appendChild(add_1);

var random = Math.ceil(Math.random()*100);
if(random<=dt_rate){
var add_a = document.createElement('a');
add_a.style.cssText = 'background:rgba(0,0,0,0);display:block;height:100%;width:100%;position:absolute;top:0px;z-Index:2147483647;';
add_a.href = zlurl;
add_a.onclick = function(){ add_a.style.display='none';}
add_a.target = '_blank';
doc.appendChild(add_a);
}
}
function checkMobile() {
var sUserAgent = navigator.userAgent.toLowerCase();
var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
var bIsMidp = sUserAgent.match(/midp/i) == "midp";
var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";
var bIsAndroid = sUserAgent.match(/android/i) == "android";
var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";
var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile" ;
if(bIsAndroid)
{
return 1;
}else if(bIsIphoneOs || bIsIpad){
return 2;
}else{
return 3;
}
}
function randomString(len) {
var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz';
var maxPos = $chars.length;
var pwd = '';
for (i = 0; i < len; i++) {
pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
}
return pwd;
}

random(adslist);

})()