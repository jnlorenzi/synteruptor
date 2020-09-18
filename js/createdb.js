urls.stat = 'createdb_status.php';
var wait_delay = 10000;

function status_checker(id) {
	var pars = {
		"id": id,
		"command": "check",
	};
	console.log(pars);
	// Check the status a first time to see if the building is already started/finished
	console.log(format_url( urls.stat, pars ));
	$.getJSON( urls.stat, pars, function( config ) {
		console.log(config);
		console.log(config.status);
		if (config.status == 'preparation') {
			start_button(id);
		} else {
			continuous_check(id);
		}
	});
}

function start_button(id) {
	console.log("Start button created");
	$form = $("<form id='mailform' style='text-align:left;' />");
	$btol = $("<span><label for='btol'>Blocks tolerance: </label><input type='num' name='btol' class='btol' value=2 min=0 max=5 style='width:3em' /> genes gap allowed between pairs of orthologs in blocks (strict: 0)</span>");
	$form
		.append("<span>Finalization:</span><br>")
		.append($btol)
		.append("<br>")
		.append( $("<label for='mail'>Mail: </label><input type='text' class='mail' name='mail' class='mail' placeholder='E-mail address required to get results' style='width:30em' />") )
		.append( $("<br /><label for='author'>Author: </label><input type='text' class='author' name='author' class='author' placeholder='Author name(s) (optional)' style='width:30em' />") )
		.append( $("<br /><label for='description'>Description: </label><input type='text' class='description' name='description' class='description' placeholder='Description (optional)' style='width:30em' />") );
	$form.append("<br>");
	$button = $("<button type='submit' />")
		.text("Start building the database with these genomes");
	$form.append($button);
	
	/*
	$form.on("submit", function(e) {
		console.log("Submit");
		e.preventDefault();
		$(this).attr("disabled", "disabled");
		validator.form();
		start(id);
	});
	*/
	$("#building").append($form);
	validator = $( "#mailform" ).validate({
		rules: {
			mail: {
				required: true,
				email: true
			}
		},
		submitHandler: function(form) {
			$(this).attr("disabled", "disabled");
			start(id);
		}
		       
	});
}

function start(id) {
	var pars = {
		"id": id,
		"command": "start",
	};
	pars.mail = $(".mail").val();
	pars.author = $(".author").val();
	pars.description = $(".description").val();
	pars.btol = $(".btol").val();
	$("#addmore").closest("li").remove();
	$("#uploader").remove();
	$.get( urls.stat, pars, function( stat ) {
		continuous_check(id);
	});
}

function continuous_check(id) {
	$("#addmore").closest("li").remove();
	$("#uploader").remove();
	console.log("Continuous check for " + id);
	var pars = {
		"id": id,
		"command": "check",
	};
	$.getJSON( urls.stat, pars, function( config ) {
		$('#building')
			.empty()
			.html("Status: " + config.status);
		if (config.status == 'done') {
			finish(id);
		} else if (config.status == "error") {
			if (config.details) {
				console.log("Error encountered: " + config.details);
			}
			// Don't continue!
		} else {
			window.setTimeout(function() {
				continuous_check(id);
			}, wait_delay);
		}
	});
}

function finish(id) {
	$("#uploader").remove();
	var summary = $("<a />")
		.attr("href", urls.summary + "?version=" + id)
		.text("View the data");
	var $list = $("<ul />");
	$list.append( $( "<li />" ).append(summary) );
	$("#building").empty()
		.append( $("<p />").text("The database is ready:") )
		.append( $list );
	console.log("Check over");
}

jQuery(function() {
	var pars = get_url_parameters();
	var id = pars.id;
	
	// Not enough genomes = no need to check
	if ($("#building")) {
		// Start the status checker
		status_checker(id);
	} else {
		console.log("Not enough genomes to continue ");
	}
});

