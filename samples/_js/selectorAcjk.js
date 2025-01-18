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
	function addCharListSelectors(p,r)
	{
		// all in one, multilang
		for(let s of r) addCharListSelector(p,s.r,s.lang);
	}
	if(langSelector) addLangSelector(langSelector);
	if(dataSelector) addCharSelector(dataSelector);
	if(charListSelector)
	{
		let url=new URL(document.currentScript.src),
			path=url.pathname.replace("_js/selectorAcjk.js","")+'_php/';
		let options={method:"POST",body:JSON.stringify({s:"all",map:map})};
		fetch(path+'fetchCharList.php',options)
		.then(r=>r.json())
		.then(r=>addCharListSelectors(charListSelector,r));
	}
})();