$(document).ready(function() {
	//Instantiate global variables
	activeNodes = [];
	////Hardcoded variables
	debugView = false;
	scale = 50;
	dotSize = scale/10;
	offset = 30;
	djiColor = "#1CB4C9";

	//Add canvas context & canvas settings
	var mainCanvas=document.getElementById("mainCanvas").getContext("2d");
	var canvasOverlay=document.getElementById("canvasOverlay").getContext("2d");
	mainCanvas.lineCap="round";
	canvasOverlay.lineCap="round";
	canvasOverlay.strokeStyle=djiColor;
	canvasOverlay.lineWidth = 4;


	//Get random map data & draw it on the canvas
	$.getJSON('random_map.php', function(data) {
		graph = data;
	    nodes = graph.nodes;
	    roads = graph.roads;

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

	//Upon click, activate or disactive a node
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
	
	/**
	 * Gets a random hexadecimal color code
	 * @return string hexadecimal color code preceded by a #
	 */
	function getRandomColor() {
	    var letters = '0123456789ABCDEF'.split('');
	    var color = '#';
	    for (var i = 0; i < 6; i++ ) {
	        color += letters[Math.floor(Math.random() * 16)];
	    }
	    return color;
	}

	/**
	 * Distance formula calculating the distance between two elements
	 * @param  int|float x1 x value of elt1
	 * @param  int|float y1 y value of elt1
	 * @param  int|float x2 x value of elt2
	 * @param  int|float y2 y value of elt2
	 * @return int|float    distance between elt1 and elt2
	 */
	function getDistance(x1, y1, x2, y2) {
		return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
	}

	/**
	 * Runs through nodes looking for the closest to a given point
	 * @param  int|float x x value of point
	 * @param  int|float y y value of point
	 * @return Node   returns a Node object
	 */
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

	/**
	 * Upon activating a node we add it to activeNodes, add a div to body and
	 * potentially find the shortest route.
	 */
	function addToActive(node) {
		//Currently only supports 2 active nodes
		if (activeNodes.length == 2) {
			removeFromActive(activeNodes[activeNodes.length-1]);
		}

		//add node to activeNodes array & add a "pin" div
		activeNodes.push(node);
		x_coords = node.coords.x*scale - dotSize + offset + 7.5 - 13;
		y_coords = node.coords.y*scale - dotSize + offset + 7.5 - 33;
		$("body").append("<div node_key='" + node.key + "' class='pin' style='top: " + y_coords + "; left: " + x_coords + "'></div>");
		
		//if there are more than 1 active nodes we must run Djisktra
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
				console.log(data);
				var nodeA = null;
				var nodeB = null;
				for (var i = data.trace.length - 1; i >= 0; i--) {
					key = data.trace[i][1]
					if (nodeA === null) {
						nodeA = key;
					} else {
						nodeB = nodeA;
						nodeA = key;
						drawLine(canvasOverlay, nodes[nodeA].coords, nodes[nodeB].coords);
					}
				}

				$("div.dynamicContent").html(data.directions);
			})
		}
	}

	/**
	 * Remove a node from the activeNodes array, wipe the overlay canvas & remove the "pin" div
	 * @param  Node node the node to be removed
	 */
	function removeFromActive(node) {
		activeNodes.splice(activeNodes.indexOf(node), 1);
		canvasOverlay.clearRect (0, 0, 1000, 1000);
		$("div[node_key=" + node.key + "]").remove();
	}

	/**
	 * Draw a line from point A to point B
	 * @param  Context canvas the canvas on which to draw
	 * @param  Object a     the first coordinates object
	 * @param  Object b     the second coordinates object
	 */
	function drawLine(canvas, a, b) {
		var xini = a.x*scale + offset;
		var yini = a.y*scale + offset;
		var xend = b.x*scale + offset;
		var yend = b.y*scale + offset;
		canvas.beginPath();
		canvas.moveTo(xini, yini);
		canvas.lineTo(xend, yend);
		canvas.stroke();
	}
});