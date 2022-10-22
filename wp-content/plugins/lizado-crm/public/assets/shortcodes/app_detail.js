var _months2 = (typeof (_locModel) != "undefined" && typeof (_locModel.T.T_S_Jan) != "undefined" )? [_locModel.T.T_S_Jan, _locModel.T.T_S_Feb, _locModel.T.T_S_Mar, _locModel.T.T_S_Apr, _locModel.T.T_S_May, _locModel.T.T_S_Jun, _locModel.T.T_S_Jul, _locModel.T.T_S_Aug, _locModel.T.T_S_Sep, _locModel.T.T_S_Oct, _locModel.T.T_S_Nov, _locModel.T.T_S_Dec] :["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
	var _months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	var _weeks = (typeof (_locModel) != "undefined" && typeof (_locModel.T.T_Sunday) != "undefined") ? [_locModel.T.T_Sunday, _locModel.T.T_Monday, _locModel.T.T_Tuesday, _locModel.T.T_Wednesday, _locModel.T.T_Thursday, _locModel.T.T_Friday, _locModel.T.T_Saturday] : ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	var _weeks2 = (typeof (_locModel) != "undefined" && typeof (_locModel.T.T_S_Sunday) != "undefined") ? [_locModel.T.T_S_Sunday, _locModel.T.T_S_Monday, _locModel.T.T_S_Tuesday, _locModel.T.T_S_Wednesday, _locModel.T.T_S_Thursday, _locModel.T.T_S_Friday, _locModel.T.T_S_Saturday] : ["Sun.", "Mon.", "Tue.", "Wed.", "Thu.", "Fri.", "Sat."];

	function formatDate(t,type)
	{
	    var strTime="";
	    var t1 = t.split(",");
		var t2 = new Date(t1[0],t1[1],t1[2],t1[3],t1[4],t1[5]);
		t2 = new Date(Date.UTC(t2.getFullYear(),t2.getMonth(),t2.getDate(),t2.getHours(),t2.getMinutes(),t2.getSeconds())); 

		if(type==1)
		{  
	        //strTime = (t2.getDate() + "-" + (t2.getMonth() + 1) + "-" + t2.getFullYear() + " " + formatTime2(t2) + " " + _weeks[t2.getDay()]);
	        strTime = timeToText(t2,13);
	    }
		else if(type==2)
		{
	        //strTime = (t2.getDate() + "-" + (t2.getMonth() + 1) + "-" + t2.getFullYear() + " " + formatTime2(t2));
	        strTime = timeToText(t2, 18);
	    }
		else if (type == 3) {
	        //strTime = (t2.getDate() + "-" + (t2.getMonth() + 1) + "-" + t2.getFullYear());
	        strTime = timeToText(t2, 2);
		}
		else if (type == 4) {
	        //strTime = formatTime2(t2);
	        strTime = timeToText(t2, 4);
		}
		else {
	        //strTime = (t2.getDate() + "-" + (t2.getMonth() + 1) + "-" + t2.getFullYear());
	        strTime = timeToText(t2, 1);
		}
	    return strTime;
	}
	function dateFtt(fmt, t) {
	    var o = {
	        "M+": t.getMonth() + 1,//month   
	        "d+": t.getDate(),//day   
	        "h+": t.getHours(),//hours   
	        "m+": t.getMinutes(),//minutes
	        "s+": t.getSeconds(),//second
	        "t1": _months[t.getMonth()],//month name
	        "t2": _months2[t.getMonth()],//simp month
	        "w+": _weeks[t.getDay()]//week
	    };

	    if (/(y+)/.test(fmt))
	        fmt = fmt.replace(RegExp.$1, (t.getFullYear() + "").substr(4 - RegExp.$1.length));

	    for (var k in o)
	        if (new RegExp("(" + k + ")").test(fmt))
	            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1 || /[tw]/.test(k)) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));

	    return fmt;
	}
	function timeToText(t2, type) {
	    type = type || 0;
	    var fmts = [
	        "dd/MM/yyyy hh:mm:ss",/*0*/
	        "dd/MM/yyyy hh:mm:ss",/*1*/
	        "dd/MM/yyyy",/*2*/
	        "dd/MM",/*3*/
	        "hh:mm",/*4*/
	        "t2 dd",/*5*/
	        "dd/MM hh:mm",/*6*/
	        "dd/MM/yy",/*7*/
	        "yyyy",/*8*/
	        "dd/MM/yyyy",/*9*/
	        "t2 dd hh:mm",/*10*/
	        "hh:mm,ww,dd/MM/yyyy",/*11*/
	        "dd/MM/yyyy(w)",/*12*/
	        "dd/MM/yyyy hh:mm w",/*13*/
	        "dd/MM hh:mm",/*14*/
	        "dd/MM hh:mm",/*15*/
	        "dd/MM/yy",/*16*/
	        "dd/MM/yyyy",/*17*/
	        "dd/MM/yyyy hh:mm",/*18*/
	        "d/M/yyyy hh:mm",/*19*/
	    ];

	    return dateFtt(fmts[type] || fmts[0], t2);
	}