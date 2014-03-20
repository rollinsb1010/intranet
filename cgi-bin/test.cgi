 #!/usr/bin/perl


use strict;
use CGI qw/:cgi/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
# use lib 'Users/rollinsb/webwork/sedl_intranet/cgi-bin/commoncode/commoncode.pm';
# use commoncode;

 #print "Content-type: text/html\n\n";

 # # print <<HTML;
 # print "<html>\n";
 # print "<head>\n";
 # print "<title>A Simple Perl CGI</title>\n";
 # print "</head>\n";
 # print "<body>\n";
 # print "<h1>A Simple Perl CGI</h1>\n";
 # print "<p>Hello World</p>\n";
 # print "</body>\n";
 # exit(0);

 # print "hello world";

my $htmlhead = "";
my $htmltail = "";

open(HTMLHEAD, "/Users/rollinsb/webwork/sedl_intranet/cgi-bin/includes/header2012.txt");
while (<HTMLHEAD>) {
    $htmlhead .= $_;
}
close(HTMLHEAD);

open(HTMLTAIL, "/Users/rollinsb/webwork/sedl_intranet/cgi-bin/includes/footer2012.txt");
while (<HTMLTAIL>) {
    $htmltail .= $_;
}
close(HTMLTAIL);

my $page_header_info = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html lang=\"en\">
<HEAD>";
## SET SIDE NAVIGATION COLOR
my $bgcolor = "#97B038";

## SET VARIABLES USED TO DRAW HTML ROUNDED-BOXES
my $sidebar_boxtop = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\">
                    <tr><td style=\"background:#ffffff;\"><a href=\"/cgi/staff/index.cgi?location=customize\"><img src=\"/intranet/images/template/sidebar-quicklinks2.gif\" alt=\"my quick links\" class=\"noBorder\"></a></td></tr><tr><td style=\"background:#FFFFFF;\">
                            <table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";
my $sidebar_boxbottom = "</td></tr></table>
                    </td></tr><tr><td valign=\"top\" style=\"background:#97B038\"><img src=\"/staff/images/sidebar-round-bottom-97B038.gif\" class=\"decoration\" alt=\"\"></td></tr></table>";

my $sidebar_boxtop_staff = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td style=\"background:#ffffff;\"><img src=\"Users/rollinsb/webwork/sedl_intranet/intranet/images/template/sidebar-round-top-staff.gif\" class=\"decoration\" alt=\"SEDL Staff\"></td></tr><tr><td style=\"background:#FFFFFF;\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";
my $sidebar_boxtop_login = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td style=\"background:#ffffff;\"><img src=\"Users/rollinsb/webwork/sedl_intranet/intranet/images/template/sidebar-round-top-login.gif\" class=\"decoration\" alt=\"Please Log In\"></td></tr><tr><td style=\"background:#FFFFFF;\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";
my $sidebar_boxtop_sedlstar = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td style=\"background:#ffffff;\"><img src=\"Users/rollinsb/webwork/sedl_intranet/intranet/images/template/sidebar-round-top-sedlstar.gif\" class=\"decoration\" alt=\"SEDL Star of the Month\"></td></tr><tr><td style=\"background:#FFFFFF;\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";
my $sidebar_boxtop_pressreleases = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td><a href=\"/new/media.html\"><img src=\"Users/rollinsb/webwork/sedl_intranet/intranet/images/template/sidebar-round-top-press.gif\" class=\"decoration noBorder\" alt=\"SEDL Press Releases\"></a></td></tr><tr><td style=\"background:#FFFFFF;\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";
my $sidebar_boxtop_suggestionbox = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td><a href=\"http://www.sedl.org/staff/communications/suggestion_box.cgi\"><img src=\"/staff/images/template/sidebar-boxtop_suggestionbox.gif\" alt=\"SEDL Suggestion Box\" class=\"noBorder decoration\"></a></td></tr><tr><td style=\"background:#FFFFFF;\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";

my $location_admin_script = "Users/rollinsb/webwork/sedl_intranet/cgi-bin/communications/intranet_page_manager.cgi";
###########################################
# END: GRAB SEDL HEADER AND FOOTER HTML #
###########################################
