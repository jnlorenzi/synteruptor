var storage = $.localStorage;
var store_name = 'migenis_sort';
var head_fields = [
	{
		'name': 'rank',
		'title': '#',
		'desc': 'Rank based on the score'
	},
	{
		'name': 'breakid',
		'title': 'id',
		'desc': 'Identifier for this break'
	},
	{
		'name': 'gpart1',
		'title': 'Contig&nbsp;1',
		'desc': 'Name of the contig of the genome 1'
	},
	{
		'name': 'gpart2',
		'title': 'Contig&nbsp;2',
		'desc': 'Name of the contig of the genome 2'
	},
	{
		'name': 'tRNA_both',
		'title': 'tRNAs',
		'desc': '"2/both" if a tRNA is present in the break in both genomes, "1/one" if only in one genome'
	},
	{
		'name': 'tRNA_both_ext',
		'title': 'tRNAs extremities',
		'desc': '"2/both" if tRNAs are at an extremity of the break in both genomes, "1/one" if only in one genome'
	},
	{
		'name': 'cycle',
		'title': 'Diversity',
		'desc': 'Number of similar breaks found in other genomes'
	},
	{
		'name': 'break_size2',
		'title': 'CDS&nbsp;1',
		'desc': 'Total number of CDS found in the break in genome 1'
	},
	{
		'name': 'break_size1',
		'title': 'CDS&nbsp;2',
		'desc': 'Total number of CDS found in the break in genome 2'
	},
	{
		'name': 'noorthos1',
		'title': 'CDS without orthologs&nbsp;1',
		'desc': 'Number of CDS in the break in genome 1 without any orthologs in the genome 2'
	},
	{
		'name': 'noorthos2',
		'title': 'CDS without orthologs&nbsp;2',
		'desc': 'Number of CDS in the break in genome 2 without any orthologs in the genome 1'
	},
	{
		'name': 'paralogs1',
		'title': 'Paralogs&nbsp;1',
		'desc': 'Paralogs 1: number of CDS in genome 1 with at least one paralog in the same genome'
	},
	{
		'name': 'paralogs2',
		'title': 'Paralogs&nbsp;2',
		'desc': 'Paralogs 2: number of CDS in genome 2 with at least one paralog in the same genome'
	},
	{
		'name': 'content1',
		'title': 'Content&nbsp;1',
		'desc': 'Summary of the genes annotations in the break in genome 1'
	},
	{
		'name': 'content2',
		'title': 'Content&nbsp;2',
		'desc': 'Summary of the genes annotations in the break in genome 2'
	},
	{
		'name': 'fromto1',
		'title': 'From-to&nbsp;1',
		'desc': 'Order of the genes in the break in genome 1'
	},
	{
		'name': 'fromto2',
		'title': 'From-to&nbsp;2',
		'desc': 'Order of the genes in the break in genome 2'
	},
	{
		'name': 'delta_GC1',
		'title': 'Diff GC ratio 1',
		'desc': 'Difference of the GC ratio of the genes of this breaks with the GC ratio of the genome 1'
	},
	{
		'name': 'delta_GC2',
		'title': 'Diff GC ratio 2',
		'desc': 'Difference of the GC ratio of the genes of this breaks with the GC ratio of the genome 2'
	},
	{
		'name': 'score',
		'title': 'Score',
		'desc': 'Total score'
	},
];

var default_sort_vals = {
	'tRNA_both': 500,
	'tRNA_both_ext': 500,
	'cycle': 100,
	'noorthos1': 10,
	'noorthos2': 0,
	'break_size2': 1,
	'break_size1': -1,
	'paralogs1': 0,
	'paralogs2': 0,
	'delta_GC1': 0,
	'delta_GC2': 0,
};
var sort_vals = get_custom_sort_vals();

function get_custom_sort_vals() {
	// Retrieve custom vals
	var store_name = 'migenis_sort';
	var sort;
	if ( storage.isSet( store_name )) {
		sort = storage.get( store_name );
		console.log("Custom stored");
	}
	if (sort == null || Object.keys(sort).length == 0) {
		console.log("Error, back to default");
		sort = default_sort_vals;
	}
	return sort;
}

function set_custom_sort_vals() {
	// Get custom vals from the form
	var sort = {};
	
	$("#sortlist input").each(function() {
		var field = $( this ).attr( "name" );
		var val = $( this ).val();
		sort[field] = val;
	});
	
	if (Object.keys(sort).length > 0) {
		console.log("Custom saved in " + store_name);
		sort_vals = sort;
		storage.set( store_name, sort );
	}
}

function set_default_sort_vals() {
	sort_vals = default_sort_vals;
	storage.set( store_name, sort_vals );
	console.log("Back to default sort");
	build_sort_table();
}

function get_head(key) {
	var head = [];
	for (i=0; i < head_fields.length; i++) {
		head.push( head_fields[i][key] );
	}
	return head;
}

var sort_by = function(data) {
	n_fields = data.length;
	return function(A, B) {
		var a, b, field, key, order, result;
		for (var i = 0, l = n_fields; i < l; i++) {
			result = 0;
			field = data[i];
			key = field.name;
			
			a = A[key];
			b = B[key];
			
			if (typeof field.type == 'int') {
				a = parseInt(a);
				b = parseInt(b);
			}
			
			order = (field.order) ? 1 : -1;
			
			if (a < b) result = order * -1;
			if (a > b) result = order * 1;
			if (result !== 0) break;
		}
		return result;
	}
}

function sort_score(data) {
	// Compute the sort_score for each break
	for (var b=0; b < data.length; b++) {
		data[b].score = 0;
		// Check each field
		for (var field in sort_vals) {
			if (data[b][field]) {
				var val = data[b][field];
				if (field == 'delta_GC1' || field == 'delta_GC2') {
					val = Math.abs(val);
				}
				data[b].score += sort_vals[field] * val;
			}
		}
	}
	return data;
}

function build_sort_table() {
	$tab = $("#sortlist");
	if ($tab.length == 0) {
		return;
	} else {
		$tab.empty();
	}
	$("#custom p").on("click", function() {
		$("#custom_hidden").toggle();
	});
	
	var $head = $("<thead />");
	var head = "<tr>";
	head += "<th class='score'>Weight</th>";
	head += "<th>Field</th>";
	head += "<th>Description</th>";
	head += "</tr>";
	$head.append($( head ));
	$tab.append( $head );
	
	var $body = $("<tbody />");
	for (var i=0 ; i < head_fields.length; i++) {
		var field = head_fields[i];
		if (sort_vals[ field.name ] != null) {
			field.value = sort_vals[ field.name ];
			var line = "<tr>";
			line += "<td class='score'><input type='number' name='" + field.name + "' value=" + field.value + "></input></td>";
			line += "<td>" + field.title + "</td>";
			line += "<td>" + field.desc + "</td>";
			line += "</tr>";
			$body.append( $( line ) );
		}
	}
	$tab.append( $body );
}

