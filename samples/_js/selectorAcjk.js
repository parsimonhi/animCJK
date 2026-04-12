// Create a lang selector in the tag having .langSelector as class if any.
// Create a char selector in the tag having .dataSelector as class if any.
// Create a char list selector in the tag having .charListSelector as class if any.
// A doIt() function should be defined in the calling page if a tag having .dataSelector
// as class is present.
// This function is executed:
//	- when the user changes the lang using the lang selector
//	- clicking on the "Run" button of the char selector
// A function called afterAddingChartSelector may be defined in the calling page
// to execute something just after adding the char list to the page
// A charList object may be set in the calling page to indicate
// what the char list selector will contain
(function()
{
	let url=new URL(document.currentScript.src);
	let path=url.pathname.replace("_js/selectorAcjk.js","")+'../';
	let langSelector=document.querySelector('.langSelector');
	let dataSelector=document.querySelector('.dataSelector');
	let charListSelector=document.querySelector('.charListSelector');
	let charList2={};
	if(charListSelector)
	{
		// in practice, only some experimental pages define their own charList
		if(typeof charList !== "undefined") charList2=charList;
		else
		{
			// default map (convenient for samples)
			charList2.type="fromDic";
			charList2.map={};
			for(let lang of ["Ja","Ko","ZhHans","ZhHant"]) charList2.map[lang]={};
			charList2.map["Ja"]["Kanji"]=["g1","g2","g3","g4","g5","g6","g7"];
			charList2.map["Ko"]["Hanja"]=["hanja8","hanja7","hanja6","hanja5","hanja4"/*,"hanja3","hanja2","hanja1"*/];
			charList2.map["ZhHans"]["Simplified Hanzi"]=["hsk31","hsk32","hsk33","hsk34","hsk35","hsk36","hsk37","hsk38","hsk39"];
			charList2.map["ZhHant"]["Traditional Hanzi"]=["t31","t32","t33"/*,"t34","t35","t36","t37","t38","t39"*/];
			charList2.title={};
			// Ja titles
			charList2.title["hiragana"]="Hiragana";
			charList2.title["katakana"]="Katakana";
			for(let k=1;k<7;k++) charList2.title["g"+k]="Grade "+k;
			charList2.title["g7"]="Junior high school";
			charList2.title["g8"]="Jinmeiyō";
			charList2.title["g9"]="Hyōgai";
			charList2.title["gc"]="Components";
			// Ko titles
			charList2.title["hanguljamo"]="Jamo";
			charList2.title["hangulsyllables"]="Hangul";
			for(let k=8;k>0;k--) charList2.title["hanja"+k]="Hanja level "+k;
			charList2.title["hanja1800a"]="1800 Hanja part 1";
			charList2.title["hanja1800b"]="1800 Hanja part 2";
			charList2.title["commonHanjaNotInHanja1800"]="Common Hanja not in the 1800";
			charList2.title["ku"]="Some uncommon Hanja";
			charList2.title["kc"]="Some components";
			// ZhHans titles
			for(let k=1;k<10;k++) charList2.title["hsk3"+k]="Hsk v3 level "+k+", simplified Hanzi";
			charList2.title["frequent2500"]="2500 frequent simplified Hanzi";
			charList2.title["lessFrequent1000"]="1000 less frequent simplified Hanzi";
			charList2.title["commonNotFrequent3500"]="3500 common but not frequent simplified Hanzi";
			charList2.title["common7000"]="7000 common simplified Hanzi";
			charList2.title["traditional"]="Some traditional Hanzi";
			charList2.title["bopomofo"]="Bopomofo";
			// ZhHant titles
			for(let k=1;k<10;k++) charList2.title["t3"+k]="Hsk v3 level "+k+", traditional Hanzi";
			charList2.title["taiwan4808"]="4808 traditional Hanzi";
			charList2.title["t3NotTaiwan4808"]="Some HSK v3 traditional hanzi not in the 4808";
			charList2.title["taiwan4808NotT3"]="Some of the 4808 not in HSK v3";
			charList2.title["tu"]="Some uncommon traditional Hanzi";
			charList2.title["tc"]="Some components";
			// All langs
			charList2.title["radical"]="The 214 radicals";
			charList2.title["stroke"]="Strokes";
		}
	}
	
	function makeLangIso(langAcjk)
	{
		if(langAcjk=="ZhHans") return "zh-Hans";
		if(langAcjk=="ZhHant") return "zh-Hant";
		if(langAcjk=="Ko") return "ko";
		return "ja";
	}
	function setLang(lang)
	{
		localStorage.setItem("section",lang);
		buildSections(lang,path);
		if(!!window.doIt) doIt();
	}
	function addOneLangRadio(p,value,label,langIso)
	{
		let lang=localStorage.getItem("section")?localStorage.getItem("section"):"Ja";
		let s="",e,checked=(value==lang?" checked":"");
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
	if(langSelector) addLangSelector(langSelector);
	
	function addDataSelector(p)
	{
		let s="",e;
		s+="<label>Kanji, Hanja or Hanzi (one char only)<input name=\"data\"></label>";
		s+="<button type=\"button\" name=\"run\">Run</button>";
		p.innerHTML=s;
		e=p.querySelector('[name="run"]');
		e.addEventListener('click',(!!window.doIt)?doIt:function(){alert("No doIt?")});
	}
	if(dataSelector) addDataSelector(dataSelector);

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
	function populateFieldset(fieldset,chars)
	{
		for(let c of [...chars])
		{
			let b=document.createElement("button");
			b.setAttribute("type","button");
			b.textContent=c;
			fieldset.appendChild(b);
			b.addEventListener("click",function(ev){click(ev);});
		}
	}
	function buildFromList(section,lang)
	{
		let fieldsets=section.querySelectorAll('section[data-lang="'+lang+'"] fieldset[data-set]');
		for(let fieldset of fieldsets)
		{
			let setLabel=fieldset.getAttribute("data-set");
			let chars=charList2.chars[setLabel];
			populateFieldset(fieldset,chars);
		}
		if(!!window.afterAddingChartSelector) afterAddingChartSelector(lang,section.getAttribute("data-category"));
	}
	function buildFromDicContinue(section,lang,dd)
	{
		
		let fieldsets=section.querySelectorAll('fieldset[data-set]');
		for(let fieldset of fieldsets)
		{
			let setLabel=fieldset.getAttribute("data-set");
			let chars="";
			for(let d1 of dd)
				if(d1["set"].includes(setLabel)) chars+=d1["character"];
			populateFieldset(fieldset,chars);
		}
		if(!!window.afterAddingChartSelector) afterAddingChartSelector(lang,section.getAttribute("data-category"));
	}
	function buildFromDic(section,lang,path)
	{
		if((typeof dicos !== "undefined")&&dicos[lang])
		{
			buildFromDicContinue(section,lang,dicos[lang]);
		}
		else
		{
			let o={cache:"no-cache"};
			fetch(path+'dictionary'+lang+'.txt',o)
			.then(r=>r.text())
			.then(d=>
				{
					let r=[];
					// dictionaries contain a list of JSON
					// transform it in a global JSON 
					d="["+d.replace(/\}\n\{/ug,"},{")+"]";
					let dd=JSON.parse(d);
					// store the dictionary in dicos if defined in the calling page
					if(typeof dicos !== "undefined") dicos[lang]=dd;
					buildFromDicContinue(section,lang,dd);
				});
		}
	}
	function buildSections(lang,path)
	{
		let categories=Object.getOwnPropertyNames(charList2.map[lang]);
		let sections=[];
		for(let category of categories)
		{
			let section=charListSelector.querySelector('.charListSelector section[data-lang="'+lang+'"][data-category="'+category+'"]');
			if(section) return;
			let section2=document.createElement('section');
			sections.push(section2);
			let h2=document.createElement('h2');
			h2.textContent=category;
			section2.appendChild(h2);
			for(let setLabel of charList2.map[lang][category])
			{
				let details=document.createElement('details');
				details.open=true;
				let summary=document.createElement('summary');
				let h3=document.createElement('h3');
				h3.textContent=charList2.title[setLabel];
				summary.appendChild(h3);
				details.appendChild(summary);
				let fieldset=document.createElement('fieldset');
				fieldset.setAttribute("data-set",setLabel);
				details.appendChild(fieldset);
				section2.appendChild(details);
			}
			section2.setAttribute("data-lang",lang);
			section2.setAttribute("lang",makeLangIso(lang));
			section2.setAttribute("data-category",category);
			charListSelector.appendChild(section2);
			if(charList2.type=="fromList") buildFromList(section2,lang);
			else buildFromDic(section2,lang,path);
		}
	}
	if(charListSelector)
	{
		let langs=Object.getOwnPropertyNames(charList2.map);
		if(langSelector)
		{
			let lang=localStorage.getItem("section")?localStorage.getItem("section"):langs[0];
			buildSections(lang,path);
		}
		// if no langSelector, one cannot assume anything about the sections
		// so build all
		else for(let lang of langs)
		{
			buildSections(lang,path);
		}
	}
})();