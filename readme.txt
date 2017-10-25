animCJK official version
------------------------

There is a repository on gitHub: https://github.com/parsimonhi/animCJK

In summary, when a change is made locally, update the server as follow:
- add to git locally (git add -A)
- commit locally (git commit -m "A message here")
- copy to server (git push -u origin master)

To update build and update animCJK:
1) run locally makeOfficial.php script (this builds svgsXxx folders from root svgsXxx folders)
2) run locally makeGraphics.php?d=Ja and makeGraphics.php?d=ZhHans (this makes graphicsXxx.txt files)
3) copy locally svgsXxx folders to official/animCJK folder
4) copy locally graphicsXxx.txt files to official/animCJK folder
5) open a terminal
	go to animCJK/official/animCJK using cd command
	add all
		git add -A
	or modify "something.xyz"
		modify "something.xyz" using a text editor
		git add "something.xyz"
	commit locally
		git commit -m "A message here"
	update github.com
		git push -u origin master
NB1: to recreate a repository locally from gitHub
	git clone https://github.com/parsimonhi/animKanji.git
NB2: to update a repository locally from gitHub
	git pull
NB3: to remove locally a repository without modifying gitHub repository
	just suppress the .git file (contains git data)
	evenly suppress all other files if they are useless

