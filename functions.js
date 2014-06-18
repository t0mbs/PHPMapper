$(document).ready(function() {
	var c=document.getElementById("myCanvas").getContext("2d");
	scale = 20;
	$.getJSON('http://localhost/mapper/random_map.php', function(data) {
	    var nodes = data.nodes;
	    console.log(nodes);
	    for (var i = nodes.length - 2; i >= 0; i--) {
	    	node = nodes[i];
	    	c.font="20px Times Roman";
	    	c.fillText(node.key, node.coordinates.x*scale,node.coordinates.y*scale);
	    	for (var j = node.related_nodes.length - 2; j >= 0; j--) {
	    		var xini = node.coordinates.x*scale;
	    		var yini = node.coordinates.y*scale;
	    		var xend = nodes[node.related_nodes[j]].coordinates.x*scale;
	    		var yend = nodes[node.related_nodes[j]].coordinates.y*scale;
	    		c.beginPath();
				c.moveTo(xini, yini);
				c.lineTo(xend, yend);
				c.stroke();
	    	}
	    };
	});

});