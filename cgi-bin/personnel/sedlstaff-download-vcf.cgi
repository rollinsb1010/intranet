#!/usr/bin/perl

################################################################################
# Copyright 2008 by SEDL
# Written by Brian Litke, SEDL Web Administrator (09-17-2008)
#
#
# This script is used to force a download of the staff vcf file (e-mail addresses for Mac Address Boox)
################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);

my $destination_filename = "sedlstaff.vcf";
my $file_data = "";
open(DATAFILE_TOSTREAM,"</home/httpd/html/staff/personnel/sedlstaff.vcf");
while (<DATAFILE_TOSTREAM>) {
	$file_data .= $_;
}
close(DATAFILE_TOSTREAM);


print "Content-type: application/octet-stream\n";
print "Content-Disposition: filename=\"$destination_filename\n\n";
print $file_data;
