// part of this script (makeAnimatedGifFromPngs function) is an adaptation of
// Animated_GIF project sample
// see https://github.com/sole/Animated_GIF/blob/master/tests/basic.js
// other parts were written from scratch

// Main functions:
//  generateRedPngFromSvg(s,size,background):
//    s is the content of a svg file coming from MakeMeAHanzi or animCJK projects
//    generate a red PNG image
//    return a string which is the base64 representation of the image
//  generateAnimatedGifFromSvg(s,size,background,delay,dec,show,save):
//    s is the content of a svg file coming from MakeMeAHanzi or animCJK projects
//    generate an animated gif image
//    the image can be shown using the "show" function and saved using the "save" function

function computeOne(a,k,km)
{
	return Math.floor((k-1)*a/Math.max(km-1,1));
}
function colorize(k,km)
{
	// compute stroke color
	var r=252,g=14,b=28;
	return "rgb("+computeOne(r,k,km)+","+computeOne(g,k,km)+","+computeOne(b,k,km)+")";
}
function generatePngFromSvg(paths,background,mmah,size)
{
	// paths is an array that contains a list of stroke d and fill attributes
	// background will be the background of the image
	// mmah indicates if the data come from MakeMeAHanzi instead of AnimCJK
	// return a base64 encoded PNG image
	var cn,cx,k,km,m,r,x0,y0,x1,y1,x2,y2,x3,y3;
	// create a ghost canvas
	cn=document.createElement('canvas');
	cn.width=size;
	cn.height=size;
	cx=cn.getContext("2d");
	if (size!=1024) cx.scale(size/1024,size/1024);
	cx.fillStyle=background;
	cx.fillRect(0,0,cn.width,cn.height);
	// draw strokes in the canvas 
	km=paths.length;
	r=/ ?([MLQC]) ?([0-9-]+) ([0-9-]+) ?([0-9-]+)? ?([0-9-]+)? ?([0-9-]+)? ?([0-9-]+)?/g;
	for(k=0;k<km;k++) // draw last strokes first
	{
		cx.beginPath();
		x0=0;
		y0=0;
		while (m=r.exec(paths[k].d))
		{
			x1=parseInt(m[2]);
			y1=parseInt(m[3]);
			if (mmah) y1=-y1+900;
			if (!x0) x0=x1;
			if (!y0) y0=y1;
			if (m[1]=="M") cx.moveTo(x1,y1);
			else if (m[1]=="L") cx.lineTo(x1,y1);
			else
			{
				x2=parseInt(m[4]);
				y2=parseInt(m[5]);
				if (mmah) y2=-y2+900;
				if (m[1]=="Q") cx.quadraticCurveTo(x1,y1,x2,y2);
				else
				{
					x3=parseInt(m[6]);
					y3=parseInt(m[7]);
					if (mmah) y3=-y3+900;
					cx.bezierCurveTo(x1,y1,x2,y2,x3,y3);
				}
			}
		}
		cx.lineTo(x0,y0); // sometimes not necessary, but it doesn't matter
		x0=0;
		y0=0;
		cx.fillStyle=paths[k].fill;
		cx.fill();
	}
	// generate a base64 encoded PNG from the canvas then return it
	return cn.toDataURL("image/png");
}
function generateRedPngFromSvg(s,size,background)
{
	// generate a "red" PNG image from a svg
	// s is a text representing a character in svg format
	// size will be the size of the PNG
	// background will be the background of the PNG (including transparent)
	var k,km,img,reg,m,paths=[],mmah;
	// if mmah is true, assume the svg comes from MakeMeAHanzi
	// else assume the svg comes from animCJK
	mmah=!s.match(/class="acjk"/);
	// extract "d" attribute values
	if (mmah) reg=/<path d=\"([^\"]*)\" fill=\"lightgray\"/g;
	else reg=/<path id=\"[^\"]*\" d=\"([^\"]*)\"/g;
	while (m=reg.exec(s)) paths.push({d:m[1]});
	km=paths.length;
	// colorize "d"
	for (k=0;k<km;k++) paths[k].fill=colorize(k+1,km);
	// return base64 PNG image
	return generatePngFromSvg(paths,background,mmah,size);
}
function makeAnimatedGifFromPngs(ghost,delay,background,dec,show,save) {
    var imgs = ghost.getElementsByTagName('img');
    var firstImage = imgs[0];
    var imageWidth = firstImage.getAttribute("width");
    var imageHeight = firstImage.getAttribute("height");
    var tasks = [];
    function buildImageCallback(img) {
        return function(gif) {
            img.src = gif;
        };
    }

    function getBuildGIFTask(img) {
        return function(doneCallback) {
            var ag = new Animated_GIF({
                repeat: null, // Don't repeat
            });
            ag.setSize(img.getAttribute("width"), img.getAttribute("height"));
            ag.addFrame(img);
            var img2 = document.createElement('img');
            if(img.nextSibling) {
                img.parentNode.insertBefore(img2, img.nextSibling);
            } else {
                img.parentNode.appendChild(img2);
            }
            ag.getBase64GIF(function(gif) {
                var originalSrc = img.src;
                img.addEventListener('mouseenter', function() {
                    img.src = gif;
                }, false);
                img.addEventListener('mouseleave', function() {
                    img.src = originalSrc;
                }, false);
                doneCallback();
            });
        };
    }

    function runTasks(tasks) {
        var nextTaskIndex = 0;
        runNextTask();
        function runNextTask() {
            if(nextTaskIndex < tasks.length) {
                // console.log('running task', nextTaskIndex);
                var task = tasks[nextTaskIndex];
                task(function() {
                    nextTaskIndex++;
                    setTimeout(runNextTask, 100);
                });
            }
        }
    }

    tasks.push(function(doneCallback) {
        var agAll = new Animated_GIF({
            repeat: 0, // repeat 0 = Repeat forever
        });
        agAll.setSize(imageWidth, imageHeight);
        agAll.setDelay(delay);
        
        for(var i = 0; i < imgs.length; i++) {
            var img = imgs[i];
            agAll.addFrame(img);
        }
        var imgAll = document.createElement('img');
        var lastRenderProgress = Date.now();
		imgAll.style.display="block";
		imgAll.style.border="0";
        agAll.onRenderProgress(function(progress) {
            var t = Date.now();
            var elapsed = t - lastRenderProgress;
            lastRenderProgress = t;
        });
        agAll.getBase64GIF(function(image) {
            imgAll.src = image;
            // in case of automatisation, it's the right place to show or save the image
            if (show) show(image, background);
            if (save) save(image, background, dec);
            doneCallback();
        });
    });

    for(var i = 0; i < imgs.length; i++) {
        tasks.push(getBuildGIFTask(imgs[i]));
    }

    runTasks(tasks);
}
function generateAnimatedGifFromSvg(s,size,background,delay,dec,show,save)
{
	// generate an animated GIF image from a svg
	// s is a text representing a character in svg format
	// size will be the size of the PNG
	// background will be the background of the image (excepting transparent)
	// if background is transparent, make grey strokes transparent and background white
	// delay is the delay between two frames
	// dec is the decimal unicode of the character
	// show is a function called to show the image somewhere at the end of the process
	// show has 2 parameters:
	//  image which is a base64 representation of the image
	//  background which can be "transparent" or any css color
	// save is a function called to save the image somewhere at the end of the process
	// save has 3 parameters:
	//  image which is a base64 representation of the image
	//  background which can be "transparent" or any css color
	//  dec which is the decimal unicode of the character
	var k1,k2,km,img,imgsSrc=[],ghost,mmah,m,paths,reg;
	// if mmah is true, assume the svg comes from MakeMeAHanzi
	// else assume the svg comes from animCJK
	mmah=!s.match(/class="acjk/);
	// create a ghost div that will contains ephemeral PNG images
	ghost=document.createElement("div");
	// extract "d" attributes
	if (mmah) reg=/<path d=\"([^\"]*)\" fill=\"lightgray\"/g;
	else reg=/<path id=\"[^\"]*\" d=\"([^\"]*)\"/g;
	paths=[];
	while (m=reg.exec(s)) paths.push({d:m[1]});
	paths.reverse(); // draw last first in case of overlapping
	km=paths.length;
	if (km)
	{
		for (k1=km;k1>=0;k1--) // generate km+1 images, the first is special
		{
			for (k2=0;k2<km;k2++)
			{
				if (k2<k1)
				{
					// first trick to manage animated image with transparent background
					// if background parameter is "transparent", draw transparent strokes
					// else draw grey strokes
					if (background=="transparent") paths[k2].fill="transparent";
					else paths[k2].fill="#ccc";
				}
				else paths[k2].fill="#000";
			}
			// second trick to manage animated image with transparent background
			// if background parameter is "transparent", draw white background
			// else draw background as is
			// size is always 1024 here even if the generated image has a different size
			if (background=="transparent")
				imgsSrc[k1]=generatePngFromSvg(paths,"#fff",mmah,1024);
			else imgsSrc[k1]=generatePngFromSvg(paths,background,mmah,1024);
		}
		for (k1=km;k1>=0;k1--)
		{
			img=document.createElement('img');
			img.id="img"+k1;
			img.style.display="block";
			img.style.border="0";
			img.width=size;
			img.height=size;
			img.acjk={};
			img.acjk.km=km;
			img.onload=function(){
				var k,km,allDone=true;
				this.done=1;
				km=this.acjk.km;
				for(k=0;k<=km;k++)
					if (!ghost.querySelector("#img"+k).done) {allDone=false;break;}
				if (allDone) // ready to generate GIF image
					makeAnimatedGifFromPngs(ghost,delay,background,dec,show,save);
			};
			img.src=imgsSrc[k1];
			ghost.appendChild(img);
		}
	}
}
