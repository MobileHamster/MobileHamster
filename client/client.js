/** * the key for the id in the localstorage */var ID_STORE = "mobileHamsterId";/** * entry for not logged users   */var NOT_LOGGED_IN="<li>not logged in yet</li>";/** * entry for empty lists */var EMPTY_LIST = "<li>Sorry not entries yet</li>";/** * the template for the number of messages and invites */var MESSAGES_AND_INVITES_TMPL = "{msgs} message(s) {invites} invite(s)";/** * the template for the list of invites  */var INVITE_TMPL="\	<li>\		<a>\			<img src='{pic}'/>\			<h3>{name}</h3>\			<div>\				<img class='contactGender' src='gfx/{gender}.png' /> \				{flag} \				{online} \			</div>\			<div style='text-align: center' data-role='controlgroup' data-type='horizontal'>\				<a href='foo' data-role='button'>Accept</a>\				<a href='foo' data-role='button'>Decline</a>\			</div>\		</a>\	</li>";	var id = null;/** * checks if the user is logged in. */function isLoggedIn() {	return id != null && id.length == 128;}/** * this function does a logIn call to the webservice */function logIn(event) {	event.preventDefault();	var user = $('#username').val();	var pwd = $('#password').val();	$.getJSON('server/login.php?user=' + user + '&pwd=' + pwd, function(data) {		id = data.code;		localStorage.setItem(ID_STORE, id);		$.mobile.changePage("#start");	});}/** * calls the log out webservice call and unsets the local id */function logOut(event) {	event.preventDefault();	$.getJSON('server/logout.php?id=' + id, function(data) {		id = null;		localStorage.setItem(ID_STORE, null);		$.mobile.changePage("#login");	});}/** * function that uses setTimeout to call loadMessageCount repeatedly */function loadMessageCountRepeatedly() {	loadMessageCount();	setTimeout('loadMessageCountRepeatedly()', 10000);}/** * loads the avaiable messages and invites */function loadMessageCount() {	if (!isLoggedIn()) {		$("#msgs").html("");	} else {		$.getJSON('server/message_num.php?id=' + id, function(data) {			var input = {				msgs : data.messages,				invites : data.invites			}			var html = $.nano(MESSAGES_AND_INVITES_TMPL, input);			$("#msgs").html(html);		});	}}function loadFriendsList() {	getUserList('server/friends.php?id=' + id, '#friendsList', true);}function loadFavoritesList() {	getUserList('server/favorites.php?id=' + id, '#favoritesList', true);}/** * loads a user list from the given url. Checks if the user is currently * logged, displays a spinner if wanted and pastes the result into a * list view. * * @param {String} url the url to load data drom * @param {String} targetId the id of the targeted list view * @param {Boolean} showLoading if true, a showPageLoadingMsg is called */function getUserList(url, targetId, showLoading) {	// check if logged in	if (!isLoggedIn()) {		$(targetId).html(NOT_LOGGED_IN);		$(targetId).listview("refresh");	} else {		// should we display some kind of spinner?		if (showLoading) {			$.mobile.showPageLoadingMsg();		}		// load the data from the given url		$.getJSON(url, function(data) {			// construct the list data			var listElements = "";			if (data.length == 0) {				listElements = EMPTY_LIST;			}			for ( i = 0; i < data.length; i++) {				var user = data[i];				var name = data[i].name;				var pic = data[i].pic;				var gender = data[i].sex;				var flag = data[i].flagIcon != "" ? "<img class='contactCountry' src='" + data[i].flagIcon + "' />" : "";				var online = data[i].online ? "<span class='userOnline'>online</span>" : "";				listElements = listElements + "<li id='" + name + "'><a>" + "<img src='" + pic + "' />" + "<h3>" + name + "</h3>" + "<div>" + "<img class='contactGender' src='gfx/" + gender + ".png' />" + flag + online + "</div>" + "</a></li>";			}			// paste the list elements to the list			$(targetId).html(listElements).trigger('create');			$(targetId + "> li").click(function(e) {				e.stopImmediatePropagation();				e.preventDefault();				var tgt = e.target;				while(tgt.nodeName != "LI") {					tgt = tgt.parentNode;				}				console.log("id: "+tgt.id);			});						$(targetId).listview("refresh");			// if we displayed the spinner, we should remove it right now			if (showLoading) {				$.mobile.hidePageLoadingMsg();			}		});	}}function loadInvitesList() {	var showLoading = true;	var url = 'server/invites.php?id=' + id;	var targetId = "#invitesList";	// check if logged in	if (!isLoggedIn()) {		$(targetId).html(NOT_LOGGED_IN);		$(targetId).listview("refresh");	} else {		$(targetId).html("");		// should we display some kind of spinner?		if (showLoading) {			$.mobile.showPageLoadingMsg();		}		// load the data from the given url		$.getJSON(url, function(data) {			// construct the list data			var listElements = "";			if (data.length == 0) {				listElements = EMPTY_LIST;			}			for ( i = 0; i < data.length; i++) {				var user = data[i];				var input = {					pic : user.pic,					name : user.name,					gender : user.sex,					flag : (user.flagIcon != "" ? "<img class='contactCountry' src='" + user.flagIcon + "' />" : ""),					online : (user.online ? "<span class='userOnline'>online</span>" : "")				};				listElements = listElements + $.nano(INVITE_TMPL, input);			}			// paste the list elements to the list			$(targetId).html(listElements).trigger('create');			$(targetId).listview("refresh");			// if we displayed the spinner, we should remove it right now			if (showLoading) {				$.mobile.hidePageLoadingMsg();			}		});	}}function loadProfile() {	alert(window.location);}function appStart() {	// retrieve id from	id = localStorage.getItem(ID_STORE);	// update all footer elements	$('.footer').html('mobile hamster by joachiml');	// set the call back for the login form.	$("#loginSubmit").click(logIn);	$("#logoutSubmit").click(logOut);	$('#friends').live('pageshow', loadFriendsList);	$('#favorites').live('pageshow', loadFavoritesList);	$('#invites').live('pageshow', loadInvitesList);	$('#viewProfile').live('pageshow', loadProfile);	loadMessageCountRepeatedly();	// make sure user is logged in	if (!isLoggedIn()) {		$.mobile.changePage('#login');	}
}
$(document).ready(appStart);