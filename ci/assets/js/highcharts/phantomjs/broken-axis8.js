
(function(a){"object"===typeof module&&module.exports?(a["default"]=a,module.exports=a):"function"===typeof define&&define.amd?define("highcharts/modules/broken-axis",["highcharts"],function(h){a(h);a.Highcharts=h;return a}):a("undefined"!==typeof Highcharts?Highcharts:void 0)})(function(a){function h(a,g,h,m){a.hasOwnProperty(g)||(a[g]=m.apply(null,h))}a=a?a._modules:{};h(a,"modules/broken-axis.src.js",[a["parts/Globals.js"],a["parts/Utilities.js"]],function(a,g){var h=g.extend,m=g.isArray,q=g.pick;
g=a.addEvent;var w=a.find,r=a.fireEvent,n=a.Axis,t=a.Series,u=function(f,c){return w(c,function(c){return c.from<f&&f<c.to})};h(n.prototype,{isInBreak:function(f,c){var a=f.repeat||Infinity,e=f.from,b=f.to-f.from;c=c>=e?(c-e)%a:a-(e-c)%a;return f.inclusive?c<=b:c<b&&0!==c},isInAnyBreak:function(f,c){var a=this.options.breaks,e=a&&a.length,b;if(e){for(;e--;)if(this.isInBreak(a[e],f)){var d=!0;b||(b=q(a[e].showPoints,!this.isXAxis))}var p=d&&c?d&&!b:d}return p}});g(n,"afterInit",function(){"function"===
typeof this.setBreaks&&this.setBreaks(this.options.breaks,!1)});g(n,"afterSetTickPositions",function(){if(this.isBroken){var a=this.tickPositions,c=this.tickPositions.info,k=[],e;for(e=0;e<a.length;e++)this.isInAnyBreak(a[e])||k.push(a[e]);this.tickPositions=k;this.tickPositions.info=c}});g(n,"afterSetOptions",function(){this.isBroken&&(this.options.ordinal=!1)});n.prototype.setBreaks=function(a,c){function f(a){var d=a,c;for(c=0;c<b.breakArray.length;c++){var f=b.breakArray[c];if(f.to<=a)d-=f.len;
else if(f.from>=a)break;else if(b.isInBreak(f,a)){d-=a-f.from;break}}return d}function e(a){var d;for(d=0;d<b.breakArray.length;d++){var c=b.breakArray[d];if(c.from>=a)break;else c.to<a?a+=c.len:b.isInBreak(c,a)&&(a+=c.len)}return a}var b=this,d=m(a)&&!!a.length;b.isDirty=b.isBroken!==d;b.isBroken=d;b.options.breaks=b.userOptions.breaks=a;b.forceRedraw=!0;b.series.forEach(function(b){b.isDirty=!0});d||b.val2lin!==f||(delete b.val2lin,delete b.lin2val);d&&(b.userOptions.ordinal=!1,b.val2lin=f,b.lin2val=
e,b.setExtremes=function(b,a,c,d,f){if(this.isBroken){for(var e,v=this.options.breaks;e=u(b,v);)b=e.to;for(;e=u(a,v);)a=e.from;a<b&&(a=b)}n.prototype.setExtremes.call(this,b,a,c,d,f)},b.setAxisTranslation=function(a){n.prototype.setAxisTranslation.call(this,a);this.unitLength=null;if(this.isBroken){a=b.options.breaks;var c=[],d=[],f=0,e,k=b.userMin||b.min,g=b.userMax||b.max,h=q(b.pointRangePadding,0),p;a.forEach(function(a){e=a.repeat||Infinity;b.isInBreak(a,k)&&(k+=a.to%e-k%e);b.isInBreak(a,g)&&
(g-=g%e-a.from%e)});a.forEach(function(a){l=a.from;for(e=a.repeat||Infinity;l-e>k;)l-=e;for(;l<k;)l+=e;for(p=l;p<g;p+=e)c.push({value:p,move:"in"}),c.push({value:p+(a.to-a.from),move:"out",size:a.breakSize})});c.sort(function(a,b){return a.value===b.value?("in"===a.move?0:1)-("in"===b.move?0:1):a.value-b.value});var m=0;var l=k;c.forEach(function(a){m+="in"===a.move?1:-1;1===m&&"in"===a.move&&(l=a.value);0===m&&(d.push({from:l,to:a.value,len:a.value-l-(a.size||0)}),f+=a.value-l-(a.size||0))});b.breakArray=
d;b.unitLength=g-k-f+h;r(b,"afterBreaks");b.staticScale?b.transA=b.staticScale:b.unitLength&&(b.transA*=(g-b.min+h)/b.unitLength);h&&(b.minPixelPadding=b.transA*b.minPointOffset);b.min=k;b.max=g}});q(c,!0)&&this.chart.redraw()};g(t,"afterGeneratePoints",function(){var a=this.options.connectNulls,c=this.points,k=this.xAxis,e=this.yAxis;if(this.isDirty)for(var b=c.length;b--;){var d=c[b],g=!(null===d.y&&!1===a)&&(k&&k.isInAnyBreak(d.x,!0)||e&&e.isInAnyBreak(d.y,!0));d.visible=g?!1:!1!==d.options.visible}});
g(t,"afterRender",function(){this.drawBreaks(this.xAxis,["x"]);this.drawBreaks(this.yAxis,q(this.pointArrayMap,["y"]))});a.Series.prototype.drawBreaks=function(a,c){var f=this,e=f.points,b,d,g,h;a&&c.forEach(function(c){b=a.breakArray||[];d=a.isXAxis?a.min:q(f.options.threshold,a.min);e.forEach(function(e){h=q(e["stack"+c.toUpperCase()],e[c]);b.forEach(function(b){g=!1;if(d<b.from&&h>b.to||d>b.from&&h<b.from)g="pointBreak";else if(d<b.from&&h>b.from&&h<b.to||d>b.from&&h>b.to&&h<b.from)g="pointInBreak";
g&&r(a,g,{point:e,brk:b})})})})};a.Series.prototype.gappedPath=function(){var f=this.currentDataGrouping,c=f&&f.gapSize;f=this.options.gapSize;var g=this.points.slice(),e=g.length-1,b=this.yAxis,d;if(f&&0<e)for("value"!==this.options.gapUnit&&(f*=this.basePointRange),c&&c>f&&c>=this.basePointRange&&(f=c),d=void 0;e--;)d&&!1!==d.visible||(d=g[e+1]),c=g[e],!1!==d.visible&&!1!==c.visible&&(d.x-c.x>f&&(d=(c.x+d.x)/2,g.splice(e+1,0,{isNull:!0,x:d}),this.options.stacking&&(d=b.stacks[this.stackKey][d]=
new a.StackItem(b,b.options.stackLabels,!1,d,this.stack),d.total=0)),d=c);return this.getGraphPath(g)}});h(a,"masters/modules/broken-axis.src.js",[],function(){})});
//# sourceMappingURL=broken-axis.js.map