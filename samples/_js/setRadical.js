// add/remove colors on radical
// assume that acjk or acjks (if any) decomposition is stored in a tag
// the tag should be a descendant of the svg parent or grandParent
// the tag should have a acjkDecomposition or acjksDecomposition class
// the tag should be the first to have such a class in the svg parent or grandParent
function setRadical(x,c1="#f90",c2="#06f")
{
	// set svg attribute instead of style to let apply global css if any
	let s1="\tfill:none;\n",s2="\tstroke:#000;\n";
	let svgs=document.querySelectorAll('svg.acjk');
	for(let i=0;i<svgs.length;i++)
	{
		let medians=svgs[i].querySelectorAll('[clip-path]'),a,c,d,dec1,dec2,chr,n=0;
		if(x)
		{
			c=svgs[i].parentNode.querySelector('.acjksDecomposition');
			if(!c) c=svgs[i].parentNode.querySelector('.acjkDecomposition');
			if(!c) c=svgs[i].parentNode.parentNode.querySelector('.acjksDecomposition');
			if(!c) c=svgs[i].parentNode.parentNode.querySelector('.acjkDecomposition');
			if(c)
			{
				dec1=c.innerHTML.codePointAt(0);
				dec2=svgs[i].id.replace(/^z([0-9]+).*$/,"$1");
				if(dec1==dec2) d=c.innerHTML;
				else d=medians.length+"";
			}
			else d=medians.length+"";
			d=d.replace(/[:]+/gu,"");
			d=d.replace(/[^.0-9]+/gu," ").trim();
			d=d.replace(/ +/gu," ");
			a=d.split(" ");
			// assume the radical is always followed by a number preceded by a dot
			// excepting if the whole character is a radical
			// if a radical that is not the whole character has to be decomposed,
			// add a dot to its components (rare)
			if(a[0]==".")
			{
				// special case: the character is a radical
				for(let j=0;j<medians.length;j++)
					medians[j].setAttributeNS(null,"stroke",c1);
			}
			else for(let b of a)
			{
				let p=b.match(/\./),km=-(-b.replace(".",""));
				for(let k=0;k<km;k++)  
					if(p) medians[k+n].setAttributeNS(null,"stroke",c1);
					else medians[k+n].setAttributeNS(null,"stroke",c2);
				n+=km;
			}
			svgs[i].innerHTML=svgs[i].innerHTML.replace(s2,"");
		}
		else
		{
			for(let j=0;j<medians.length;j++) medians[j].removeAttributeNS(null,"stroke");
			svgs[i].innerHTML=svgs[i].innerHTML.replace(s1+"}",s1+s2+"}");
		}
	}
}
