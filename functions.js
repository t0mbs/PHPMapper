$(document).ready(function() {
	var c=document.getElementById("myCanvas").getContext("2d");
	scale = 50;
	dotSize = scale/10;
	offset = 20;
	$.getJSON('http://localhost/mapper/random_map.php', function(data) {
	    var nodes = data.nodes;
	    var roads = data.roads;

    	console.log(nodes);
    	console.log(roads);

		for (var j = roads.length - 1; j >= 0; j--) {
    		c.strokeStyle=getRandomColor();
    		var road = roads[j];
    		for (var k = road.length - 2; k >= 0; k--) {
    			var n0 = nodes[road[k]].coords;
    			var n1 = nodes[road[k+1]].coords;

    			var xini = n0.x*scale + offset;
	    		var yini = n0.y*scale + offset;
	    		var xend = n1.x*scale + offset;
	    		var yend = n1.y*scale + offset;
	    		c.beginPath();
				c.moveTo(xini, yini);
				c.lineTo(xend, yend);
				c.stroke();
    		};
    	};

    	 for (var i = nodes.length - 1; i >= 0; i--) {
	    	node = nodes[i];
	    	c.font="10px Times Roman";
	    	c.fillText(
	    		node.key, 
	    		node.coords.x*scale + offset + dotSize,
	    		node.coords.y*scale + offset - dotSize
	    		);
	    	c.fillRect(
	    		node.coords.x*scale - dotSize/2 + offset,
	    		node.coords.y*scale - dotSize/2 + offset,
	    		dotSize,
	    		dotSize
	    		);
	    };
	});
	function getRandomColor() {
	    var letters = '0123456789ABCDEF'.split('');
	    var color = '#';
	    for (var i = 0; i < 6; i++ ) {
	        color += letters[Math.floor(Math.random() * 16)];
	    }
	    return color;
	}

});