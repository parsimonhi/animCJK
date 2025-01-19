// add/remove colors on radical
// assume that acjk or acjks (if any) decomposition is stored in a tag
// the tag should be a descendant of the svg parent or grandParent
// the tag should have a acjkDecomposition or acjksDecomposition class
// the tag should be the first to have such a class in the svg parent or grandParent
function setRadical(x,c1="#f90",c2="#06f")
{
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
			d=d.replace(/[:]+/g,"");
			d=d.replace(/[^.0-9]+/g," ").trim();
			d=d.replace(/ +/g," ");
			a=d.split(" ");
			if(a[0]==".")
			{
				// special cas: the character is a radical
				for(let j=0;j<medians.length;j++) medians[j].style.stroke=c1;
			}
			else for(let b of a)
			{
				let p=b.match(/\./),km=-(-b.replace(".",""));
				for(let k=0;k<km;k++)
					if(p) medians[k+n].style.stroke=c1;
					else medians[k+n].style.stroke=c2;
				n+=km;
			}
		}
		else for(let j=0;j<medians.length;j++)
		{
			medians[j].style.stroke="";
		}
	}
}
