// create a lang selector in the .langSelector if any
// create a char selector in the .charSelector if any
// create some char list selectors in the .charListSelector if any
// assume a doIt() function is defined in the calling page
(function()
{
	let langSelector=document.querySelector('.langSelector');
	let charSelector=document.querySelector('.charSelector');
	let charListSelector=document.querySelector('.charListSelector');
	function makeLangIso(lang)
	{
		if(lang=="ZhHans") return "zh-hans";
		if(lang=="ZhHant") return "zh-hant";
		return "ja";
	}
	function setLang(lang)
	{
		document.querySelector('html').setAttribute("lang",makeLangIso(lang));
		doIt();
	}
	function addOneLangRadio(p,value,label)
	{
		let s="",e,checked=(value=="Ja"?" checked":"");
		e=document.createElement('label');
		s+="<input type=\"radio\" name=\"lang\" value=\""+value+"\" "+checked+">";
		s+=" "+label;
		e.innerHTML=s;
		p.appendChild(e);
		e=p.querySelector('[value="'+value+'"]');
		e.addEventListener('click',function(){setLang(value);});
	}
	function addLangSelector(p)
	{
		addOneLangRadio(p,"Ja","Japanese");
		addOneLangRadio(p,"ZhHans","Simplified Chinese");
		addOneLangRadio(p,"ZhHant","Traditional Chinese");
	}
	function addCharSelector(p)
	{
		let s="",e;
		e=document.createElement('label');
		s+="Hanzi or Kanji (one char only): <input name=\"char\"></label>";
		s+=" <button type=\"button\" name=\"run\">Run</button>";
		e.innerHTML=s;
		p.appendChild(e);
		e=p.querySelector('[name="run"]');
		e.addEventListener('click',doIt);
	}
	function click(ev)
	{
		let b=ev.target;
		let c=document.querySelector('[name="char"]');
		let o=document.querySelector('[name="run"]');
		c.value=b.innerHTML+c.value; // able to deal several characters
		o.focus(); // otherwise mobiles may show keyboard
		window.scrollTo(0,0);
		o.dispatchEvent(new Event('click'));
		b.classList.add="visited";
	}
	function addCharListSelector(p,r,lang)
	{
		let nav,list,s;
		nav=document.createElement('nav');
		if(lang=="ZhHans") s="<h2>Simplified hanzi</h2>";
		else if(lang=="ZhHant") s="<h2>Traditional hanzi</h2>";
		else s="<h2>Kanji</h2>";
		for(let a of r)
		{
			if(a.title) s+="<h3>"+a.title+"</h3>";
			s+="<p>";
			for(let c of [...a.chars]) s+='<button type="button">'+c+'</button>';
			s+="</p>";
		}
		nav.innerHTML=s;
		nav.setAttribute("lang",makeLangIso(lang));
		p.appendChild(nav);
		list=nav.querySelectorAll('button');
		for(let b of list) b.addEventListener("click",function(ev){click(ev);});
	}
	if(langSelector) addLangSelector(langSelector);
	if(charSelector) addCharSelector(charSelector);
	if(charListSelector)
	{
		for(let b of ["Ja","ZhHans","ZhHant"])
		{
			let options={method:"POST",body:JSON.stringify({s:b})};
			fetch('_php/fetchCharList.php',options)
			.then(r=>r.json())
			.then(r=>addCharListSelector(charListSelector,r,b));
		}
	}
})();