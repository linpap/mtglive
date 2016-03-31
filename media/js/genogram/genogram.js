	  function init() {
		var customTextEditor = document.getElementById("customTextEditor");
		customTextEditor.style.visibility = "hidden";
		
		
		var $ = go.GraphObject.make;  // for conciseness in defining templates
	
		myDiagram =
		  $(go.Diagram, "myDiagram", {
              allowDrop: true,
              initialAutoScale: go.Diagram.Uniform,
              initialContentAlignment: go.Spot.Center
            });

        load();


		function nodeStyle() {
		  return {
			// the Node.location is at the center of each node
			locationSpot: go.Spot.Center,
			//isShadowed: shadows,
			//shadowColor: "#242424",
			// handle mouse enter/leave events to show/hide the ports
			mouseEnter: function (e, obj) { showPorts(obj.part, true); },
			mouseLeave: function (e, obj) { showPorts(obj.part, false); }
		  };
		}
	
		// Define a function for creating a "port" that is normally transparent.
		// The "name" is used as the GraphObject.portId, the "spot" is used to control how links connect
		// and where the port is positioned on the node, and the boolean "output" and "input" arguments
		// control whether the user can draw links from or to the port.
		function makePort(name, spot, output, input) {
		  // the port is basically just a small circle that has a white stroke when it is made visible
		  return $(go.Shape, "Circle",
				   {
					  fill: "transparent",
					  stroke: "transparent",  // this is changed to "white" in the showPorts function
					  desiredSize: new go.Size(15, 15),
					  alignment: spot, alignmentFocus: spot,  // align the port on the main Shape
					  portId: name,  // declare this object to be a "port"
					  fromSpot: spot, toSpot: spot,  // declare where links may connect at this port
					  fromLinkable: output, toLinkable: input,  // declare whether the user may draw links to/from here
					  cursor: "pointer"  // show a different cursor to indicate potential link point
				   });
		}
	
		// define the Node template for regular nodes
	
		var lightText = '#ffffff';
		var darkText = '#454545';
		var startColor = "#79C900";
		var mainColor = "#FFFFFF";
		var endColor = "#DC3C00";
		
				
		myDiagram.nodeTemplateMap.add("",  // the default category
		  $(go.Node, "Spot", nodeStyle(),
			// The Node.location comes from the "loc" property of the node data,
			// converted by the Point.parse method.
			// If the Node.location is changed, it updates the "loc" property of the node data,
			// converting back using the Point.stringify method.
			new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
			// the main object is a Panel that surrounds a TextBlock with a rectangular Shape
			{ resizable: true, resizeObjectName: "SHAPE" },
			$(go.Panel, "Auto",
			  	
			  $(go.Shape, "RoundedRectangle",
				{ stroke: "#3c3c3c", strokeWidth: 1, minSize: new go.Size(150, 20) },
				new go.Binding("figure", "figure"),
				new go.Binding("fill", "fill")),
			  $(go.TextBlock,
				{ font: "13px robotoregular",
				  stroke: lightText,
				  margin: 10,
				  //minSize: new go.Size(100, 35),
				  //maxSize: new go.Size(100, NaN),
				  //wrap: go.TextBlock.WrapFit,
				  textAlign: "center",
				  alignment: go.Spot.Center,
				  isMultiline: true,
				  editable: true },
				new go.Binding("text", "text").makeTwoWay())
			),
			// four named ports, one on each side:
			makePort("T", go.Spot.Top, true, true),
			makePort("L", go.Spot.Left, true, true),
			makePort("R", go.Spot.Right, true, true),
			makePort("B", go.Spot.Bottom, true, true)
		  ));

		myDiagram.nodeTemplateMap.add("Marriage",
		  $(go.Node, "Auto", nodeStyle(),
			new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
			$(go.Shape, 
			  { fill: "#FFFFFF", stroke: "#3c3c3c", width:50, height:50}, new go.Binding("figure", "figure"),new go.Binding("parameter1", "parameter1")),
			$(go.TextBlock,
			  { margin: 5,
				maxSize: new go.Size(50, 50),
				wrap: go.TextBlock.WrapFit,
				textAlign: "center",
				editable: true,
				font: "13px robotoregular",
				stroke: '#3c3c3c'
			  },
			  new go.Binding("text", "text").makeTwoWay())
			// no ports, because no links are allowed to connect with a comment
		  ));
		  
		  myDiagram.nodeTemplateMap.add("Love",
		  $(go.Node, "Auto", nodeStyle(),
			new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
			$(go.Shape, 
			  { fill: "transparent", stroke: "#006D00", width:50, height:50}, new go.Binding("figure", "figure"),new go.Binding("parameter1", "parameter1")),
			$(go.TextBlock,
			  { margin: 5,
				maxSize: new go.Size(50, 50),
				wrap: go.TextBlock.WrapFit,
				textAlign: "center",
				editable: true,
				font: "13px robotoregular",
				stroke: '#006D00'
			  },
			  new go.Binding("text", "text").makeTwoWay())
			// no ports, because no links are allowed to connect with a comment
		  ));

		myDiagram.nodeTemplateMap.add("Line",
		  $(go.Node, "Auto", nodeStyle(),
			new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
			$(go.Shape, 
			  { fill: "#FFFFFF", stroke: "#3c3c3c", width:50, height:50 }, new go.Binding("figure", "figure"),new go.Binding("parameter1", "parameter1")),
			$(go.TextBlock,
			  { margin: 5,maxSize: new go.Size(50, 50),wrap: go.TextBlock.WrapFit,textAlign: "center",editable: true,font: "13px robotoregular",stroke: '#3c3c3c'},
			  new go.Binding("text", "text").makeTwoWay()),
			  {
				toolTip:  // define a tooltip for each node that displays the color as text
					$(go.Adornment, "Auto",$(go.Shape, { fill: "#FFFFCC" }),$(go.TextBlock, { margin: 4 },new go.Binding("text", "text")))  // end of Adornment			  
			  }
			// no ports, because no links are allowed to connect with a comment
		  ));
		// replace the default Link template in the linkTemplateMap
		myDiagram.linkTemplate =
		  $(go.Link,  // the whole link panel
			{ routing: go.Link.AvoidsNodes,
			  curve: go.Link.JumpOver,
			  corner: 5, toShortLength: 4,
			  relinkableFrom: true, relinkableTo: true, reshapable:true },
			$(go.Shape,   // the link path shape
			  { isPanelMain: true,name:"LINK",
				stroke: "#cccccc", strokeWidth: 2}),

			$(go.Shape,{ name: "LINE1", toArrow: "", segmentIndex: 3, segmentFraction: 1}),
			$(go.Shape,{ name: "LINE2", toArrow: "", segmentIndex: 3, segmentFraction: 0.90 }),
			$(go.Shape,{ name: "LINE3", toArrow: "", segmentIndex: 3, segmentFraction: 0.80 }),
			$(go.Shape,{ name: "LINE4", toArrow: "", segmentIndex: 3, segmentFraction: 0.70 }),
			$(go.Shape,{ name: "LINE5", toArrow: "", segmentIndex: 3, segmentFraction: 0.60 }),
			$(go.Shape,{ name: "LINE6", toArrow: "", segmentIndex: 3, segmentFraction: 0.50 }),
			$(go.Shape,{ name: "LINE7", toArrow: "", segmentIndex: 3, segmentFraction: 0.40 }),
			$(go.Shape,{ name: "LINE8", toArrow: "", segmentIndex: 3, segmentFraction: 0.30 }),
			$(go.Shape,{ name: "LINE9", toArrow: "", segmentIndex: 3, segmentFraction: 0.20 }),
		
			$(go.Shape,{ name: "LINE11", fromArrow: "", segmentIndex: 1, segmentFraction: 0.10 }),
			$(go.Shape,{ name: "LINE12", fromArrow: "", segmentIndex: 1, segmentFraction: 0.20 }),
			$(go.Shape,{ name: "LINE13", fromArrow: "", segmentIndex: 1, segmentFraction: 0.30 }),
			$(go.Shape,{ name: "LINE14", fromArrow: "", segmentIndex: 1, segmentFraction: 0.40 }),
			$(go.Shape,{ name: "LINE15", fromArrow: "", segmentIndex: 1, segmentFraction: 0.50 }),
			$(go.Shape,{ name: "LINE16", fromArrow: "", segmentIndex: 1, segmentFraction: 0.60 }),
			$(go.Shape,{ name: "LINE17", fromArrow: "", segmentIndex: 1, segmentFraction: 0.70 }),
			$(go.Shape,{ name: "LINE18", fromArrow: "", segmentIndex: 1, segmentFraction: 0.80 }),
			$(go.Shape,{ name: "LINE19", fromArrow: "", segmentIndex: 1, segmentFraction: 0.90 }),
	
			$(go.Shape,{ name: "LINE21", fromArrow: "", segmentIndex: 2, segmentFraction: 0.10 }),
			$(go.Shape,{ name: "LINE22", fromArrow: "", segmentIndex: 2, segmentFraction: 0.20 }),
			$(go.Shape,{ name: "LINE23", fromArrow: "", segmentIndex: 2, segmentFraction: 0.30 }),
			$(go.Shape,{ name: "LINE24", fromArrow: "", segmentIndex: 2, segmentFraction: 0.40 }),
			$(go.Shape,{ name: "LINE25", fromArrow: "", segmentIndex: 2, segmentFraction: 0.50 }),
			$(go.Shape,{ name: "LINE26", fromArrow: "", segmentIndex: 2, segmentFraction: 0.60 }),
			$(go.Shape,{ name: "LINE27", fromArrow: "", segmentIndex: 2, segmentFraction: 0.70 }),
			$(go.Shape,{ name: "LINE28", fromArrow: "", segmentIndex: 2, segmentFraction: 0.80 }),
			$(go.Shape,{ name: "LINE29", fromArrow: "", segmentIndex: 2, segmentFraction: 0.90 }),
			
	
			$(go.Panel, "Auto", 
			  { visible: false, name: "LABEL", segmentIndex: 2, segmentFraction: 0.5},
			  new go.Binding("visible", "visible").makeTwoWay(),
			  $(go.Shape,   // the link shape
			  { name:"LABELIMAGE",stroke: "#a9a9a9",fill:"#FFFFFF",minSize: new go.Size(20, 20),maxSize: new go.Size(150, 150)},
			  new go.Binding("figure", "figure").makeTwoWay()),
			  $(go.TextBlock,   // the label
				{ name:"LABELTEXT",
				  textAlign: "center",
				  wrap: go.TextBlock.WrapFit,
				  font: "13px robotoregular",
				  stroke: "#3c3c3c",
				  margin: 2, editable: true },
				new go.Binding("text", "text").makeTwoWay())
			)
		  );
		// make link labels visible if coming out of a "conditional" node
		myDiagram.addDiagramListener("LinkDrawn", function(e) {
		//	alert('tsetset');
			
			
			  var selnode = myRelation.selection.first();
			 if(selnode)
			 {
				 //alert(linestr+'adfgasg');
				if(selnode.data.parameter1=="Line")
				{
					var line1 = "LINE1";
					var line2 = "LINE2";
					var linestr = "";
					//FROM CENTER TO END POINT LINES
					for(var line=1;line<=9;line++)
					{	
						linestr = "LINE"+line;
						e.subject.findObject(linestr).toArrow=selnode.data.text;
						e.subject.findObject(linestr).segmentIndex=3;
						//alert(linestr);
					}
					//FROM START TO CENTER LINES
					for(var line=11;line<=19;line++)
					{	
						linestr = "LINE"+line;
						e.subject.findObject(linestr).toArrow=selnode.data.text;
						e.subject.findObject(linestr).segmentIndex=1;
						//alert(linestr);
					}
					//FOR CENTER LINES
					for(var line=21;line<=29;line++)
					{	
						linestr = "LINE"+line;
						e.subject.findObject(linestr).toArrow=selnode.data.text;
						e.subject.findObject(linestr).segmentIndex=2;
						//alert(linestr);
					}
				}
				else if(selnode.data.parameter1=="Lable"){ 
						e.subject.findObject("LABELTEXT").text=selnode.data.text;
						e.subject.findObject("LABELIMAGE").figure=selnode.data.figure;
						var label = e.subject.findObject("LABEL");
						if (label !== null) label.visible = true;
				}
			}
		})

		// temporary links used by LinkingTool and RelinkingTool are also orthogonal:
		myDiagram.toolManager.linkingTool.temporaryLink.routing = go.Link.Orthogonal;
		myDiagram.toolManager.relinkingTool.temporaryLink.routing = go.Link.Orthogonal;
	
		load();  // load an initial diagram from some JSON text
	
		// initialize the Palette that is on the left side of the page
		myPalette =
		  $(go.Palette, "myPalette",  // must name or refer to the DIV HTML element
			{
			  nodeTemplateMap: myDiagram.nodeTemplateMap,  // share the templates used by myDiagram
			  model: new go.GraphLinksModel([  // specify the contents of the Palette
				{ category: "Male", text: "Gutt", figure: "RoundedRectangle" , fill: "#0A7BB5"},
				{ category: "Female", text: "Jente", figure: "RoundedRectangle" , fill: "#aa34b2"}
			  ])
			});
		myRelation =
		  $(go.Palette, "myRelation",  // must name or refer to the DIV HTML element
			{
			  nodeTemplateMap: myDiagram.nodeTemplateMap,  // share the templates used by myDiagram
			  model: new go.GraphLinksModel([  // specify the contents of the Palette
	
				{ category: "Marriage", text: "G", figure: "Circle", parameter1:"Lable"},
				{ category: "Love", text: "K", figure: "Circle", parameter1:"Lable"}//,
			   /* { category: "Hline", text: "", figure: "LineH", parameter1:"Lable"},*/
	
				//{ category: "Line", text: "DisTrust", figure: "DisTrustS", parameter1:"Lable"},
				//{ category: "Line", text: "Hostile", figure: "HostileS", parameter1:"Lable"}
				
			  ])
			});
	
	  }
	
	  // Make all ports on a node visible when the mouse is over the node
	  function showPorts(node, show) {
		var diagram = node.diagram;
		if (!diagram || diagram.isReadOnly || !diagram.allowLink) return;
		var it = node.ports;
		while (it.next()) {
		  var port = it.value;
		  port.stroke = (show ? "white" : null);
		  port.fill = (show ? "white" : null);
		}
	  }
	
	
	  // Show the diagram's model in JSON format that the user may have edited
	  function save() {
		var str = myDiagram.model.toJson();
		document.getElementById("mySavedModel").value = str;
	  }
	  function load() {
		var str = document.getElementById("mySavedModel").value;
		myDiagram.model = go.Model.fromJson(str);
		myDiagram.undoManager.isEnabled = true;
	  }
	  var UnsavedFileName = "(Unsaved File)";

	  // shows/hides gridlines
	  // to be implemented onclick of a button
	  function grid() {
		var grid = document.getElementById("grid"); 
		if (grid.checked === true) {
		  myDiagram.grid.visible = true;
		  return;
		} else {
		  myDiagram.grid.visible = false; 
		}
	  }
	
	  // shows/hides guidelines
	  function guidelines() {
		var guide = document.getElementById("guidelines")
		if (guide.checked === true) {
		  myDiagram.toolManager.draggingTool.isGuidelineEnabled = true;
		} else {
		  myDiagram.toolManager.draggingTool.isGuidelineEnabled = false;
		}
	  }
	
	  // enables/disables snapping tools, to be implemented by buttons 
	  function snap() {
		var snap = document.getElementById("snap");
		if (snap.checked === true) {
		  myDiagram.toolManager.draggingTool.isGridSnapEnabled = true;
		  myDiagram.toolManager.resizingTool.isGridSnapEnabled = true;
		} else {
		  myDiagram.toolManager.draggingTool.isGridSnapEnabled = false;
		  myDiagram.toolManager.resizingTool.isGridSnapEnabled = false;
		}
	  }
	
	  // user specifies the amount of space between nodes when making rows and column 
	  function spacer() {
		var space = prompt("Desired space between nodes (in pixels):", "0"); 
		return space; 
	  }
	
	  // arrowkey function is determined by a dropbox in the toolbar
	  function arrowMode() {
		var move = document.getElementById("move");
		var select = document.getElementById("select");
		var scroll = document.getElementById("scroll");
		if (move.checked === true) {
		  myDiagram.commandHandler.arrowKeyBehavior = "move";
		} else if (select.checked === true) {
		  myDiagram.commandHandler.arrowKeyBehavior = "select";
		} else if (scroll.checked === true) {
		  myDiagram.commandHandler.arrowKeyBehavior = "scroll";
		}
	  }

