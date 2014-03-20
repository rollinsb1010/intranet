#!/usr/bin/perl

#####################################################################################################
# Copyright 2000 by Southwest Educational Development Laboratory
#
# 2001-10-17  Put into new staff page template and moved to http://www.sedl.org/staff/train/schedule/signup.cgi
# Written by Brian Litke 10-22-2001 
#####################################################################################################
use strict;
use CGI qw/:all/;

## THIS IS A PERL MODULE THAT CHECKS THE SYNTAX OF E-MAIL ADDRESSES
use Mail::CheckUser qw(check_email);
$Mail::CheckUser::Skip_Network_Checks = 1;


my $query = new CGI;
my $debug = $query->param("debug") || "0"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS

my $browser = $ENV{"HTTP_USER_AGENT"};
my $error_message = "";
#	if (($browser =~ 'MSIE') && ($browser =~ 'Mac')) {
#		$error_message = "<P>You are using an incompatible browser (Internet Explorer for Mac).  Please reset your default browser to Safari.  Contact Eric Waters at <A HREF=\"mailto:ewaters\@sedl.org\">ewaters\@sedl.org</A> or x-329 if you need assistance.<P>";
#	}
my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};

## GET THE CURRENT DATE INFO
my $weekday = ""; my $month = ""; my $date = ""; my $time = ""; my $tz = ""; my $year = "";
open(DATE,"/bin/date |");  # pipe output from /bin/date command to perl for reading.
                                  # Use | to actually execute a program 
       while(<DATE>){
            chop;
            ($weekday,$month,$date,$time,$tz,$year) = split(/\s+/);
       }
       close(DATE);

my $todaysdate = "$month $date, $year";



### GET VARIABLES FROM THE FORM

my $recipient_userid = param('recipient_userid');
my $recipient_name = param('recipient_name');
my $sender_userid = param('sender_userid');
my $sender_name = param('sender_name');
my $email_subject = param('email_subject');
my $email_body = param('email_body');
my $email_signature = param('email_signature');
my $sender_email = "$sender_userid\@sedl.org";
my $recipient_email = "$recipient_userid\@sedl.org";
my $from_email = "SIMS\@sedl.org";
my $window_message = param('window_message');

print header;

print<<EOM;

<HTML><BODY onLoad="resizeTo(400,400)">
<table width=100% border=0 cellpadding=20>
<tr><td><center>
$error_message

$window_message.<p>

Thank you.<p>$error_message

<a href="javascript:window.close();">close this window</a>
</center>
</td></tr></table>
</BODY></HTML>
EOM

my $recipient_email_isvalid = "no";

	if ($recipient_userid =~ '\,') {
		# MULTIPLE RECIPIENTS: TEST EACH ONE
		$recipient_userid =~ s/ //g; # REMOVE SPACES IN COMMA-SEPARATED LIST OF E-MAIL ADDRESSES
		
		$recipient_email_isvalid = "yes"; # FORM MULTIPLE E-MAILS DEFAULT TO YES, AND CHANGE TO "no" IF FAILURE
		
		# SPLIT MULTIPLE ADDRESSES INTO UNIQUE ADDRESSES, ALLOWING FOR UP TO 10
		my $this_recipient_userid = $recipient_userid; # MAKE A COPY OF THE MULTIPLE ADDRESSES, SO WE CAN WORK WITH IT
		my @e = "";
		$e[0] = ""; # I'M NOT USING THE "0" ELEMENT OF THE ARRAY - LEAVE IT BLANK
		($e[1], $e[2], $e[3], $e[4], $e[5], $e[6], $e[7], $e[8], $e[9], $e[10]) = split(/\,/,$this_recipient_userid);

		# CHECK SYNTAX OF THE MULTIPLE E-MAIL ADDRESSES
		my $counter = "1";
		while ($counter <= 10) {			
			if ($e[$counter] ne '') {
				$e[$counter] = "$e[$counter]\@sedl.org" if ($e[$counter] !~ '\@'); # Add "sedl.org" to end of user ID, if its not blank and doesn't contain an at sign
				# CHECK VALIDITY OF E-MAIL ADDRESS
				if (check_email($e[$counter])) {
					# GOOD E-MAIL, DO NOTHING
				} else {
					# BAD E-MAIL, SET WARNING
					$recipient_email_isvalid = "no";
				} # END IF/ELSE
			} # END IF
			$counter++;
		} # END WHILE
		$recipient_email = $recipient_userid;
		
	} else {
		# SINGLE RECIPIENT, CHECK ITS E-MAIL STRUCTURE
		$recipient_email =~ s/ //g; # REMOVE SPACES
		$recipient_email_isvalid = "yes" if (check_email($recipient_email));
	}


####################################################################
## START: SEND_EMAIL
####################################################################
	my $mailprog = '/usr/sbin/sendmail -t';
	my $cc_to = 'ewaters@sedl.org';
	my $errors_to = 'ewaters@sedl.org, blitke@sedl.org';
	$sender_email = "SIMS\@sedl.org" if ($sender_email eq '');

$email_body = &cleanaccents2html($email_body);
$email_subject = &cleanaccents2html($email_subject);
$recipient_name = &cleanaccents2html($recipient_name);
	
if ($recipient_email_isvalid eq 'yes') {
 
# START: ONLY SEND IF E-MAIL IS VALID
open(NOTIFY,"| $mailprog");


print NOTIFY <<EOM;
From: $from_email
To: $recipient_email
Bcc: $cc_to
Reply-To: $sender_email
Errors-To: $errors_to
Sender: $sender_email
Subject: $email_subject
X-Mail-Gateway: comment.cgi Mail Gateway 1.0
X-Real-Host-From: $sender_email
MIME-Version: 1.0
Content-type: text/html; charset=iso-8859-1

<HTML><BODY>
<P>
Dear $recipient_name,
<P> 
$email_body
<P>
---------------------------------------------------------------------------------------------------------------------------------<br>
$email_signature

</body></html>
EOM

close(NOTIFY);
} else {
open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: $from_email
To: $errors_to
Bcc: $cc_to
Reply-To: $sender_email
Errors-To: $errors_to
Sender: $sender_email
Subject: UNABLE TO SEND E-MAIL WARNING - $email_subject
X-Mail-Gateway: comment.cgi Mail Gateway 1.0
X-Real-Host-From: $sender_email
MIME-Version: 1.0
Content-type: text/html; charset=iso-8859-1

<HTML><BODY>
<P>
UNABLE TO SEND TO THE ADDRESS ($recipient_email)

ORIGINAL MESSAGE FOLLOWS:




Dear $recipient_name,
<P> 
$email_body
<P>
---------------------------------------------------------------------------------------------------------------------------------<br>
$email_signature

</body></html>
EOM
close(NOTIFY);

}

####################################################################
## END: SEND_EMAIL
####################################################################

sub cleanaccents2html {
my $cleanitem = $_[0];
   $cleanitem =~ s/“/"/g;         
   $cleanitem =~ s/”/"/g;         
   $cleanitem =~ s/’/'/g;         
   $cleanitem =~ s/‘/'/g;
   $cleanitem =~ s// /g;
   $cleanitem =~ s/—/--/g;
   $cleanitem =~ s/ //g; # invisible bullet
   $cleanitem =~ s/À/&Agrave\;/g; 
   $cleanitem =~ s/à/&agrave\;/g;   
   $cleanitem =~ s/Á/&Aacute\;/g;  
   $cleanitem =~ s/á/&aacute\;/g;
   $cleanitem =~ s/Â/&Acirc\;/g;
   $cleanitem =~ s/â/&acirc\;/g;
   $cleanitem =~ s/Ã/&Atilde\;/g;
   $cleanitem =~ s/ã/&atilde\;/g;
   $cleanitem =~ s/Ä/&Auml\;/g;
   $cleanitem =~ s/ä/&auml\;/g;
   $cleanitem =~ s/É/&Eacute\;/g;
   $cleanitem =~ s/é/&eacute\;/g;
   $cleanitem =~ s/È/&Egrave\;/g;
   $cleanitem =~ s/è/&egrave\;/g;
   $cleanitem =~ s/Ê/&Euml\;/g;
   $cleanitem =~ s/ë/&euml\;/g;
   $cleanitem =~ s/Ì/&Igrave\;/g;
   $cleanitem =~ s/ì/&igrave\;/g;
   $cleanitem =~ s/Í/&Iacute\;/g;
   $cleanitem =~ s/í/&iacute\;/g;
   $cleanitem =~ s/Î/&Icirc\;/g;
   $cleanitem =~ s/î/&icirc\;/g;
   $cleanitem =~ s/Ï/&Iuml\;/g;
   $cleanitem =~ s/ï/&iuml\;/g;
   $cleanitem =~ s/Ñ/&Ntilde\;/g;
   $cleanitem =~ s/ñ/&ntilde\;/g;
   $cleanitem =~ s/Ò/&Ograve\;/g;
   $cleanitem =~ s/ò/&ograve\;/g;
   $cleanitem =~ s/Ó/&Oacute\;/g;
   $cleanitem =~ s/ó/&oacute\;/g;
   $cleanitem =~ s/Õ/&Otilde\;/g;
   $cleanitem =~ s/õ/&otilde\;/g;
   $cleanitem =~ s/Ö/&Ouml\;/g;
   $cleanitem =~ s/ö/&ouml\;/g;
   $cleanitem =~ s/Ù/&Ugrave\;/g;
   $cleanitem =~ s/ù/&ugrave\;/g;
   $cleanitem =~ s/Ú/&Uacute\;/g;
   $cleanitem =~ s/ú/&uacute\;/g;
   $cleanitem =~ s/Û/&Ucirc\;/g;  ## THIS REPLACES THE ó FOR SOME REASON
   $cleanitem =~ s/û/&ucirc\;/g;
   $cleanitem =~ s/Ü/&Uuml\;/g;
   $cleanitem =~ s/ü/&uuml\;/g;
   $cleanitem =~ s/ÿ/&yuml\;/g;
   return ($cleanitem);
}

