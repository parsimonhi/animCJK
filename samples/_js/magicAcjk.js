// part of this script (makeAnimatedGifFromPngs function) is an adaptation of
// Animated_GIF project sample
// see https://github.com/sole/Animated_GIF/blob/master/tests/basic.js
// other parts were written from scratch
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
function makeAnimatedGifFromPngs(target,delay) {
    var imgs = document.querySelectorAll('img.ghostImg'+target.id);
    var firstImage = imgs[0];
    var imageWidth = firstImage.clientWidth;
    var imageHeight = firstImage.clientHeight;
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
            ag.setSize(img.clientWidth, img.clientHeight);
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
            // in case of automatisation, this is the right place to save the image
            doneCallback();
        });
        target.appendChild(imgAll);
    });

    for(var i = 0; i < imgs.length; i++) {
        tasks.push(getBuildGIFTask(imgs[i]));
    }

    runTasks(tasks);
}
function generatePngFromSvg(paths,mmah,transparent)
{
	// paths is an array that contains a list of stroke d and fill attributes
	// mmah indicates if the data come from MakeMeAHanzi instead of AnimCJK
	// return a base64 encoded PNG image
	var cn,cx,k,km,m,r,x0,y0,x1,y1,x2,y2,x3,y3;
	// create a ghost canvas
	cn=document.createElement('canvas');
	cn.width=1024;
	cn.height=1024;
	cx=cn.getContext("2d");
	if (transparent) cx.fillStyle="transparent";
	else cx.fillStyle="white";
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
function generateRedPngFromSvg(s,target)
{
	// generate a "red" PNG image from a svg
	// s is a text representing a character in svg format
	// target is a HTML element where the animated GIF will be displayed
	// delay is the delay between two frames
	// the size of the animated GIF image will be the size of target
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
	// create and display a PNG image in target
	img=document.createElement("img");
	img.style.display="block";
	img.style.border="0";
	img.width=target.clientWidth;
	img.height=target.clientHeight;
	img.src=generatePngFromSvg(paths,mmah,0);
	// in case of automatisation, this is the right place to save the image
	target.appendChild(img);
}
function generateAnimatedGifFromSvg(s,target,delay)
{
	// generate an animated GIF image from a svg
	// s is a text representing a character in svg format
	// target is a HTML element where the animated GIF will be displayed
	// delay is the delay between two frames
	// the size of the animated GIF image will be the size of target
	var k1,k2,km,img,imgsSrc=[],ghost,mmah,m,paths,reg;
	// if mmah is true, assume the svg comes from MakeMeAHanzi
	// else assume the svg comes from animCJK
	mmah=!s.match(/class="acjk"/);
	// create a ghost div that will contains ephemeral PNG images
	ghost=document.createElement("div");
	ghost.style.opacity="0"; // avoid disturbing page display
	ghost.style.position="absolute"; // avoid disturbing page display
	target.parentNode.appendChild(ghost);
	// extract "d" attributes
	if (mmah) reg=/<path d=\"([^\"]*)\" fill=\"lightgray\"/g;
	else reg=/<path id=\"[^\"]*\" d=\"([^\"]*)\"/g;
	paths=[];
	while (m=reg.exec(s)) paths.push({d:m[1]});
	paths.reverse(); // draw last first in case of overlapping
	km=paths.length;
	if (km)
	{
		for (k1=km;k1>=0;k1--) // generate km+1 images, the first is totally grey
		{
			for (k2=0;k2<km;k2++)
			{
				if (k2<k1) paths[k2].fill="#ccc";
				else paths[k2].fill="#000";
			}
			imgsSrc[k1]=generatePngFromSvg(paths,mmah,0);
		}
		for (k1=km;k1>=0;k1--)
		{
			img=document.createElement('img');
			img.id="img"+k1;
			img.className="ghostImg"+target.id;
			img.style.display="block";
			img.style.border="0";
			img.width=target.clientWidth;
			img.height=target.clientHeight;
			img.acjkKm=km;
			img.acjkTarget=target;
			img.acjkDelay=delay;
			img.acjkGhost=ghost;
			img.onload=function(){
				var k,km,target,delay,allDone=true;
				this.done=1;
				km=this.acjkKm;
				target=this.acjkTarget;
				delay=this.acjkDelay;
				ghost=this.acjkGhost;
				for(k=0;k<=km;k++)
					if (!document.getElementById("img"+k).done) {allDone=false;break;}
				if (allDone) // ready to generate GIF image
				{
					makeAnimatedGifFromPngs(target,delay);
					target.parentNode.removeChild(ghost); // remove ghost div and its imgs
				}
			};
			img.src=imgsSrc[k1];
			ghost.appendChild(img);
		}
	}
}