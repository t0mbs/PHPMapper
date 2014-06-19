$(document).ready(function() {
	var mainCanvas=document.getElementById("mainCanvas").getContext("2d");
	var canvasOverlay=document.getElementById("canvasOverlay").getContext("2d");
	debugView = false;
	scale = 50;
	dotSize = scale/10;
	offset = 30;
	activeNodes = [];
	djiColor = "#84EBBE";
	$.getJSON('http://localhost/mapper/random_map.php', function(data) {
		graph = data;
	    nodes = graph.nodes;
	    roads = graph.roads;
	    landmarks = data.landmarks;

		for (var j = roads.length - 1; j >= 0; j--) {
    		mainCanvas.strokeStyle=getRandomColor();
    		var road = roads[j].nodes;
    		for (var k = road.length - 2; k >= 0; k--) {
    			var n0 = nodes[road[k]].coords;
    			var n1 = nodes[road[k+1]].coords;
    			drawLine(mainCanvas, n0, n1);
    		};
    	};

    	if (debugView) {
    		 for (var i = nodes.length - 1; i >= 0; i--) {
		    	node = nodes[i];
		    	mainCanvas.font="10px Times Roman";
		    	mainCanvas.fillText(
		    		node.key, 
		    		node.coords.x*scale + offset + dotSize,
		    		node.coords.y*scale + offset - dotSize
		    		);
		    	mainCanvas.fillRect(
		    		node.coords.x*scale - dotSize/2 + offset,
		    		node.coords.y*scale - dotSize/2 + offset,
		    		dotSize,
		    		dotSize
		    		);
		    };
    	}
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
	    console.log(color);
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
		if (activeNodes.length == 2) {
			removeFromActive(activeNodes[activeNodes.length-1]);
		}
		activeNodes.push(node);
		x_coords = node.coords.x*scale - dotSize + offset + 7.5 - 13;
		y_coords = node.coords.y*scale - dotSize + offset + 7.5 - 33;
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
				canvasOverlay.strokeStyle=djiColor;
				canvasOverlay.lineWidth = 4;

				var nodeA = null;
				var nodeB = null;
				for (var i = data.trace.length - 1; i >= 0; i--) {
					key = data.trace[i][1]
					if (nodeA === null) {
						nodeA = key;
						console.log("First run! A:" + nodeA);
					} else {
						nodeB = nodeA;
						nodeA = key;
						drawLine(canvasOverlay, nodes[nodeA].coords, nodes[nodeB].coords);
						console.log("Subsequent run, A:" + nodeA +", B:" + nodeB);
					}
				};
			})
		}
	}

	function removeFromActive(node) {
		activeNodes.splice(activeNodes.indexOf(node), 1);
		canvasOverlay.clearRect (0, 0, 1000, 1000);
		$("div[node_key=" + node.key + "]").remove();
	}

	function drawLine(canvas, n0, n1) {
		var xini = n0.x*scale + offset;
		var yini = n0.y*scale + offset;
		var xend = n1.x*scale + offset;
		var yend = n1.y*scale + offset;
		canvas.beginPath();
		canvas.moveTo(xini, yini);
		canvas.lineTo(xend, yend);
		canvas.stroke();
	}
});