$(document).ready(function() {
	var c=document.getElementById("myCanvas").getContext("2d");
	scale = 50;
	dotSize = scale/10;
	offset = 20;
	$.getJSON('http://localhost/mapper/random_map.php', function(data) {
	    var nodes = data.nodes;
	    console.log(nodes);
	    for (var i = nodes.length - 1; i >= 0; i--) {
	    	node = nodes[i];
	    	c.font="10px Times Roman";
	    	c.fillText(
	    		node.key, 
	    		node.coordinates.x*scale + offset + dotSize,
	    		node.coordinates.y*scale + offset - dotSize
	    		);
	    	c.fillRect(
	    		node.coordinates.x*scale - dotSize/2 + offset,
	    		node.coordinates.y*scale - dotSize/2 + offset,
	    		dotSize,
	    		dotSize
	    		);
	    	for (var j = node.related_nodes.length - 1; j >= 0; j--) {
	    		var xini = node.coordinates.x*scale + offset;
	    		var yini = node.coordinates.y*scale + offset;
	    		var xend = nodes[node.related_nodes[j]].coordinates.x*scale + offset;
	    		var yend = nodes[node.related_nodes[j]].coordinates.y*scale + offset;
	    		c.beginPath();
				c.moveTo(xini, yini);
				c.lineTo(xend, yend);
				c.stroke();
	    	}
	    };
	});

});