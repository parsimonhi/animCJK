// add/remove colors on radical
// assume that acjk or acjks (if any) decomposition is stored in a tag
// the tag should be a descendant of the svg parent or grandParent
// the tag should have a acjkDecomposition or acjksDecomposition class
// the tag should be the first to have such a class in the svg parent or grandParent
function setRadical(x)
{
	let svgs=document.querySelectorAll('svg.acjk');
	for(let i=0;i<svgs.length;i++)
	{
		let medians=svgs[i].querySelectorAll('[clip-path]'),a,c,d,n=0;
		if(x)
		{
			c=svgs[i].parentNode.querySelector('.acjksDecomposition');
			if(!c) c=svgs[i].parentNode.querySelector('.acjkDecomposition');
			if(!c) c=svgs[i].parentNode.parentNode.querySelector('.acjksDecomposition');
			if(!c) c=svgs[i].parentNode.parentNode.querySelector('.acjkDecomposition');
			d=c?c.innerHTML:(d=medians.length+"");
			d=d.replace(/[:]+/g,"");
			d=d.replace(/[^.0-9]+/g," ").trim();
			d=d.replace(/ +/g," ");
			a=d.split(" ");
			if(a[0]==".")
			{
				// special cas: the character is a radical
				for(let j=0;j<medians.length;j++) medians[j].style.stroke="#fa0";
			}
			else for(let b of a)
			{
				let p=b.match(/\./),km=-(-b.replace(".",""));
				for(let k=0;k<km;k++)
					if(p) medians[k+n].style.stroke="#fa0";
					else medians[k+n].style.stroke="#00f";
				n+=km;
			}
		}
		else for(let j=0;j<medians.length;j++)
		{
			medians[j].style.stroke="";
		}
	}
}
