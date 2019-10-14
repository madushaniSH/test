/*
    Filename: transition.js
    Author: Malika Liyanage
    Created: 17/07/2019
    Purpose: Script used for adding a soft transition effect to web pages
*/


/* 
	Function adds fade effect during page transisitions
*/
function fadeInPage(){
	/* Exits function if animation not supported by the browser*/
	if (!window.AnimationEvent){
		return;
	}
	var fader = document.getElementById("fader");
	fader.classList.add("fade-out");
	/* Once the DOM content is loaded*/
	document.addEventListener("DOMContentLoaded",function(){
		/* Exit if animation not supported*/
		if (!window.AnimationEvent){
			return;
		}
		/* Selects all anchor tags in the webpage*/
		var anchors = document.getElementsByTagName("a");
		for (var i = 0; i < anchors.length; i++){
			/* Skips iteration if anchor tags links to external websites*/
			if (anchor[i].hostname !== window.location.hostname){
				continue;
			}
			/* Once the anchor tag is clicked*/
			anchors[i].addEventListener("click", function(event) {
				var fader = document.getElementById("fader"),
					anchor = event.currentTarget;
				var listener = function(){
					window.location = anchor.href;
					fader.removeEventListener("animationend", listener);
				};
				fader.addEventListener("animationend", listener);
				/* Prevents location change until animation ends*/
				event.preventDefault();
				/* Fades out of the page*/
				fader.classList.add("fade-in");
			});
		}
	});
	/* For safari browser*/
	window.addEventListener('pageshow', function (event) {
		/* If event not saved in cache exit*/
		if (!event.persisted) {
			return;
		}
		/* Removes class stored in cache*/
		var fader = document.getElementById('fader');
		fader.classList.remove('fade-in');
	});
}

window.addEventListener("load", fadeInPage);
