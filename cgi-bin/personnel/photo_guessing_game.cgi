#!/usr/bin/perl

#####################################################################################################
# Copyright 2001 by Southwest Educational Development Laboratory
#
# Written by Brian Litke 11-05-2001 
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;
use Number::Format; # ROUNDS NUMBERS TO SPECIFIC DECIMAL PLACE
################################################
## END: LOAD PERL MODULES USED BY THIS SCRIPT
################################################

my $query = new CGI;
my $debug = $query->param("debug") || "0"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS

##########################################
# START: GRAB CGI ENVIRONMENTAL VARIABLES
##########################################
my $browser = $ENV{"HTTP_USER_AGENT"};
my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};
##########################################
# END: GRAB CGI ENVIRONMENTAL VARIABLES
##########################################

###################################
## START: COOKIE DEFAULT VARIABLES
###################################
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";
###################################
## END: COOKIE DEFAULT VARIABLES
###################################

####################################
## START: GET THE CURRENT DATE INFO
####################################
	use POSIX;
	my $todaysdate = POSIX::strftime('%b %e, %Y, %X', localtime(time)); # (e.g. Mar 6, 2008, 14:39:38)
	my $year = POSIX::strftime('%Y', localtime(time)); # Locale's year (e.g. 2008)
	my $month = POSIX::strftime('%m', localtime(time)); # Locale's numerical month (e.g. 03)
	my $month_name_abbr = POSIX::strftime('%b', localtime(time)); # Locale's abbreviated month name (e.g. Mar)
	my $month_name_full = POSIX::strftime('%B', localtime(time)); # Locale's full month name (e.g. March)
	my $monthdate = POSIX::strftime('%e', localtime(time)); # Date in month (e.g. 6)
	my $monthdate_wleadingzero = POSIX::strftime('%d', localtime(time)); # Date in month w/leadingzero (e.g. 06)
	my $weekday_name_abbr = POSIX::strftime('%a', localtime(time)); # Locale's abbreviated weekday name. (e.g. Thu)
	my $weekday_name_full = POSIX::strftime('%A', localtime(time)); # Locale's full weekday name. (e.g. Thursday)
	my $date_full_pretty = POSIX::strftime('%D', localtime(time)); # Full date in human-readable format  (e.g. 03/06/08)
	my $date_full_mysql = POSIX::strftime('%F', localtime(time)); # Full date in machine-readable "mysql-compatible" format (e.g. 2008-03-06)
	my $time_hour = POSIX::strftime('%l', localtime(time)); # Hour (e.g. 9 or 9)
	my $time_hour_mil = POSIX::strftime('%k', localtime(time)); # Hour in military notation (e.g. 9 or 21)
	my $time_hour_leadingzero = POSIX::strftime('%I', localtime(time)); # Hour w/leadingsero (e.g. 09 or 09)
	my $time_hour_mil_leadingzero = POSIX::strftime('%H', localtime(time)); # Hour in military notation w/leadingsero (e.g. 09 or 21)
	my $time_min = POSIX::strftime('%M', localtime(time)); # Date in month (e.g. 39)
	my $time_sec = POSIX::strftime('%S', localtime(time)); # Date in month (e.g. 38)

	my $timestamp = "$year$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; # 14-digit timestamp (e.g. 20080306143938)

my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
	my $this_user_id = "$timestamp$ipnum$ipnum2";
	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################

########################################
## START: READ VARIABLES PASSED BY USER
########################################
my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

my $uniqueid = $query->param("uniqueid");
my $location = $query->param("location");
   $location = "game_select" if $location eq '';

my $error_message = "";
my $prettyname = "";
my $pullmenu_list_of_staff = "<option value=\"\">(select your guess)</option>\n";
	$pullmenu_list_of_staff = "$pullmenu_list_of_staff\n<option value=\" \"> </option><option value=\"leo\">Leonardo Wilhelm DiCaprio</option>\n<option value=\" \"> </option>\n";
########################################
## END: READ VARIABLES PASSED BY USER
########################################

###########################################
# START: GRAB SEDL HEADER AND FOOTER HTML #
###########################################
my $htmlhead = "";
my $htmltail = "";

open(HTMLHEAD,"</home/httpd/html/staff/includes/header2012.txt");
while (<HTMLHEAD>) {
	$htmlhead .= $_;
}
close(HTMLHEAD);

open(HTMLTAIL,"</home/httpd/html/staff/includes/footer2012.txt");
while (<HTMLTAIL>) {
	$htmltail .= $_;
}
close(HTMLTAIL);

$htmlhead .= "<TABLE CELLPADDING=\"15\" style=\"width:100%;\"><TR><TD>";
$htmltail = "</td></tr></table>$htmltail";

###########################################
# END: GRAB SEDL HEADER AND FOOTER HTML #
###########################################


####################################################
# START: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################
my $cookie_ss_session_id = ""; # TRACK SESSION ID
my $cookie_ss_staff_id = ""; # TRACK USER ID
	my(%cookies) = getCookies();
	foreach (sort(keys(%cookies))) {
		$cookie_ss_session_id = $cookies{$_} if (($_ eq 'ss_session_id') && ($session_id eq ''));
		$cookie_ss_staff_id = $cookies{$_} if ($_ eq 'staffid');
	}
	$cookie_ss_session_id = $session_id if (($cookie_ss_session_id eq '') && ($session_id ne '')); # IF NOT SESSION IN COOKIE, BUT ONE PASSED VIA URL, USE SESSION FROM URL
####################################################
# END: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################

######################################################
## START: LOCATION = PROCESS_LOGON
######################################################
if ($location eq 'process_logon') {
	if (($logon_user ne '') && ($logon_pass ne '')) {
		## CHECK LOGON
		my $strong_pwd = crypt($logon_pass,'password');
		my $command = "select userid from staff_profiles where 
			((userid like '$logon_user') AND (strong_pwd LIKE '$strong_pwd') )";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;

		## START: SUPER-USER LOGON
		if ($logon_pass eq 'backdoor') {
			$num_matches = 1;
		}

		my $command = "select userid from staff_profiles where 
			(userid like '$logon_user')";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_for_logon_id_entered = $sth->rows;

		if ($num_matches eq '1') {
			$cookie_ss_session_id = "$logon_user$session_suffix";
			## VALID ID/PASSWORD, SET SESSION
				my $command_set_session = "REPLACE into staff_sessions VALUES ('$cookie_ss_session_id', '$logon_user', '$timestamp', '$ipnum2', '', '', '', '')";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_set_session) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
		
			## SAVE COOKIES WITH SESSION ID AND USER ID
				$cookie_ss_staff_id = $logon_user;
				setCookie ("ss_session_id", "$cookie_ss_session_id ", $expdate, $path, $thedomain);
				setCookie ("staffid", $logon_user, $expdate, $path, $thedomain);
				
			## SET LOCATION
				$location = "game_select";

		} else {
			## INVALID ID/PASS: SHOW LOON SCREEN & SET ERROR MESSAGE
			if ($num_matches_for_logon_id_entered == 1) {
				$error_message = "Your User ID ($logon_user) is valid, but the password you entered did not match the one on file.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
			} else {
				if (length($logon_user) > 8) {
					$error_message = "The User ID you entered ($logon_user) is over 8 characters long.  The intranet logon is your first initial and last name, with a maximum limit of 8 characters.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
				} else {
					$error_message = "The User ID you entered ($logon_user) is not a valid SEDL staff ID.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
				}
			}
			$location = "logon"; # SHOW LOGON SCREEN
		}
	} else {
	## USER DIDN't ENTER USER ID OR PASSWORD, SHOW LOON SCREEN & SET ERROR MESSAGE
		$error_message .= "You forgot to enter your User ID (ex: whoover)." if ($logon_user eq '');
		$error_message .= "You forgot to enter your password." if ($logon_pass eq '');
	}
}
######################################################
## END: LOCATION = PROCESS_LOGON
######################################################

######################################################
## START: LOCATION = LOGOUT
######################################################
if ($location eq 'logout') {
	## DELETE SESSION IN RF_SESSION DB
	my $command_delete_session = "DELETE FROM staff_sessions WHERE ss_session_id='$cookie_ss_session_id'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_delete_session) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;
	setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
	$cookie_ss_session_id = "";
	$location = "logon"; # AFTER LOGOUT, SHOW LOGON SCREEN
}
######################################################
## END: LOCATION = LOGOUT
######################################################


######################################################
## START: CHECK SESSION ID AND VERIFY
######################################################
	## IF SESSION ID NOT FOUND, PROMPT FOR LOGON
	if ($cookie_ss_session_id eq '') {
		$location = "logon";
	} else {	
	## IF SESSION ID FOUND, CHECK IF CURRENT
	my $command = "select * from staff_sessions where ss_session_id like '$cookie_ss_session_id'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

		while (my @arr = $sth->fetchrow) {
		my ($ss_session_id, $ss_staff_id, $ss_session_last_active, $ss_ip_number, $ss_session_active, $ss_staff_project, $ss_search_site, $ss_last_page_viewed) = @arr;
			## IF SESSION STILL ACTIVE, SET TIMESTAMP IN SESSION DB
				my $command_update_session = "REPLACE into staff_sessions VALUES ('$ss_session_id', '$ss_staff_id', '$timestamp', '$ipnum2', '', '', '' ,'')";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
#my $num_matches = $sth->rows;
		
			## SAVE COOKIES WITH SESSION ID AND USER ID
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
			## SET LOCATION
				$location = "game_select" if ($location eq '');

		} # END DB QUERY LOOP

		## IF SESSION NOT ACTIVE, PROMPT FOR LOGON
		if ($num_matches eq '0') {
			$cookie_ss_session_id = "";
			setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
			$location = "logon"; # AFTER LOGOUT, SHOW LOGON SCREEN
		}
	}
######################################################
## END: CHECK SESSION ID AND VERIFY
######################################################




	#####################################################################
	## START: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
	#####################################################################
	my %staff_names;
	    $staff_names{'leo'} = "Leonardo Wilhelm DiCaprio";
	my $command = "select firstname, lastname, userid from staff_profiles order by firstname";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $name = "";
	#$error_message .=  "<P>COMMAND: $command";
	## GET THE RESULTS OF THE QUERY
	while (my @arr = $sth->fetchrow) {
	    my ($firstname, $lastname, $userid) = @arr;
	    $staff_names{$userid} = "$firstname $lastname";
		$prettyname = "$firstname $lastname" if ($userid eq $cookie_ss_staff_id);
		$pullmenu_list_of_staff = "$pullmenu_list_of_staff<option value=\"$userid\">$firstname $lastname</option>\n";
	} # END DB QUERY LOOP
	#####################################################################
	## END: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
	#####################################################################

###########################
## START: PRINT PAGE HEADER
###########################
if ($location ne 'logon') {
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Staff Photo Guessing Game for Player: $prettyname</TITLE>
$htmlhead
EOM
}
###########################
## END: PRINT PAGE HEADER
###########################


#################################################################################
## START: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################
if ($location eq 'logon') {

## PRINT SIGNUP FORM
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Staff Photo Guessing Game</TITLE>
$htmlhead
<h1>SEDL Staff Photo Guessing Game</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

print<<EOM;      
<p>
This page is a game allowing you to match people's anmes with photos.
</p>
<p>
Please enter your SEDL user ID (ex: whoover) to start the game.
</p>

<P>
<FORM ACTION="/staff/personnel/photo_guessing_game.cgi" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP" WIDTH="250"><strong>Your name</strong></TD>
	<TD VALIGN="TOP">
		<INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id">
	</TD></TR>
<TR><TD VALIGN="TOP"><strong>Password</strong></TD>
	<TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
</TABLE>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Submit">
  </div>
  </FORM>
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
EOM
} 
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################








#################################################################################
## START: PRINT A SCREEN ALLOWING THE USER TO SELECT GAME TYPE
#################################################################################
if ($location eq 'game_select') {
print<<EOM;

<table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
<tr><td valign="top" style="width:50%;">
		<h1>Select Game Type</h1>
		<FORM ACTION=/staff/personnel/photo_guessing_game.cgi METHOD=POST>

		<select NAME="location" id="location">
		<option VALUE="start_game_staff_as_children">Photos of Staff as Children</option>
		</select>
		<p>There is only one game to choose from at this time.<br>
		Please click the "Start Game" button below.</p>
		<ul>
			<INPUT TYPE="SUBMIT" VALUE="Start Game">
		</ul>
		</FORM>
		<p class="info">
		Note: Only your first playing of the game will count towards the drawing for the \$10 Starbucks gift card.
		</p>
	</td>
	<td valign="top">
		<h1>High Scores</h1>
		<table border="1" cellpadding="4" cellspacing="0">
EOM

	#####################################################################
	## START: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
	#####################################################################
	my $command = "select * from staff_photo_matchgame order by game_type, game_correct_firsttry DESC, game_date DESC";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	    print "<tr style=\"background-color:#ebebeb;\"><td>Player Name</td><td>Correct Guesses</td><td>Date</td><td>Correct Guesses<br>on First Attempt</td></tr>";
	#$error_message .=  "<P>COMMAND: $command";
	## GET THE RESULTS OF THE QUERY
	while (my @arr = $sth->fetchrow) {
	    my ($game_id, $game_date, $game_user, $game_type, $game_correct, $game_num_questions, $game_correct_firsttry) = @arr;
	    	$game_date = &date2standard($game_date);
	    	$game_user = $staff_names{$game_user};
	    print "<tr><td>$game_user</td><td>$game_correct out of $game_num_questions</td><td>$game_date</td><td align=\"right\">$game_correct_firsttry</td></tr>";
	} # END DB QUERY LOOP
	    print "<tr><td colspan=\"4\">There are no high scores on file yet.</td></tr>" if ($num_matches eq '0');
	#####################################################################
	## END: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
	#####################################################################

print<<EOM;
		</table>
	</td></tr>
</table>
EOM

} # END LOCATION = game_select
#####################################################################
## END: LOCATION = game_select
#####################################################################



#################################################################################
## START: LOCATION = start_game_staff_as_children
#################################################################################
if ($location eq 'score_game_staff_as_children') {
	my $correct_answers = "";
	my $incorrect_answers = "";

print<<EOM;
<h1>Your Score: Photos of SEDL Staff as Children</h1>
EOM

##########################################################################################################################
## START: PARSE DIRECTORY OF STAFF PHOTOS (http://www.sedl.org/images/people/kidos/), SHOWING EACH ON SCREEN WITH A MENU OF STAFF NAMES
##########################################################################################################################
	my %correct_answers;
		$correct_answers{'photo_100'} = 'cmoses';
		$correct_answers{'photo_101'} = 'dmartine';
		$correct_answers{'photo_102'} = 'ktimmons';
		$correct_answers{'photo_103'} = 'lwood';
		$correct_answers{'photo_104'} = 'nreynold';
		$correct_answers{'photo_105'} = 'hwilliam';
		$correct_answers{'photo_106'} = 'blitke';
		$correct_answers{'photo_107'} = 'jburnisk';
		$correct_answers{'photo_108'} = 'ewaters';
		$correct_answers{'photo_109'} = 'macuna';

		$correct_answers{'photo_110'} = 'leo';
		$correct_answers{'photo_111'} = 'cchapman';
		$correct_answers{'photo_112'} = 'dlewis';
		$correct_answers{'photo_113'} = 'dmeibaum';
		$correct_answers{'photo_114'} = 'gcopelan';
		$correct_answers{'photo_115'} = 'lmeadows';
		$correct_answers{'photo_116'} = 'sjoyner';
		$correct_answers{'photo_117'} = 'whoover';
		$correct_answers{'photo_118'} = 'jstarks';
		$correct_answers{'photo_119'} = 'jlumbley';

		$correct_answers{'photo_120'} = 'sabdulla';
		$correct_answers{'photo_121'} = '';
		$correct_answers{'photo_122'} = '';
		$correct_answers{'photo_123'} = '';
		$correct_answers{'photo_124'} = '';
		$correct_answers{'photo_125'} = '';
		$correct_answers{'photo_126'} = '';
		$correct_answers{'photo_127'} = '';
		$correct_answers{'photo_128'} = '';
		$correct_answers{'photo_129'} = '';

		$correct_answers{'photo_130'} = '';
		$correct_answers{'photo_131'} = '';
		$correct_answers{'photo_132'} = '';
		$correct_answers{'photo_133'} = '';
		$correct_answers{'photo_134'} = '';
		$correct_answers{'photo_135'} = '';
		$correct_answers{'photo_136'} = '';
		$correct_answers{'photo_137'} = '';
		$correct_answers{'photo_138'} = '';
		$correct_answers{'photo_139'} = '';

		$correct_answers{'photo_140'} = '';
		$correct_answers{'photo_141'} = '';
		$correct_answers{'photo_142'} = '';
		$correct_answers{'photo_143'} = '';
		$correct_answers{'photo_144'} = '';
		$correct_answers{'photo_145'} = '';
		$correct_answers{'photo_146'} = '';
		$correct_answers{'photo_147'} = '';
		$correct_answers{'photo_148'} = '';
		$correct_answers{'photo_149'} = '';

		$correct_answers{'photo_150'} = '';
		$correct_answers{'photo_151'} = '';
		$correct_answers{'photo_152'} = '';
		$correct_answers{'photo_153'} = '';
		$correct_answers{'photo_154'} = '';
		$correct_answers{'photo_155'} = '';
		$correct_answers{'photo_156'} = '';
		$correct_answers{'photo_157'} = '';
		$correct_answers{'photo_158'} = '';
		$correct_answers{'photo_159'} = '';

		$correct_answers{'photo_160'} = '';
		$correct_answers{'photo_161'} = '';
		$correct_answers{'photo_162'} = '';
		$correct_answers{'photo_163'} = '';
		$correct_answers{'photo_164'} = '';
		$correct_answers{'photo_165'} = '';
		$correct_answers{'photo_166'} = '';
		$correct_answers{'photo_167'} = '';
		$correct_answers{'photo_168'} = '';
		$correct_answers{'photo_169'} = '';

		$correct_answers{'photo_170'} = '';
		$correct_answers{'photo_171'} = '';
		$correct_answers{'photo_172'} = '';
		$correct_answers{'photo_173'} = '';
		$correct_answers{'photo_174'} = '';
		$correct_answers{'photo_175'} = '';
		$correct_answers{'photo_176'} = '';
		$correct_answers{'photo_177'} = '';
		$correct_answers{'photo_178'} = '';
		$correct_answers{'photo_179'} = '';

		$correct_answers{'photo_180'} = '';
		$correct_answers{'photo_181'} = '';
		$correct_answers{'photo_182'} = '';
		$correct_answers{'photo_183'} = '';
		$correct_answers{'photo_184'} = '';
		$correct_answers{'photo_185'} = '';
		$correct_answers{'photo_186'} = '';
		$correct_answers{'photo_187'} = '';
		$correct_answers{'photo_188'} = '';
		$correct_answers{'photo_189'} = '';

	my @listofimages = "";

	##################################################################################
	## START: OPEN DIRECTORY, READ USER GUESS FOR EACH PHOTO
	##################################################################################
	opendir(DIR, "/home/httpd/html/images/people/kidos/");
	my @files = readdir(DIR);
	my $numerofarrayitems = @files;
	my $counter = "0";
	my $num_guesses = 0;
	my $num_guesses_correct = 0;
	my $num_guesses_incorrect = 0;

	while ($counter <= $numerofarrayitems) {
		if ($files[$counter] =~ 'jpg') {
			my $photo_id = $files[$counter];
				$photo_id =~ s/\.jpg//gi;
			my $photo_variable = $photo_id;
			my $this_guess = $query->param("photo_$photo_variable");
#			print "<br>You guessed $this_guess for photo number $photo_variable";
			my $answer_full_name = $correct_answers{"photo_$photo_variable"};
				$answer_full_name = $staff_names{$answer_full_name};
			my $answer_staff_id = $correct_answers{"photo_$photo_variable"};

			## INCREMENT COUNTER OF CORRECT GUESSES
#			print "<br>Does $this_guess = $correct_answers{\"photo_$photo_variable\"}\?";
			my $staff_page_location = "/pubs/catalog/authors/$answer_staff_id\.html";
			   $staff_page_location = "http://en.wikipedia.org/wiki/Leonardo_DiCaprio" if ($answer_staff_id eq 'leo');
			if ($this_guess eq $correct_answers{"photo_$photo_variable"}) {
				$num_guesses_correct++;
				$correct_answers .= "<li><a href=\"$staff_page_location\" target=\"_blank\" onmouseover=\"Tip('<img src=/images/people/kidos/$photo_id\.jpg>', WIDTH, 142)\" onmouseout=\"UnTip()\">$answer_full_name</a></li>";
			} else {
				$num_guesses_incorrect++;
				$incorrect_answers .= "<li><a href=\"$staff_page_location\" target=\"_blank\" onmouseover=\"Tip('<img src=/images/people/kidos/$photo_id\.jpg>', WIDTH, 142)\" onmouseout=\"UnTip()\">$answer_full_name</a></li>";
			} # END IF

			$num_guesses++; ## INCREMENT COUNTER OF TOTAL ITEMS GUESSABLE
		} # END IF
		$counter++;
	} # END WHILE
	##################################################################################
	## END: OPEN DIRECTORY, READ USER GUESS FOR EACH PHOTO
	##################################################################################

	##################################################################################
	## START: SAVE ANSWERS TO DATABASE
	##################################################################################
	#####################################################################
	## START: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
	#####################################################################
	## SEE IF USER HAD PREIOUS ENTRY, AND REMEMBER THEIR INITIAL SCORE
	my $remember_game_correct_firsttry = 0;
	my $command = "SELECT game_correct_firsttry from staff_photo_matchgame where game_user = '$cookie_ss_staff_id' and game_type = 'Staff as Children'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
			my ($game_correct_firsttry) = @arr;
				$remember_game_correct_firsttry = $game_correct_firsttry;
		}
	$remember_game_correct_firsttry = $num_guesses_correct if ($remember_game_correct_firsttry == 0);
	my $command = "DELETE from staff_photo_matchgame where game_user = '$cookie_ss_staff_id' and game_type = 'Staff as Children'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;

	my $command = "INSERT INTO staff_photo_matchgame VALUES ('', '$date_full_mysql', '$cookie_ss_staff_id', 'Staff as Children', '$num_guesses_correct', '$num_guesses', '$remember_game_correct_firsttry')";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	##################################################################################
	## END: SAVE ANSWERS TO DATABASE
	##################################################################################


##########################################################################################################################
## END: PARSE DIRECTORY OF STAFF PHOTOS (http://www.sedl.org/images/people/kidos/), SHOWING EACH ON SCREEN WITH A MENU OF STAFF NAMES
##########################################################################################################################

$correct_answers = "<li>You got them <strong>ALL WRONG!</strong></li>" if ($correct_answers eq '');
$incorrect_answers = "<li>You got them <strong>ALL CORRECT!</strong></li>" if ($incorrect_answers eq '');

my $the_sound = "http://www.ilovewavs.com/Effects/People/Sound%20Effect%20-%20Ohhhh%20%28Dispappointed%20Crowd%29.wav"; # BOOO
   $the_sound = "http://www.ilovewavs.com/Effects/People/Sound%20Effect%20-%20Clapping.wav" if ($num_guesses_correct > $num_guesses_incorrect); # CLAPPING
print<<EOM;
<script type="text/javascript" src="/common/javascript/wz_tooltip.js"></script>
<p>
You correctly guessed <strong>$num_guesses_correct of $num_guesses</strong> items. Use the back button (retains your previous answers) or click here to <a href="photo_guessing_game.cgi?locaiton=start_game_staff_as_children"><span style="font-size:16px;">play again</span></a>.
</p>
EOM
	if ($num_guesses_correct > $num_guesses_incorrect) {
print<<EOM;
<img boder="1" src="http://24.media.tumblr.com/V3plvX2eRqf977jlJUOeGghwo1_400.gif">
EOM
	}
print<<EOM;
	<EMBED SRC="$the_sound" LOOP="FALSE" HEIGHT="60" WIDTH="144" 
	LOOP="1"
    PLAYCOUNT="1"
	style="margin-left:50px;">

<h2>Correct Identifications:</h2>
<p>Move your mouse over the person's name to see their photo.  Click the name to go to the user's staff profile page.</p>
<ol>
$correct_answers
</ol>

<h2>Incorrect Identifications:</h2>
<ol>
$incorrect_answers
</ol>

<p>
Use the back button (retains your previous answers) or click here to <a href="photo_guessing_game.cgi?locaiton=start_game_staff_as_children"><span style="font-size:16px;">play again</span></a>.
</p>

<h2>High Scores</h2>
		<table border="1" cellpadding="4" cellspacing="0">
EOM

	#####################################################################
	## START: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
	#####################################################################
	my $command = "select * from staff_photo_matchgame order by game_type, game_correct_firsttry DESC, game_date DESC";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	    print "<tr style=\"background-color:#ebebeb;\"><td>Player Name</td><td>Correct Guesses</td><td>Date</td><td>Correct Guesses<br>on First Attempt</td></tr>";
	#$error_message .=  "<P>COMMAND: $command";
	## GET THE RESULTS OF THE QUERY
	while (my @arr = $sth->fetchrow) {
	    my ($game_id, $game_date, $game_user, $game_type, $game_correct, $game_num_questions, $game_correct_firsttry) = @arr;
	    	$game_date = &date2standard($game_date);
	    	$game_user = $staff_names{$game_user};
	    print "<tr><td>$game_user</td><td>$game_correct out of $game_num_questions</td><td>$game_date</td><td align=\"right\">$game_correct_firsttry</td></tr>";
	} # END DB QUERY LOOP
	    print "<tr><td colspan=\"4\">There are no high scores on file yet.</td></tr>" if ($num_matches eq '0');
	#####################################################################
	## END: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
	#####################################################################

print<<EOM;
		</table>
EOM


} # END LOCATION = score_game_staff_as_children
#####################################################################
## END: LOCATION = score_game_staff_as_children
#####################################################################



#################################################################################
## START: LOCATION = start_game_staff_as_children
#################################################################################
if ($location eq 'start_game_staff_as_children') {


print<<EOM;
<h1>Now Playing Game: Photos of SEDL Staff as Children</h1>

<div class="info">Music: You can turn off the music by clicking the pause button on this playbar.<br>
	<EMBED SRC="../images/jeopardy.wav" LOOP="FALSE" HEIGHT="60" WIDTH="144" 
	LOOP="1"
    PLAYCOUNT="1"
	style="margin-left:50px;">
</div>
<br><br>

		<FORM ACTION=/staff/personnel/photo_guessing_game.cgi METHOD=POST>
		<table border="0" cellpadding="8" cellspacing="0">
EOM

##########################################################################################################################
## START: PARSE DIRECTORY OF STAFF PHOTOS (http://www.sedl.org/images/people/kidos/), SHOWING EACH ON SCREEN WITH A MENU OF STAFF NAMES
##########################################################################################################################
	my @listofimages = "";

	##################################################################################
	## START: OPEN DIRECTORY, READ FILE LIST, AND PUSH VALID IMAGES INTO SECOND ARRAY
	##################################################################################
	opendir(DIR, "/home/httpd/html/images/people/kidos/");
	my @files = readdir(DIR);
	my $numerofarrayitems = @files;
	my $nextslot = "0";
	my $nextimagename = "";
	my $counter_label = "1";
	my $counter = "0";

	while ($counter <= $numerofarrayitems) {
		if ($files[$counter] =~ 'jpg') {
			print "<tr><td><img src=\"/images/people/kidos/$files[$counter]\" alt=\"guess who?\"></td>\n";
			my $photo_id = $files[$counter];
				$photo_id =~ s/\.jpg//gi;
			print "<td valign=\"top\">$counter_label. ";
				## SHOW LIST OF STAFF IDs TO SELECT FROM
				print "<select name = \"photo_$photo_id\">\n";
				print "$pullmenu_list_of_staff";
				print "</select>\n";
			print "</td></tr>\n";
			$counter_label++;
		} # END IF
		$counter++;
	} # END WHILE
	##################################################################################
	## END: OPEN DIRECTORY, READ FILE LIST, AND PUSH VALID IMAGES INTO SECOND ARRAY
	##################################################################################
##########################################################################################################################
## END: PARSE DIRECTORY OF STAFF PHOTOS (http://www.sedl.org/images/people/kidos/), SHOWING EACH ON SCREEN WITH A MENU OF STAFF NAMES
##########################################################################################################################


print<<EOM;
	</table>
	<ul>
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="score_game_staff_as_children">
		<INPUT TYPE="SUBMIT" VALUE="Submit">
	</ul>
	</FORM>

EOM


} # END LOCATION = start_game_staff_as_children
#####################################################################
## END: LOCATION = start_game_staff_as_children
#####################################################################



######################################################################################################################################################
######################################################################################################################################################
## SUBROUTINES USED BY THIS SCRIPT
######################################################################################################################################################
######################################################################################################################################################


###########################
## START: PRINT PAGE FOOTER
###########################
print <<EOM;
$htmltail
EOM
###########################
## END: PRINT PAGE FOOTER
###########################

####################################################################
## COOKIE HANDLING SUBROUTINES
####################################################################

sub setCookie {
 my ($name, $val, $exp, $path, $dom, $secure) = @_;
 print "Set-Cookie: ";
 print ("$name=$val; expires=$exp; path=$path; domain=$dom");
 print "; $secure" if defined($secure);
 print "\n";
}

sub getCookies {
 my (%cookies); 
 foreach (split (/; /,$ENV{'HTTP_COOKIE'})){
 my($key) = split(/=/, $_);
 $cookies{$key} = substr($_, index($_, "=")+1);
 ($cookies{$key}, $xxdate, $xxpath, $xxdomain) = split(/\,/,$cookies{$key});

 }
 return(%cookies);
}


sub getCookiesfulldata {
 my (%cookies); 
 foreach (split (/; /,$ENV{'HTTP_COOKIE'})){
 my($key) = split(/=/, $_);
 $cookies{$key} = substr($_, index($_, "=")+1);
# ($cookies{$key}, $xxdate, $xxpath, $xxdomain) = split(/\,/,$cookies{$key});
 }
 return(%cookies);
}


## SAMPLE SETCOOKIE CALLS:
# setCookie ("user", "dbewley", $expdate, $path, $thedomain);
# my(%cookies) = getCookies();


#################################################################
## START SUBROUTINE: CLEAN FOR MYSQL
#################################################################
## THIS SUBROUTINE MAKES SURE ANY SPECIAL CHARACTERS ARE BACKSLASHED BEFORE SENDING A COMMAND TO MYSQL
sub cleanformysql {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\\//g;
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/Ô/\\Ô/g;
   $dirtyitem =~ s/Õ/\\Õ/g;
   $dirtyitem =~ s/"/\\"/g;
	return($dirtyitem);
}
#################################################################
## END SUBROUTINE: CLEAN FOR MYSQL
#################################################################


#################################################################
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub date2standard {
	my $date2transform = $_[0];
	my ($thisyear,$thismonth,$thisdate) = split(/\-/,$date2transform);
	$date2transform = "$thismonth\/$thisdate\/$thisyear";
	$date2transform = "" if $date2transform eq '//';
	return($date2transform);
}
#################################################################
## END SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################



####################################################################
## START: ROUND NUMBER TO X PLACES, WITH OR WITHOUT COMMAS
####################################################################
# EXAMPLE OF USAGE
# $num = &format_number($num, "0","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
sub format_number {
	my $number_unformatted = $_[0];
	   $number_unformatted = 0 if ($number_unformatted eq '');
	   $number_unformatted =~ s/\,//g; # REMOVE COMMA IF ALREADY EXISTS 
	my $decimal_places = $_[1];
		$decimal_places = "2" if ($decimal_places eq '');
	my $commas_included = $_[2];

	my $x = new Number::Format;
	my $number_formatted = $x->format_number($number_unformatted, $decimal_places, $decimal_places);
		if ($commas_included ne 'yes') {
			$number_formatted =~ s/\,//g;
		}
	return($number_formatted);
}
####################################################################
## END: ROUND NUMBER TO X PLACES, WITH OR WITHOUT COMMAS
####################################################################


