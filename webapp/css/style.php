<?php
header("Content-type: text/css");
include("../core/config.php");
?>
body {
	margin: 0 auto;
	padding: 0;
	font-family: "Arial", sans-serif;
}

#sidebar-container {
	margin: 0 auto;
	padding: 25px 0 100px;
	position: fixed;
	top: 0;
	left: 0;
	z-index: 3;
	overflow-x: hidden;
	width: 0px;
	height: 100vh;
	white-space: nowrap;
	background-color: <?php echo $CONFIG["COLOR_SIDEBAR"] ?>;
	color: white;
	transition: 0.5s;
	box-shadow: 0 0 5px black;
}

#sidebar-container a.closebtn {
	color: #FFFFFF;
	position: absolute;
	top: 0;
	right: 25px;
	font-size: 36px;
}

#sidebar-container a.closebtn:hover, #sidebar-container a.closebtn:active, #sidebar-container a.closebtn:focus, #main #top-nav #top-nav-control a:hover, #main #top-nav #top-nav-control a:active, #main #top-nav #top-nav-control a:focus {
	text-decoration: none;
}

#sidebar-container h2 {
	display: none;
}

#sidebar-container #small-profile {
	text-align: center;
	margin: 50px auto;
}

#sidebar-container #small-profile img {
	margin: 0 auto 10px;
	width:100px;
	height:100px;
	border-radius:50px;
}

#sidebar-container #small-profile #user-name {
	font-size: 1.2em;
}

#sidebar-container #small-profile #user-group {
	font-weight: bold;
}

#sidebar-container ul {
	list-style-type: none;
	margin: 0 auto;
	padding: 0;
}

#sidebar-container ul li a {
	display: block;
	padding: 15px 20px;
	text-decoration: none;
	color: white;
}

#sidebar-container ul li a:hover {
	background-color: <?php echo $CONFIG["COLOR_SIDEBAR_LINKS"]; ?>;
}

#sidebar-container ul li.current {
	border-left: 4px solid #4BB91E;
	background-color: #37742F;
}

#sidebar-container ul li.notyet {
	border-left: 4px solid red;
	display: none;
}


/******************************
 *                            *
 *            MAIN            *
 *                            *
 ******************************/

#main {
	margin: 0 auto;
	padding: 60px 15px 50px;
	width: 100%;
	overflow-x: hidden;
	overflow-y: scroll;
	background-color: #ECECEC;
	font-size: 1.2em;
}

#main h1#main-title {
	padding: 20px;
}

#main div#content {
	margin: 0 auto;
	padding: 20px;
	background-color: #FFFFFF;
	border-radius: 5px;
}

#main footer {
	width: 100%;
	text-align:center;
	color: grey;
	font-size: 0.9em;
	padding: 30px 0 50px;
}

/******************************
 *                            *
 *           TOPNAV           *
 *                            *
 ******************************/

#main #top-nav {
	margin: 0 auto;
	padding: 0;
	background-color: #FFFFFF;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 75px;
	box-shadow: 0 0 5px #333333;
	transition: 0.5s;
	overflow-y: hidden;
	z-index: 2;
}

#main #top-nav #top-nav-control {
	width: 100%;
	padding: 20px 20px 20px 40px;
	font-size: 1.5em;
}

#main #top-nav #top-nav-control a {
	display: inline-block;
	color: #333333;
	position: absolute;
	top: 20px;
	right: 50px;
}

#main #top-nav ul {
	list-style-type: none;
	margin: 0 auto;
	padding: 0;
	overflow-y: scroll;
	height: 325px;
}

#main #top-nav ul li {
	display: block;
}

#main #top-nav ul li a {
	display: inline-block;
	width: 100%;
	padding: 25px 40px;
	margin: 0 auto;
	color: #333333;
}

#main #top-nav ul li a:hover {
	background-color: #F9F9F9;
	text-decoration: none;
}

/*
OTHER
*/

.profilemainpic {
	width: 2.3em;
	height: auto;
	float: left;
	margin-right: 20px;
}

/*******************************
 *                             *
 *           DESKTOP           *
 *                             *
 *******************************/

@media(min-width: 992px){
	#sidebar-container {
		padding: 0;
		width: 250px;
		float: left;
		overflow-x: hidden;
		position: relative;
		transition: 0s;
	}
	
	#sidebar-container a.closebtn {
		display:none;
	}
	
	#sidebar-container h2 {
		margin: 30px auto 50px;
		padding: 0;
		text-align: center;
		display: block;
	}
	
	#main {
		padding: 0 50px 50px;
		width: calc(100% - 250px);
		height: 100vh;
		float:right;
	}
	
	#main #top-nav {
		position: relative;
		margin: 0 -50px;
		padding: 0 40px;
		width: auto;
		text-align: right;
		height: auto;
	}
	
	#main #top-nav #top-nav-control {
		display: none;
	}
	
	#main #top-nav ul {
		overflow-y: hidden;
		height: auto;
	}
	
	#main #top-nav ul li {
		display: inline-block;
	}
	
	#main #top-nav ul li a {
		padding: 25px 10px;
	}
	
	#main #top-nav ul li#top-nav-panel-opener {
		display: none;
	}
	
	#twitch-player-container {
		padding-right: 0;
	}
	
	#twitch-chat-container {
		padding-left: 0;
	}
}