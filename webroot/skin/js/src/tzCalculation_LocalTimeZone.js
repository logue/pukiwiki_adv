// White Papers
// Time Zone Calculation
// http://www.desisoftsystems.com/white-papers/timeZoneCalculation/
//
// Conclusion
// In the interest of promoting open standards, you are free to use 
// the source code in this white paper and the code available in this JavaScript source file:
//
// http://www.desisoftsystems.com/tzCalculation_LocalTimeZone.js
//
// to implement this solution to this world-wide problem.
//
function tzCalculation_LocalTimeZone(i,d){var j;var b;var f;var c;var h;var a;var e;var g;j=new Date();b=Date.UTC(j.getUTCFullYear(),j.getUTCMonth(),j.getUTCDate(),j.getUTCHours(),j.getUTCMinutes(),j.getUTCSeconds());f=Date.UTC(j.getFullYear(),j.getMonth(),j.getDate(),j.getHours(),j.getMinutes(),j.getSeconds());c=f-b;h=(c/1000)/60;if(0>h){a="-"}else{a="+"}e=h%60;if(e!=0){h-=e;if(0>e){e=Math.abs(e)}}if(0>h){h=Math.abs(h)}g=h/60;if(10>g){a=a+"0"}a=a+g;if(10>e){a=a+"0"}a=a+e;if(d){document.writeln(a)}document.cookie="timezone="+a+"; domain="+i;return a};