	  function init() {
		var $ = go.GraphObject.make;  // for conciseness in defining templates

		myDiagram =
		  $(go.Diagram, "myDiagram",  // must name or refer to the DIV HTML element
			{ allowDrop: true,  initialAutoScale: go.Diagram.Uniform, initialContentAlignment: go.Spot.Center });  // must be true to accept drops from the Palette
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
		  return $(go.Shape, "Ellipse",
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
	
		var lightText = '#3c3c3c';
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
			  	
			  $(go.Shape, "Rectangle",
				{ stroke: "#0B2026", strokeWidth: 1, minSize: new go.Size(100, 50) },
				new go.Binding("figure", "figure"),
				new go.Binding("fill", "fill")),
			  $(go.TextBlock,
				{ font: "13px robotoregular",
				  stroke: lightText,
				  margin: 8,
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
		  
		// Male Diagram
		/*myDiagram.nodeTemplateMap.add("Male",  // male
			$(go.Node, "Spot",
			   new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
			  $(go.Panel,
				$(go.Shape, "Rectangle",
					{ width: 60, height: 60,stroke:"#000000",
					strokeWidth: 1, fill:  "#45B0FE", portId: "" })
			  ),
			 // $(go.TextBlock,{  width: 20, height: 20,text: "",editable: true,font: "32pt Verdana",  position: new go.Point(1, 1),
//			  textEditor: customText}),
//			  $(go.TextBlock,{  width: 20, height: 20,text: "",editable: true,font: "32pt Verdana", position: new go.Point(1, 21),
//			  textEditor: customText}),
//			  $(go.TextBlock,{  width: 20, height: 20,text: "",editable: true,font: "32pt Verdana",  position: new go.Point(21, 1),
//			  textEditor: customText}),
//			  $(go.TextBlock,{  width: 20, height: 20,text: "",editable: true,font: "32pt Verdana",  position: new go.Point(21, 21),
//			  textEditor: customText}),
//			  $(go.TextBlock,{wrap: go.TextBlock.WrapFit},
//				new go.Binding("text", "text").makeTwoWay()),
			$(go.TextBlock,{wrap: go.TextBlock.WrapFit,textAlign: "center",font: "10pt helvetica, arial, sans-serif",stroke: "#000000",margin: 2,editable: true},
				new go.Binding("text", "text").makeTwoWay()),	
			// three named ports, one on each side except the bottom, all input only:
			makePort("T", go.Spot.Top, true, true),
			makePort("B", go.Spot.Bottom, true, true),
			makePort("L", go.Spot.Left, true, true),
			makePort("R", go.Spot.Right, true, true)
			));
			
		// Female Diagram	
		myDiagram.nodeTemplateMap.add("Female",  // male
			$(go.Node, "Spot",
			  new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
			  $(go.Panel,
				$(go.Shape, "Circle",
				  { width: 60, height: 60, stroke:"#000000",
					strokeWidth: 1, fill: "#FF92CB", portId: "" })
			  ),
			  $(go.TextBlock,{wrap: go.TextBlock.WrapFit,textAlign: "center",font: "10pt helvetica, arial, sans-serif",stroke: "#000000",margin: 2,editable: true},
				new go.Binding("text", "text").makeTwoWay()),	
			// three named ports, one on each side except the bottom, all input only:
			makePort("T", go.Spot.Top, true, true),
			makePort("B", go.Spot.Bottom, true, true),
			makePort("L", go.Spot.Left, true, true),
			makePort("R", go.Spot.Right, true, true)
			));*/
			
		
	
		
	   /*
		myDiagram.nodeTemplateMap.add("Marriage",
		  $(go.Node, "Auto", nodeStyle(),
			new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
			$(go.Shape, 
			  { fill: "#FFFFFF", stroke: "#000000", width:60, height:60}, new go.Binding("figure", "figure"),new go.Binding("parameter1", "parameter1")),
			$(go.TextBlock,
			  { margin: 5,
				maxSize: new go.Size(50, 50),
				wrap: go.TextBlock.WrapFit,
				textAlign: "center",
				editable: true,
				font: "normal 10pt Helvetica, Arial, sans-serif",
				stroke: '#000000'
			  },
			  new go.Binding("text", "text").makeTwoWay())
			// no ports, because no links are allowed to connect with a comment
		  ));
		  
		  myDiagram.nodeTemplateMap.add("Love",
		  $(go.Node, "Auto", nodeStyle(),
			new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
			$(go.Shape, 
			  { fill: "transparent", stroke: "#006D00", width:60, height:60}, new go.Binding("figure", "figure"),new go.Binding("parameter1", "parameter1")),
			$(go.TextBlock,
			  { margin: 5,
				maxSize: new go.Size(50, 50),
				wrap: go.TextBlock.WrapFit,
				textAlign: "center",
				editable: true,
				font: "normal 10pt Helvetica, Arial, sans-serif",
				stroke: '#006D00'
			  },
			  new go.Binding("text", "text").makeTwoWay())
			// no ports, because no links are allowed to connect with a comment
		  ));*/
		  
		/*  myDiagram.nodeTemplateMap.add("Hline",
		  $(go.Node, "Auto", nodeStyle(),
			new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
			$(go.Shape, 
			  { fill: "transparent", stroke: "#006D00", width:50, height:30}, new go.Binding("figure", "figure"),new go.Binding("parameter1", "parameter1")),
			$(go.TextBlock,
			  { margin: 5,
				maxSize: new go.Size(50, 50),
				wrap: go.TextBlock.WrapFit,
				textAlign: "center",
				editable: true,
				font: "normal 10pt Helvetica, Arial, sans-serif",
				stroke: '#006D00'
			  },
			  new go.Binding("text", "text").makeTwoWay())
			// no ports, because no links are allowed to connect with a comment
		  ));*/
		 var W_geometry = go.Geometry.parse("M 0,0 L 10,50 20,10 30,50 40,0");
		myDiagram.nodeTemplateMap.add("Line",
		  $(go.Node, "Auto", nodeStyle(),
			new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
			$(go.Shape, 
			  { fill: "#FFFFFF", stroke: "#cccccc", width:30, height:15 }, new go.Binding("figure", "figure"),new go.Binding("parameter1", "parameter1")),
			$(go.TextBlock,
			  { margin: 5,maxSize: new go.Size(90, 90),wrap: go.TextBlock.WrapFit,textAlign: "center",editable: true,font: "normal 10pt Helvetica, Arial, sans-serif",stroke: '#cccccc'},
			  new go.Binding("text", "text").makeTwoWay()),
			  {
				toolTip:  // define a tooltip for each node that displays the color as text
					$(go.Adornment, "Auto",$(go.Shape, { fill: "#FFFFCC" }),$(go.TextBlock, { margin: 4 },new go.Binding("text", "text")))  // end of Adornment			  
			  }
			// no ports, because no links are allowed to connect with a comment
		  ));
		// replace the default Link template in the linkTemplateMap
		/*myDiagram.linkTemplate =
		  $(go.Link,  // the whole link panel
			{ routing: go.Link.AvoidsNodes,
			  curve: go.Link.JumpOver,
			  corner: 5, toShortLength: 4,
			  relinkableFrom: true, relinkableTo: true, reshapable:true },
			$(go.Shape,   // the link path shape
			  { isPanelMain: true,name:"LINK",
				stroke: "#000000", strokeWidth: 2}),

			
			$(go.Shape,{name: "LINE1", toArrow: "Main", segmentIndex: 3, segmentFraction: 1}),
			$(go.Shape,{name: "LINE2", toArrow: "Main", segmentIndex: 3, segmentFraction: 0.90 }),
			$(go.Shape,{name: "LINE3", toArrow: "Main",  segmentIndex: 3, segmentFraction: 0.80 }),
			$(go.Shape,{ name: "LINE4", toArrow: "Main", segmentIndex: 3, segmentFraction: 0.70 }),
			$(go.Shape,{ name: "LINE5", toArrow: "Main", segmentIndex: 3, segmentFraction: 0.60 }),
			$(go.Shape,{ name: "LINE6", toArrow: "Main", segmentIndex: 3, segmentFraction: 0.50 }),
			$(go.Shape,{ name: "LINE7", toArrow: "Main", segmentIndex: 3, segmentFraction: 0.40 }),
			$(go.Shape,{ name: "LINE8", toArrow: "Main", segmentIndex: 3, segmentFraction: 0.30 }),
			$(go.Shape,{ name: "LINE9", toArrow: "Main", segmentIndex: 3, segmentFraction: 0.20 }),
		
	
			$(go.Shape,{ name: "LINE11", fromArrow: "Main", segmentIndex: 1, segmentFraction: 0.10 }),
			$(go.Shape,{ name: "LINE12", fromArrow: "Main", segmentIndex: 1, segmentFraction: 0.20 }),
			$(go.Shape,{ name: "LINE13", fromArrow: "Main", segmentIndex: 1, segmentFraction: 0.30 }),
			$(go.Shape,{ name: "LINE14", fromArrow: "Main", segmentIndex: 1, segmentFraction: 0.40 }),
			$(go.Shape,{ name: "LINE15", fromArrow: "Main", segmentIndex: 1, segmentFraction: 0.50 }),
			$(go.Shape,{ name: "LINE16", fromArrow: "Main", segmentIndex: 1, segmentFraction: 0.60 }),
			$(go.Shape,{ name: "LINE17", fromArrow: "Main", segmentIndex: 1, segmentFraction: 0.70 }),
			$(go.Shape,{ name: "LINE18", fromArrow: "Main", segmentIndex: 1, segmentFraction: 0.80 }),
			$(go.Shape,{ name: "LINE19", fromArrow: "Main", segmentIndex: 1, segmentFraction: 0.90 }),
	
			
			
			$(go.Shape,{ name: "LINE21", fromArrow: "Main", segmentIndex: 2, segmentFraction: 0.10 }),
			$(go.Shape,{ name: "LINE22", fromArrow: "Main", segmentIndex: 2, segmentFraction: 0.20 }),
			$(go.Shape,{ name: "LINE23", fromArrow: "Main", segmentIndex: 2, segmentFraction: 0.30 }),
			$(go.Shape,{ name: "LINE24", fromArrow: "Main", segmentIndex: 2, segmentFraction: 0.40 }),
			$(go.Shape,{ name: "LINE25", fromArrow: "Main", segmentIndex: 2, segmentFraction: 0.50 }),
			$(go.Shape,{ name: "LINE26", fromArrow: "Main", segmentIndex: 2, segmentFraction: 0.60 }),
			$(go.Shape,{ name: "LINE27", fromArrow: "Main", segmentIndex: 2, segmentFraction: 0.70 }),
			$(go.Shape,{ name: "LINE28", fromArrow: "Main", segmentIndex: 2, segmentFraction: 0.80 }),
			$(go.Shape,{ name: "LINE29", fromArrow: "Main", segmentIndex: 2, segmentFraction: 0.90 }),
	
	
			
			$(go.Panel, "Auto", 
			  { visible: false, name: "LABEL", segmentIndex: 2, segmentFraction: 0.5},
			  new go.Binding("visible", "visible").makeTwoWay(),
			  $(go.Shape,   // the link shape
			  { name:"LABELIMAGE",stroke: "#006D00",fill:"#FFFFFF"},
			  new go.Binding("figure", "figure").makeTwoWay()),
			  $(go.TextBlock,   // the label
				{ name:"LABELTEXT",
				  textAlign: "center",
				  font: "10pt helvetica, arial, sans-serif",
				  stroke: "#000000",
				  margin: 2, editable: true },
				new go.Binding("text", "text").makeTwoWay())
			)
			
			
			
			
		  );*/
		  myDiagram.linkTemplate =
		  $(go.Link,  // the whole link panel
			{ routing: go.Link.AvoidsNodes,
			  curve: go.Link.JumpOver,
			  corner: 5, toShortLength: 4,
			  relinkableFrom: true, relinkableTo: true, reshapable:true },
			
			$(go.Shape,   // the link path shape
			  { isPanelMain: true,name:"LINK",
				stroke: "#bdbdbd", strokeWidth: 2}),
			/*$(go.Shape,  // the arrowhead
			  { toArrow: "standard",
			  stroke: null, fill: "gray"}),*/
			$(go.Panel, "Auto",
			  { visible: false, name: "LABEL", segmentIndex: 2, segmentFraction: 0.5},
			  new go.Binding("visible", "visible").makeTwoWay(),
			  $(go.Shape,    // the link shape
				{stroke: "#FF0000" }),
			  $(go.TextBlock, "Yes",  // the label
				{ name:"LABELTEXT",
				  textAlign: "center",
				  font: "10pt helvetica, arial, sans-serif",
				  stroke: "#919191",
				  margin: 0, editable: true },
				new go.Binding("text", "text").makeTwoWay())
			)
		  );
		
	
		// make link labels visible if coming out of a "conditional" node
		 myDiagram.addDiagramListener("LinkDrawn", function(e) {
		  if (e.subject.fromNode.data.figure === "Diamond") {
			var label = e.subject.findObject("LABEL");
			if (label !== null) label.visible = true;
		  }
		})
		
		/*myDiagram.addDiagramListener("LinkDrawn", function(e) {
		  if (e.subject.fromNode.data.figure === "Diamond") {
			var label = e.subject.findObject("LABEL");
			if (label !== null) label.visible = true;
		  }
		})*/
	
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
				//{ category: "Male", text: "Gutt", figure: "Rectangle", geo: "F1 M0,0 L0,100 12,100 12,0 0,0z", fill: "#45B0FE" },
				{ category: "Female", text: "...", figure: "Ellipse", geo: "F1 M 0,0 a35,35 0 1,0 1,-1 z", fill: "#FFFFFF" }
			  ])
			});
		/*myRelation =
		  $(go.Palette, "myRelation",  // must name or refer to the DIV HTML element
			{
			  nodeTemplateMap: myDiagram.nodeTemplateMap,  // share the templates used by myDiagram
			  model: new go.GraphLinksModel([  // specify the contents of the Palette
	
				{ category: "Marriage", text: "Gift", figure: "Circle", parameter1:"Lable"}
				
			  ])
			});*/

          Mtg.Vaktrapport.genogram(myDiagram);
	  }
	
	  // Make all ports on a node visible when the mouse is over the node
	  function showPorts(node, show) {
		var diagram = node.diagram;
		if (!diagram || diagram.isReadOnly || !diagram.allowLink) return;
		var it = node.ports;
		while (it.next()) {
		  var port = it.value;
		  port.stroke = (show ? "#cccccc" : null);
		  port.fill = (show ? "#dddddd" : null);
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
	
	
	  // changes the item of the object
	  function rename(obj) {
		var newName = prompt("Rename " + obj.part.data.item + " to...");
		obj.part.data.item = newName; 
	  }
	
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
	  function newDocument() {
		var currentFile = document.getElementById("currentFile");
		// checks to see if all changes have been saved 
		if (myDiagram.isModified) {
		  var fileName = currentFile.textContent;
		  var save = confirm("Would you like to save changes to " + fileName + "?");
		  if (save) {
			if (fileName == UnsavedFileName) {
			  saveDocumentAs();
			} else {
			  saveDocument();
			}
		  }
		}
		// loads a blank diagram 
		myDiagram.model = new go.GraphLinksModel();
		myDiagram.model.undoManager.isEnabled = true;
		myDiagram.isModified = false;
		currentFile.innerHTML = UnsavedFileName;
	  }
	  function checkLocalStorage() {
		if (typeof(Storage) == "undefined" || navigator.appName == "Microsoft Internet Explorer") {
		  alert("Sorry! No web storage support. \n If you're using Internet Explorer, you must load the page from a server for local storage to work.");
		  return false;
		}
		return true;
	  }
	
	  // checks to see if all changes have been saved -> shows the open HTML element 
	  function openDocument() {
		if (checkLocalStorage()) {
		  if (myDiagram.isModified) {
			var fileName = document.getElementById("currentFile").textContent;
			var save = confirm("Would you like to save changes to " + fileName + "?");
			if (save) {
			  if (fileName == UnsavedFileName) {
				saveDocumentAs();
			  } else {
				saveDocument();
			  }
			}
		  }
		  var openDocument = document.getElementById("openDocument");
		  openDocument.style.visibility = "visible";
		}
	  }
	
	  // saves the current floorplan to local storage
	  function saveDocument(fn) {
		if (checkLocalStorage()) {
		  var currentFile = document.getElementById("currentFile");
		  var str = myDiagram.model.toJson();
		  var id = document.getElementById("pasientId").value;
		  var vid = document.getElementById("vaktrapportId").value;

		  if (window.XMLHttpRequest) {
			xmlhttp=new XMLHttpRequest();
		 } 
		 else { 
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		 }
		 xmlhttp.onreadystatechange=function() {
		 	if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                if(typeof(fn) == 'function') {
                    fn(xmlhttp);
                } else {
                    alert(xmlhttp.responseText);
                }
			}
		}
		if(id>0)
		{
		 xmlhttp.open("POST","saveVaktrapport.php",true);
		 xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		 xmlhttp.send("q="+str+"&id="+id+"&vid="+vid);
		}
		else
		{
			alert("Please Try Again");
		}
		 //  alert(str);
		  // exit();
		  localStorage.setItem(currentFile.textContent, str);
		  myDiagram.isModified = false;
		}
	  }
	
	  // saves floor plan to local storage 
	  function saveDocumentAs() {
		if (checkLocalStorage()) {
		  var saveName = prompt("Save file as...");
		  if (saveName) {
			var str = myDiagram.model.toJson();
			localStorage.setItem(saveName, str);
			myDiagram.isModified = false;
			var listbox = document.getElementById("mySavedFiles");
			// adds saved floor plan to listbox if it isn't there already 
			var exists = false;
			for (var i = 0; i < listbox.options.length; i++) {
			  if (listbox.options[i].value === saveName) {
				exists = true;
				break;
			  }
			}
			if (exists === false) {
			  var option = document.createElement('option');
			  option.value = saveName;
			  option.text = saveName;
			  listbox.add(option, null);
			}
			var currentFile = document.getElementById("currentFile");
			currentFile.innerHTML = saveName;
		  }
		}
	  }
	
	  // shows the remove HTML element 
	  function removeDocument() {
		if (checkLocalStorage()) {
		  // makes the HTML element visible 
		  var removeDocument = document.getElementById("removeDocument");
		  removeDocument.style.visibility = "visible";
		}
	  }
	  // deletes the selected file from local storage
	  function removeFile() {
		var listbox = document.getElementById('mySavedFiles2');
		var otherlistbox = document.getElementById('mySavedFiles');
		var fileName = undefined;
		for (var i = 0; i < listbox.options.length; i++) {
		  if (listbox.options[i].selected) fileName = listbox.options[i].text; // selected file  
		}
		if (fileName !== undefined) {
		  // verify deleting 
		  var verify = confirm("Are you sure you want to delete " + fileName + "?");
		  if (verify == false) return; 
		  // removes file from local storage
		  localStorage.removeItem(fileName);
		  // removes file from listboxes
		  var optionToRemove = listbox.options.selectedIndex;
		  listbox.remove(optionToRemove);
		  otherlistbox.remove(optionToRemove);
		  // hides the remove HTML element
		  var removeDocument = document.getElementById("removeDocument");
		  removeDocument.style.visibility = "hidden";
		}
	  }
	
	  // hides the open/remove elements when the "cancel" button is pressed
	  function closeElement() {
		var removeDocument = document.getElementById("removeDocument");
		if (removeDocument.style.visibility === "visible") removeDocument.style.visibility = "hidden";
		var openDocument = document.getElementById("openDocument");
		if (openDocument.style.visibility === "visible") openDocument.style.visibility = "hidden";
	  }