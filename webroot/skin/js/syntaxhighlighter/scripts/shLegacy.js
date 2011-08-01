/* 
 This file is part of SyntaxHighlighter.

 SyntaxHighlighter is free software: you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 SyntaxHighlighter is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with SyntaxHighlighter. If not, see <http://www.gnu.org/copyleft/lesser.html>.
*/
var dp={SyntaxHighlighter:{}}; dp.SyntaxHighlighter={parseParams:function(a,h,i,j,k,l){function d(c,a){return c!=null?c:a}function e(c){return c!=null?c.toString():null}var a=a.split(":"),f=a[0],b={},g={"true":"true"};reverse={"true":"false"};result=null;defaults=SyntaxHighlighter.defaults;for(var m in a)b[a[m]]="true";h=e(d(h,defaults.gutter));i=e(d(i,defaults.toolbar));j=e(d(j,defaults.collapse));l=e(d(l,defaults.ruler));k=e(d(k,defaults["first-line"]));return result={brush:f,gutter:d(reverse[b.nogutter],h),toolbar:d(reverse[b.nocontrols], i),collapse:d(g[b.collapse],j),ruler:d(g[b.showcolumns],l),"first-line":d(function(c,a){for(var d=new XRegExp("^"+a+"\\[(?<value>\\w+)\\]$","gi"),m=null,b=0;b<c.length;b++)if((m=d.exec(c[b]))!=null)return m.value;return null}(a,"firstline"),k)}},HighlightAll:function(a,h,i,j,k,l){function d(){for(var a=arguments,c=0;c<a.length;c++)if(a[c]!==null){if(typeof a[c]=="string"&&a[c]!="")return a[c]+"";if(typeof a[c]=="object"&&a[c].value!="")return a[c].value+""}return null}function e(a,c,b){for(var b= document.getElementsByTagName(b),d=0;d<b.length;d++)b[d].getAttribute("name")==c&&a.push(b[d])}var f=[];e(f,a,"pre");e(f,a,"textarea");if(f.length!==0)for(a=0;a<f.length;a++){var b=f[a],g=d(b.attributes["class"],b.className,b.attributes.language,b.language);g!==null&&(g=dp.SyntaxHighlighter.parseParams(g,h,i,j,k,l),SyntaxHighlighter.highlight(g,b))}}};