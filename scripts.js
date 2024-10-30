var question = 0;
var gift = "";
var scores = {};
var scoreTracker = [];
var name = "";
var email = "";
var radios = document.getElementById("options").elements["frequency"]; //const
var identity = quizData.identity;
var questions = quizData.questions.split('\r\n');
var number = parseInt(quizData.number);
var alphanumeric = quizData.alphanumeric;

var split = questions[question].split(",");
var cat = split[split.length - 1];
var quest = questions[question].replace("," + cat, "");

document.getElementById("question-text").innerHTML = quest;
document.getElementById("details").dataset.wanted = identity;
document.getElementById("nav-head").dataset.wanted = identity;
document.getElementById("hide").dataset.wanted = identity;
document.getElementById("details-info").dataset.wanted = identity;

var answers = ["Never", "Always"];
if (number >= 3) answers.splice(1,0,"Sometimes");
if (number == 4) answers.splice(2,0,"Mostly");
var descriptions = {
	"Never": "Not at all",
	"Sometimes": "Once in a while",
	"Mostly":  "Usually true",
	"Always": "Definitely true"
}

if (number == 4) {
	var style = document.createElement("style");
	style.innerHTML = "@media only screen and (max-width:655px){.option{display:block;width:100%;margin-bottom:10px}.option label{box-sizing:border-box;width:100%}}";
	document.head.appendChild(style);
}

innerText = [];
if (alphanumeric === "alphabetical") innerText = answers
else for(var i = 0; i < number; i++) innerText[i] = i + 1;

for(var i = 0; i < answers.length; i++) {
	var div = document.createElement('div');
	div.className = "option";
	
	var input = document.createElement('input');
	input.value = i + 1;
	input.setAttribute("name", "frequency");
	input.setAttribute("type", "radio");
	input.id = answers[i];
	
	var label = document.createElement('label');
	label.setAttribute("title", descriptions[answers[i]]);
	label.setAttribute("for", answers[i]);
	label.onclick = function(){radioToggle(this, true);};
	label.innerText = innerText[i];
	
	div.appendChild(input);
	div.appendChild(label);
	document.getElementById("options").appendChild(div);
	document.getElementById("options").appendChild(document.createTextNode("\n"));
}

gift = cat.trim();
radios = document.getElementById("options").elements["frequency"]; //const
document.getElementById("next").setAttribute("onclick", "next()");
document.getElementById("previous").setAttribute("onclick", "previous()");

document.getElementById("save").onclick = updateDetails;
document.getElementById("hide").onclick = function() { document.getElementById('details').classList.toggle('hide'); };

function radioToggle(label, runNext) {
	[].forEach.call(radios, function(itm, inx){ itm.parentElement.classList.remove("checked"); });
	label.parentElement.classList.add("checked");
	label.previousElementSibling.checked = true;
	if (runNext) next();
	document.getElementById('option-info').style.display = "none";
}

// Go to the next question
function next() {
	if (question === questions.length) return; // Quit if user is on last question
	if (!updateDetails()) return; // Quit if user has not entered their details
	
	// Check if user has selected an option
	var unchecked = 0;
	var noclass = 0;
	[].forEach.call(radios, function(itm, inx) {
		if (itm.checked && itm.parentElement.classList.contains('checked')) {
			if(!scores[gift]) scores[gift] = [];
			scores[gift].push(parseInt(itm.value));
			scoreTracker[question] = itm.id;
			itm.checked = false;			
		} else {
			if (!itm.checked) unchecked++;
			if (!itm.parentElement.classList.contains('checked')) noclass++;
		}
	});

	if (unchecked == number || noclass === number) { // Quit if no option was selected
		document.getElementById('option-info').style.display = "block";
		[].forEach.call(radios, function(itm, inx) { itm.checked = false; });
		return;
	}
	
	// If user has finished quiz, finalise results
	if (question == questions.length - 1) {
		question++;
		var best = {"values":[],"titles":[]};
		var categories = Object.keys(scores); //const
		for (i = 0; i < categories.length; i++) scores[categories[i]][scores[categories[i]].length] = scores[categories[i]].reduce(function(total, num){return total + num;});

		for (i = 0; i < categories.length; i++) {
			var score = scores[categories[i]].slice(-1)[0]; //const
			best["values"].push(score);
			best["titles"].push(categories[i]);
		}
		
		var combined = [];
		for (var i = 0; i < best["titles"].length; i++) combined.push([best["values"][i], best["titles"][i]]);
		combined.sort(function (a, b) { return b[0] - a[0]; });
		for (var i = 0; i < combined.length; i++) {
			best["values"][i] = combined[i][0];
			best["titles"][i] = combined[i][1];
		}
		
		// Display results
		var allScores = document.getElementById("all-scores");
		allScores.innerHTML = "";
		for (i = 3; i < best["titles"].length; i++) {
			var li = document.createElement("li");
			li.innerHTML = "<b>" + best["titles"][i] + "</b>: " + best["values"][i];
			allScores.appendChild(li);
		}

		document.getElementById("first-title").innerHTML = "Your most prominent trait is: " + best["titles"][0];
		document.getElementById("first-score").innerHTML = "Score: " + best["values"][0];
		document.getElementById("second-title").innerHTML = "Your second most prominent trait is: " + best["titles"][1];
		document.getElementById("second-score").innerHTML = "Score: " + best["values"][1];
		document.getElementById("third-title").innerHTML = "Your third most prominent trait is: " + best["titles"][2];
		document.getElementById("third-score").innerHTML = "Score: " + best["values"][2];
		document.getElementById("best").style.display = "block";
		
		params = {
			"name": name,
			"email": email,
			"to": quizData.to,
			"values": best["values"],
			"titles": best["titles"],
			"test": document.querySelector('#mainNav h1').innerText,
			"security": quizData.nonce,
			"action": "iqz_emailResults"
		};

		// Send email if user wants to
		if (params.to != "" || params.email != "") {
			var req = new XMLHttpRequest();
			req.open("POST", quizData.ajaxurl, true);
			req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			
			req.onload = function(){
				var info = document.getElementById("email-info");
				info.innerText = req.responseText;
				info.style.color = (req.status === 200 || req.status === 202) ? "green" : "red";
				if (identity === "checked") document.getElementById("email-info").style.display = "block";
			};
			req.send(jsonToQueryString(params));
		}
	} else { // Move onto next question
		question++;
		
		var split = questions[question].split(",");
		var cat = split[split.length - 1];
		var quest = questions[question].replace("," + cat, "");

		// var current = questions[question].split(","); //const
		document.getElementById("question-title").innerHTML = "Question " + (question + 1);
		document.getElementById("question-text").innerHTML = quest;
		gift = cat.trim();
		[].forEach.call(radios, function(itm, inx){
			itm.parentElement.classList.remove("checked");
			itm.checked = false;
		});
		if (scoreTracker[question]) radioToggle(document.getElementById(scoreTracker[question]).nextElementSibling, false);
	}
}

// Go back to previous question
function previous() {
	if (question === 0) return; // Quit if on 1st question
	if (question === questions.length) { // Undo finalised variables if user was on last question
		document.getElementById("email-info").style.display = "none";
		document.getElementById("best").style.display = "none";
		scores[gift].splice(-1,1);
		question--;
		var categories = Object.keys(scores); //const
		for (i = 0; i < categories.length; i++) {
			scores[categories[i]].splice(-1,1);
		}
	}
	
	question--;
	
	var split = questions[question].split(",");
	var cat = split[split.length - 1];
	var quest = questions[question].replace("," + cat, "");
	
	// var current = questions[question].split(","); //const
	document.getElementById("question-title").innerHTML = "Question " + (question + 1);
	document.getElementById("question-text").innerHTML = quest;
	gift = cat.trim();
	[].forEach.call(radios, function(itm, inx){itm.parentElement.classList.remove("checked");});
	if (scoreTracker[question]) radioToggle(document.getElementById(scoreTracker[question]).nextElementSibling, false);
	scores[gift].splice(-1,1);
}

function submitDetails(event) {
	event.preventDefault();
	updateDetails();
}

function validateEmail(email) {
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

function updateDetails() { // Check if the user has inputted their name/email
	if (identity === "false") return true;
	name = document.getElementById("name").value;
	var warning = document.getElementById("details-info");
	if (name == "") {
		warning.style.color = "red";
		warning.style.display = "block";
		return false;
	}
	email = document.getElementById("email").value;
	if (validateEmail(email) == false) {
		email = "";
		warning.style.color = "red";
		warning.style.display = "block";
		return false;
	}
	warning.style.display = "none";
	document.getElementById('hide').className = "show";
	document.getElementById('details').className = "hide";
	return true;
}

// Convert JSON to query string
function jsonToQueryString(json) {
	return Object.keys(json).map(function(key) {
			return encodeURIComponent(key) + '=' +
				encodeURIComponent(json[key]);
		}).join('&');
}