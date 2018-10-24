function closeSidePanel() {
	document.getElementById("sidebar-container").style.width = "0";
}

function openSidePanel() {
	document.getElementById("sidebar-container").style.width = "250px";
}

var open = false;
function toggleTopNav(){
	var el = document.getElementById("top-nav").style;
	if(open){
		// Make hidden
		open = false;
		el.height = "75px";
	}
	else {
		// Make visible
		open = true;
		el.height = "400px";
	}
}