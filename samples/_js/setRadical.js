// add/remove colors on radical
// assume that acjk or acjks (if any) decomposition is stored in a tag of the html code
// this tag should be a descendant of the svg parent or grandParent
// this tag should have a acjkDecomposition or acjksDecomposition class
// this tag should be the first to have such a class in the svg parent or grandParent
function setRadical(x,c1="#f90",c2="#06f")
{
	// set svg attribute instead of style to let apply global css if any
	let s1="\tfill:none;\n",s2="\tstroke:#000;\n";
	let svgs=document.querySelectorAll('svg.acjk');
	for(let i=0;i<svgs.length;i++)
	{
		let medians=svgs[i].querySelectorAll('[clip-path]'),len=medians.length;
		if(x)
		{
			// colorize the radical strokes
			let c=svgs[i].parentNode.querySelector('.acjksDecomposition'),d;
			if(!c) c=svgs[i].parentNode.querySelector('.acjkDecomposition');
			if(!c) c=svgs[i].parentNode.parentNode.querySelector('.acjksDecomposition');
			if(!c) c=svgs[i].parentNode.parentNode.querySelector('.acjkDecomposition');
			if(c)
			{
				let dec1=c.innerHTML.codePointAt(0);
				let dec2=svgs[i].id.replace(/^z([0-9]+).*$/,"$1");
				if(dec1==dec2) d=c.innerHTML;
			}
			if(d)
			{
				if(d.match(/^.?[.]/u))
				{
					// the character itself is the radical
					for(let j=0;j<len;j++) medians[j].setAttributeNS(null,"stroke",c1);
				}
				else
				{
					// assume the radical is followed by a dot
					// and its components if any are also followed by a dot
					let a,n=0;
					d=d.replace(/[~:]/gu,"").replace(/[^.0-9]+/gu," ");
					a=d.trim().split(/ +/);
					n=0;
					for(let b of a)
					{
						// b can be "." or "."+ a number or a number
						// keep b only when it ends by a number
						if(b.match(/[0-9]+$/))
						{
							let p=b.match(/^[.]/),km=-(-b.replace(/[^0-9]/,""));
							for(let k=0;k<km;k++)  
								if(p) medians[k+n].setAttributeNS(null,"stroke",c1);
								else medians[k+n].setAttributeNS(null,"stroke",c2);
							n+=km;
						}
					}
				}
			}
			svgs[i].innerHTML=svgs[i].innerHTML.replace(s2,"");
		}
		else
		{
			// uncolorize all the strokes
			for(let j=0;j<len;j++) medians[j].removeAttributeNS(null,"stroke");
			svgs[i].innerHTML=svgs[i].innerHTML.replace(s1+"}",s1+s2+"}");
		}
	}
}
