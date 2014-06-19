$(document).ready(function() {
	var c=document.getElementById("myCanvas").getContext("2d");
	scale = 50;
	dotSize = scale/10;
	offset = 20;
	activeNodes = [];
	$.getJSON('http://localhost/mapper/random_map.php', function(data) {
		graph = data;
	    nodes = graph.nodes;
	    roads = graph.roads;
	    landmarks = data.landmarks;

		for (var j = roads.length - 1; j >= 0; j--) {
    		c.strokeStyle=getRandomColor();
    		var road = roads[j].nodes;
    		for (var k = road.length - 2; k >= 0; k--) {
    			var n0 = nodes[road[k]].coords;
    			var n1 = nodes[road[k+1]].coords;
    			drawLine(n0, n1);
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


	$(window).on('click', function() {
		var x = (event.pageX-offset-dotSize)/scale;
		var y = (event.pageY-offset-dotSize)/scale;
		x = Math.round(x);
		y = Math.round(y);

		node = getClosestNode(x, y);
		if (activeNodes.indexOf(node) != -1) {
			removeFromActive(node);
		} else {
			addToActive(node);
		}
	});
	
	function getRandomColor() {
	    var letters = '0123456789ABCDEF'.split('');
	    var color = '#';
	    for (var i = 0; i < 6; i++ ) {
	        color += letters[Math.floor(Math.random() * 16)];
	    }
	    return color;
	}

	function getDistance(x1, y1, x2, y2) {
		return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
	}

	function getClosestNode(x, y) {
		var currentDist = Number.POSITIVE_INFINITY;
		var nodeA;
		for (var i = nodes.length - 1; i >= 0; i--) {
			node = nodes[i];
			if (node.coords.x == x && node.coords.y == y) {
				return node;
			}
			distance = getDistance(x, y, node.coords.x, node.coords.y);
			if (distance < currentDist) {
				currentDist = distance;
				nodeA = node;
			}
		}
		return nodeA
	}

	function addToActive(node) {
		activeNodes.push(node);
		x_coords = node.coords.x*scale - dotSize + offset + 7.5;
			y_coords = node.coords.y*scale - dotSize + offset + 7.5;
			$("body").append("<div node_key='" + node.key + "' class='pin' style='top: " + y_coords + "; left: " + x_coords + "'></div>");
		if (activeNodes.length >= 2) {
			var post_data =  {
				"dij": {
					"n0": activeNodes[0].key, 
					"n1": activeNodes[1].key
				},
				"graph_data": graph
			}
			$.post("dijkstra.php", post_data, function(data) {
				console.log(data);
				data = $.parseJSON(data);
				var nodeA = false;
				var nodeB = false;
				c.strokeStyle=getRandomColor();
				c.lineWidth = 10;
				$.each(data.trace, function(key, length) {
					if (!nodeA) {
						nodeA = key;
					} else {
						nodeB = nodeA;
						nodeA = key;
						drawLine(nodes[nodeA].coords, nodes[nodeB].coords);
					}
				});
			})
		}
	}

	function removeFromActive(node) {
		activeNodes.pop(node);
		$("div[node_key=" + node.key + "]").remove();
	}

	function drawLine(n0, n1) {
		var xini = n0.x*scale + offset;
		var yini = n0.y*scale + offset;
		var xend = n1.x*scale + offset;
		var yend = n1.y*scale + offset;
		c.beginPath();
		c.moveTo(xini, yini);
		c.lineTo(xend, yend);
		c.stroke();
	}
});