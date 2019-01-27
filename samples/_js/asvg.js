// AnimCJK 2016-2019 Copyright Francois Mizessyn - https://github.com/parsimonhi/animCJK
// LGPL license
// ----
// pitiful hack for pitiful browser that cannot animate svg properly using css
// designed to simulate path animation for animCJK svg only in various cases
// usage:
//    add somewhere in your web page <script src="asvg.js"></script>
//    each time you need to start svg animation, use the following javascript instruction:
//        asvg.run(<action>);
//    where <action> can be "one", "color" , "radical" ...
//    see switch(asvg.action) in asvg.anime() function to get a list of available actions
// ----
// if your browser knows how to animate svg properly, asvg.run(<action>) does nothing
// otherwise, it uses some javascript to simulate what has to be done
// ----
// you can add your own action if you need it:
//    suppose you want to add an action called "foo"
//    add your action in the switch(asvg.action) of asvg.anime() function as follow:
//        case "foo":asvg.animeFoo(t);break;
//    write your own asvg.animeFoo() and put in it what you want
//    see asvg.animeOne(), asvg.animeColor() and asvg.animeRadical() as samples
//    remember that your action has to be designed for pitiful browsers
//    and that's all!

var asvg={activated:-1,animStack:[],time:1000,action:"one"}

asvg.init=function()
{
	// detect if the browser started the animation
	// two cases: browser cannot animate svg (many?) or can animate poorly svg (edge?)
	var s,d,list,k,km,c;
	// clip-path attribute still exists here
	list=document.querySelectorAll("svg.acjk path[clip-path]");
	if (list&&(km=list.length))
	{
		s=window.getComputedStyle(list[0],null);
		d=parseInt(s.getPropertyValue("stroke-dashoffset"));
		if (d==3339)
		{
			// stroke-dashoffset is still 3339 => animation not started
			asvg.activated=1; // pitiful browser
		}
		else if (window.navigator.userAgent.indexOf("Edge")>-1)
		{
			// edge starts the animation, but displays nothing
			asvg.activated=1; // pitiful browser
		}
		else asvg.activated=0; // normal browser
	}
	// asvg.activated=1; // for testing only
};

asvg.isRadicalStroke=function(n,d)
{
	var c,nsi,nsj,pos;
	if (!d) return false;
	if (d.charAt(1)==".") return true;
	nsi=0;
	d=d.replace(/:/g,"");
	d=d.replace(/[^0-9.]+/g,"?");
	while ((pos=d.search(/[0-9]+/))>=1)
	{
		nsj=nsi+parseInt(d.substr(pos));
		c=d.charAt(pos-1);
		if ((c==".")&&(n>=nsi)&&(n<nsj)) return true;
		nsi=nsj;
		d=d.replace(/^[^0-9]*[0-9]+/,"");
	}
	return false;
};

asvg.getDelay=function(e)
{
	var a,m;
	// if the delay is already stored in the "data-d" attribute, just return it
	a=e.getAttributeNS(null,"data-d");
	if (a) return parseFloat(a);
	// otherwise return the "--d" css variable value
	a=e.getAttributeNS(null,"style");
	if (a&&(m=a.match(/--d:([^;]+);/))) return parseFloat(m[1]);
	// some browsers may remove "--d" from the "style" attribute (pitifulest browsers)
	// then get the stroke number from the id of the clip-path element as a workaround
	// remember: the clip-path attribut is removed after the first call to asvg.getDelay()
	a=e.getAttributeNS(null,"clip-path");
	if (a&&(m=a.match(/c([0-9]+)[^0-9]/))) return parseInt(m[1]);
	return -1; // error
};

asvg.getDuration=function(e)
{
	return asvg.time*0.8/1000;
}

asvg.setDuration=function(t)
{
	asvg.time=t*1000*1.25;
}

asvg.animeStroke=function(n)
{
	var list,k,km;
	list=document.querySelectorAll("svg.acjk path[class='median']");
	if (list&&(km=list.length)&&(n<km))
	{
		for (k=0;k<km;k++)
			if (k>n) list[k].style.fill="none";
			else list[k].style.fill="#000";
	}
};

asvg.animeOne=function(t)
{
	var list,k,km,computedDelay;
	list=document.querySelectorAll("svg.acjk path[class='median']");
	if (list&&(km=list.length))
		for (k=0;k<km;k++)
		{
			computedDelay=(asvg.getDelay(list[k])+t-1)*asvg.time;
			asvg.animStack[k]=setTimeout("asvg.animeStroke("+k+")",computedDelay);
		}
};

asvg.last=function()
{
	var list,k,km;
	list=document.querySelectorAll("svg.acjk path[class='median']");
	// remember that the last stroke could be split in several sub-strokes
	if (list&&(km=list.length)) for (k=0;k<km;k++) list[k].style.fill="#000";
	asvg.animStack=[];
}

asvg.animeColorStroke=function(n)
{
	var list,k,km,delayN;
	list=document.querySelectorAll("svg.acjk path[class='median']");
	if (list&&(km=list.length))
	{
		delayN=asvg.getDelay(list[n]);
		for (k=0;k<km;k++)
			if (k>n) list[k].style.fill="transparent";
			else if (asvg.getDelay(list[k])==delayN) list[k].style.fill="#c00";
			else list[k].style.fill="#000";
	}
};

asvg.animeColor=function(t)
{
	var list,k,km,computedDelay;
	list=document.querySelectorAll("svg.acjk path[class='median']");
	if (list&&(km=list.length))
	{
		for (k=0;k<km;k++)
		{
			computedDelay=(asvg.getDelay(list[k])+t-1)*asvg.time;
			asvg.animStack[k]=setTimeout("asvg.animeColorStroke("+k+")",computedDelay);
		}
		asvg.animStack[k]=setTimeout(asvg.last,computedDelay+asvg.time);
	}
};

asvg.animeRadicalStroke=function(n)
{
	// manage special decomposition when some strokes are split to show the radical
	// normal decomposition is in asvg.acjk, special decomposition is in asvg.acjks
	// see for instance 由, 甲, 申 ...
	var list,k,km;
	list=document.querySelectorAll("svg.acjk path[class='median']");
	if (list&&(km=list.length))
	{
		for (k=0;k<km;k++)
			if (k>n)
				list[k].style.fill="transparent";
			else if (asvg.isRadicalStroke(k,asvg.acjks?asvg.acjks:asvg.acjk))
				list[k].style.fill="#fa0";
			else list[k].style.fill="#00f";
	}
};

asvg.animeRadical=function(t)
{
	var list,k,km,computedDelay;
	list=document.querySelectorAll("svg.acjk path[class='median']");
	if (list&&(km=list.length))
	{
		for (k=0;k<km;k++)
		{
			computedDelay=(asvg.getDelay(list[k])+t-1)*asvg.time;
			asvg.animStack[k]=setTimeout("asvg.animeRadicalStroke("+k+")",computedDelay);
		}
	}
};

asvg.anime=function()
{
	var t,list1,list2,k,km,km1,km2;
	if (asvg.activated<0) {asvg.init();t=0;}
	else t=1;
	if (asvg.activated>0)
	{
		// reset stroke style
		list1=document.querySelectorAll("svg.acjk path[id]");
		if (list1&&(km1=list1.length))
		{
			// clip-path still exists the first time one comes here
			list2=document.querySelectorAll("svg.acjk path[clip-path]");
			if (list2&&(km2=list2.length)&&(km1==km2))
			{
				km=km2;
				for (k=0;k<km;k++)
				{
					list2[k].style.animation="none";
					list2[k].style.stroke="none";
					list2[k].style.fill="none";
					// svg element => uses class attribute instead of className attribute
					list2[k].setAttributeNS(null,"class","median");
					// store the delay in the data-d attribute
					// before removing the clip-path attribute
					list2[k].setAttributeNS(null,"data-d",asvg.getDelay(list2[k])+'');
					list2[k].removeAttribute("clip-path");
					list2[k].removeAttribute("pathLength");
					list2[k].setAttributeNS(null,"data-median",list2[k].getAttributeNS(null,"d"));
					list2[k].setAttributeNS(null,"d",list1[k].getAttributeNS(null,"d"));
				}
			}
			else
			{
				list2=document.querySelectorAll("svg.acjk path[class='median']");
				if (list2&&(km2=list2.length)&&(km1==km2)) km=km2;
				else km=0;
				for (k=0;k<km;k++) list2[k].style.fill="none";
			}
		}
		switch(asvg.action)
		{
			case "color":asvg.animeColor(t);break;
			case "radical":asvg.animeRadical(t);break;
			default:asvg.animeOne(t); // "one" action or equivalent
		}
	}
};

asvg.speed=function(t)
{
	asvg.setDuration(t*0.8);
};

asvg.start=function()
{
	var list,k,km;
	km=asvg.animStack.length;
	for (k=0;k<km;k++) clearTimeout(asvg.animStack[k]);
	asvg.animStack=[];
	asvg.anime();
};

asvg.run=function(a)
{
	if (a) asvg.action=a;
	if (asvg.activated<0)
	{
		// detect if hack is necessary
		setTimeout(asvg.anime,asvg.time*1.1);
	}
	else if (asvg.activated>0) asvg.start();
};
