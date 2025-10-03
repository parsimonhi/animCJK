// Create a lang selector in the tag having .langSelector as class if any.
// Create a char selector in the tag having .dataSelector as class if any.
// Create a char list selector in the tag having .charListSelector as class if any.
// A doIt() function should be defined in the calling page if a tag having .dataSelector
// as class is present.
// This function is executed:
//	- when the user changes the lang using the lang selector
//	- clicking on the "Run" button of the char selector
// A afterAddingChartSelector may be defined in the calling page to execute something
// just after adding the char list to the page
// A charListMap object may be set in the calling page to indicate
// what the char list selector will contain
(function()
{
	let langSelector=document.querySelector('.langSelector');
	let dataSelector=document.querySelector('.dataSelector');
	let charListSelector=document.querySelector('.charListSelector');
	let map;
	if(charListSelector)
	{
		if(typeof charListMap !== "undefined") map=JSON.stringify(charListMap);
		else
		{
			// default map (convenient for samples)
			let charListMap={};
			charListMap["Ja"]=["g1","g2","g3","g4","g5","g6","g7"];
			charListMap["Ko"]=["hanja8","hanja7","hanja6","hanja5","hanja4","hanja3","hanja2","hanja1"];
			charListMap["ZhHans"]=["hsk31","hsk32","hsk33","hsk34","hsk35","hsk36","hsk37","hsk38","hsk39"];
			charListMap["ZhHant"]=["t31","t32","t33","t34","t35","t36","t37","t38","t39"];
			map=JSON.stringify(charListMap);
		}
	}
	function makeTitle(s)
	{
		if(s=="hiragana") return "Hiragana";
		if(s=="katakana") return "Katakana";
		if(s=="g1") return "Grade 1";
		if(s=="g2") return "Grade 2";
		if(s=="g3") return "Grade 3";
		if(s=="g4") return "Grade 4";
		if(s=="g5") return "Grade 5";
		if(s=="g6") return "Grade 6";
		if(s=="g7") return "Junior high school";
		if(s=="g8") return "Jinmeiyō";
		if(s=="g9") return "Hyōgai";
		if(s=="gc") return "Components";
		if(s=="gs") return "Strokes";
		if(s=="hanja8") return "Hanja level 8";
		if(s=="hanja7") return "Hanja level 7";
		if(s=="hanja6") return "Hanja level 6";
		if(s=="hanja5") return "Hanja level 5";
		if(s=="hanja4") return "Hanja level 4";
		if(s=="hanja3") return "Hanja level 3";
		if(s=="hanja2") return "Hanja level 2";
		if(s=="hanja1") return "Hanja level 1";
		if(s=="hanja1800a") return "Hanja part 1";
		if(s=="hanja1800b") return "Hanja part 2";
		if(s=="ku") return "Uncommon hanja";
		if(s=="kc") return "Components";
		if(s=="hanguljamos") return "Jamo";
		if(s=="hangulsyllables") return "Hangul";
		if(s=="hsk31") return "HSK v3 level 1, simplified hanzi";
		if(s=="hsk32") return "HSK v3 level 2, simplified hanzi";
		if(s=="hsk33") return "HSK v3 level 3, simplified hanzi";
		if(s=="hsk34") return "HSK v3 level 4, simplified hanzi";
		if(s=="hsk35") return "HSK v3 level 5, simplified hanzi";
		if(s=="hsk36") return "HSK v3 level 6, simplified hanzi";
		if(s=="hsk37") return "HSK v3 level 7, simplified hanzi";
		if(s=="hsk38") return "HSK v3 level 8, simplified hanzi";
		if(s=="hsk39") return "HSK v3 level 9, simplified hanzi";
		if(s=="frequentNotHsk3") return "Other frequent hanzi";
		if(s=="commonNotHsk3NorFrequent") return "Other common hanzi";
		if(s=="frequent2500") return "2500 frequent hanzi";
		if(s=="lessFrequent1000") return "1000 less frequent hanzi";
		if(s=="commonNotFrequent") return "3500 other common hanzi";
		if(s=="common7000") return "7000 common hanzi";
		if(s=="traditional") return "Traditional hanzi used in simplified Chinese";
		if(s=="uncommon") return "Uncommon hanzi";
		if(s=="component") return "Components";
		if(s=="t31") return "HSK v3 level 1, traditional hanzi";
		if(s=="t32") return "HSK v3 level 2, traditional hanzi";
		if(s=="t33") return "HSK v3 level 3, traditional hanzi";
		if(s=="t34") return "HSK v3 level 4, traditional hanzi";
		if(s=="t35") return "HSK v3 level 5, traditional hanzi";
		if(s=="t36") return "HSK v3 level 6, traditional hanzi";
		if(s=="t37") return "HSK v3 level 7, traditional hanzi";
		if(s=="t38") return "HSK v3 level 8, traditional hanzi";
		if(s=="t39") return "HSK v3 level 9, traditional hanzi";
		if(s=="taiwan4808") return "Taiwan 4808 traditional hanzi";
		if(s=="t3NotTaiwan4808") return "HSK v3 traditional hanzi not in Taiwan 4808";
		if(s=="taiwan4808NotT3") return "Taiwan 4808 traditional hanzi not in HSK v3";
		if(s=="tu") return "Uncommon traditional hanzi";
		if(s=="tc") return "Components";
		if(s=="radicals") return "The 214 radicals";
		if(s=="stroke") return "Strokes";
		return s;
	}
	function makeLangIso(lang)
	{
		if(lang=="ZhHans") return "zh-Hans";
		if(lang=="ZhHant") return "zh-Hant";
		if(lang=="Ko") return "ko";
		return "ja";
	}
	function setLang(lang)
	{
		document.querySelector('html').setAttribute("lang",makeLangIso(lang));
		if(!!window.doIt) doIt();
	}
	function addOneLangRadio(p,value,label,langIso)
	{
		let s="",e,checked=(value=="Ja"?" checked":"");
		e=document.createElement('label');
		s+="<input type=\"radio\" name=\"lang\"";
		s+=" data-lang=\""+langIso+"\" value=\""+value+"\" "+checked+">";
		s+=label;
		e.innerHTML=s;
		p.appendChild(e);
		e=p.querySelector('[value="'+value+'"]');
		e.addEventListener('click',function(){setLang(value);});
	}
	function addLangSelector(p)
	{
		addOneLangRadio(p,"Ja","Japanese","ja");
		addOneLangRadio(p,"Ko","Korean","ko");
		addOneLangRadio(p,"ZhHans","Simplified Chinese","zh-Hans");
		addOneLangRadio(p,"ZhHant","Traditional Chinese","zh-Hant");
	}
	function addCharSelector(p)
	{
		let s="",e;
		s+="<label>Kanji, Hanja or Hanzi (one char only)<input name=\"data\"></label>";
		s+="<button type=\"button\" name=\"run\">Run</button>";
		p.innerHTML=s;
		e=p.querySelector('[name="run"]');
		e.addEventListener('click',(!!window.doIt)?doIt:function(){alert("No doIt?")});
	}
	function click(ev)
	{
		let b=ev.target;
		let c=b.innerHTML;
		let d=document.querySelector('[name="data"]');
		let o=document.querySelector('[name="run"]');
		if(d) d.value=c+d.value; // able to deal several characters
		if(o) o.focus(); // otherwise mobiles may show keyboard
		window.scrollTo(0,0);
		if(o) o.dispatchEvent(new Event('click'));
		else if(!!window.doIt) doIt(c);
		else alert("No doIt?");
		b.classList.add="visited";
	}
	function addCharListSelector(p,r,lang)
	{
		let section,list,s;
		section=document.createElement('section');
		if(lang=="ZhHans") s="<h2>Simplified hanzi</h2>";
		else if(lang=="ZhHant") s="<h2>Traditional hanzi</h2>";
		else if(lang=="Ko") s="<h2>Hanja</h2>";
		else s="<h2>Kanji</h2>";
		for(let a of r)
		{
			s+="<details open><summary><h3>";
			s+=a.title?a.title:"?";
			s+="</h3></summary><fieldset>";
			for(let c of [...a.chars]) s+='<button type="button">'+c+'</button>';
			s+="</fieldset></details>";
		}
		section.innerHTML=s;
		section.setAttribute("lang",makeLangIso(lang));
		p.appendChild(section);
		list=section.querySelectorAll('button');
		for(let b of list) b.addEventListener("click",function(ev){click(ev);});
		if(!!window.afterAddingChartSelector)
			if(p.querySelector('[lang="ja"]')&&p.querySelector('[lang="ko"]')
				&&p.querySelector('[lang="zh-Hans"]')&&p.querySelector('[lang="zh-Hant"]'))
				afterAddingChartSelector();
	}
		
	if(langSelector) addLangSelector(langSelector);
	if(dataSelector) addCharSelector(dataSelector);
	
	if(charListSelector)
	{
		let url=new URL(document.currentScript.src),
			path=url.pathname.replace("_js/selectorAcjk.js","")+'../',
			charListMap2=JSON.parse(map);
		for(let langLabel of ["Ja","Ko","ZhHans","ZhHant"])
		{
			if(charListMap2[langLabel])
				fetch(path+'dictionary'+langLabel+'.txt')
				.then(r=>r.text())
				.then(d=>
					{
						let r=[];
						d="["+d.replace(/\}\n\{/ug,"},{")+"]";
						let dd=JSON.parse(d);
						// store the dictionary in dicos if defined in the calling page
						if(typeof dicos !== "undefined") dicos[langLabel]=dd;
						for(let setLabel of charListMap2[langLabel])
						{
							let chars="";
							for(let d1 of dd)
								if(d1["set"][0]==setLabel) chars+=d1["character"];
							if(chars) r.push({title:makeTitle(setLabel),chars:chars});
						}
						addCharListSelector(charListSelector,r,langLabel);
					}
				);
		}
	}

})();