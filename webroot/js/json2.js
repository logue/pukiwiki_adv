/*
    http://www.JSON.org/json2.js
    2011-10-19

    Public Domain.

    NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.

    See http://www.JSON.org/js.html
*/
var JSON;JSON||(JSON={});
(function(){function k(a){return 10>a?"0"+a:a}function o(a){p.lastIndex=0;return p.test(a)?'"'+a.replace(p,function(a){var c=r[a];return"string"===typeof c?c:"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+a+'"'}function m(a,j){var c,d,h,n,g=e,f,b=j[a];b&&"object"===typeof b&&"function"===typeof b.toJSON&&(b=b.toJSON(a));"function"===typeof i&&(b=i.call(j,a,b));switch(typeof b){case "string":return o(b);case "number":return isFinite(b)?""+b:"null";case "boolean":case "null":return""+b;
case "object":if(!b)return"null";e+=l;f=[];if("[object Array]"===Object.prototype.toString.apply(b)){n=b.length;for(c=0;c<n;c+=1)f[c]=m(c,b)||"null";h=0===f.length?"[]":e?"[\n"+e+f.join(",\n"+e)+"\n"+g+"]":"["+f.join(",")+"]";e=g;return h}if(i&&"object"===typeof i){n=i.length;for(c=0;c<n;c+=1)"string"===typeof i[c]&&(d=i[c],(h=m(d,b))&&f.push(o(d)+(e?": ":":")+h))}else for(d in b)Object.prototype.hasOwnProperty.call(b,d)&&(h=m(d,b))&&f.push(o(d)+(e?": ":":")+h);h=0===f.length?"{}":e?"{\n"+e+f.join(",\n"+
e)+"\n"+g+"}":"{"+f.join(",")+"}";e=g;return h}}"function"!==typeof Date.prototype.toJSON&&(Date.prototype.toJSON=function(){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+k(this.getUTCMonth()+1)+"-"+k(this.getUTCDate())+"T"+k(this.getUTCHours())+":"+k(this.getUTCMinutes())+":"+k(this.getUTCSeconds())+"Z":null},String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(){return this.valueOf()});var q=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
p=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,e,l,r={"\u0008":"\\b","\t":"\\t","\n":"\\n","\u000c":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},i;"function"!==typeof JSON.stringify&&(JSON.stringify=function(a,j,c){var d;l=e="";if("number"===typeof c)for(d=0;d<c;d+=1)l+=" ";else"string"===typeof c&&(l=c);if((i=j)&&"function"!==typeof j&&("object"!==typeof j||"number"!==typeof j.length))throw Error("JSON.stringify");return m("",
{"":a})});"function"!==typeof JSON.parse&&(JSON.parse=function(a,e){function c(a,d){var g,f,b=a[d];if(b&&"object"===typeof b)for(g in b)Object.prototype.hasOwnProperty.call(b,g)&&(f=c(b,g),void 0!==f?b[g]=f:delete b[g]);return e.call(a,d,b)}var d,a=""+a;q.lastIndex=0;q.test(a)&&(a=a.replace(q,function(a){return"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)}));if(/^[\],:{}\s]*$/.test(a.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,
"]").replace(/(?:^|:|,)(?:\s*\[)+/g,"")))return d=eval("("+a+")"),"function"===typeof e?c({"":d},""):d;throw new SyntaxError("JSON.parse");})})();