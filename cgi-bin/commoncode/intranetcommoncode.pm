package intranetcommoncode;

####################################################################
## START: SUBROUTINE get_side_nav_menu_code (INTRANET SIDE NAVIGATION)
####################################################################
sub get_side_nav_menu_code {
	use DBI;
	my $dsn = "DBI:mysql:database=intranet;host=localhost";

	my $pid = $_[0];

	my $show_s = "";
	my $show_sg = "";
	my $currentpage_leftnav = "";

	#################################################################
	## START: QUERY INTRANET_PAGES DB TO GET THE DATA FOR THIS PAGE
	#################################################################
	my $command = "select intranet_section.is_id, intranet_section_group.isg_id FROM intranet_section, intranet_section_group, intranet_pages 
					WHERE intranet_section.is_id = intranet_section_group.isg_is_id
					AND intranet_section_group.isg_id = intranet_pages.page_isg_id";
	   $command .= " AND intranet_pages.page_id like '$pid'" if ($pid ne '');

	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

	while (my @arr = $sth->fetchrow) {
		my ($is_id, $isg_id) = @arr;
			$show_s = $is_id;
			$show_sg = $isg_id;
	} # END WHILE LOOP
	#################################################################
	## END: QUERY INTRANET_PAGES DB TO GET THE DATA FOR THIS PAGE
	#################################################################

		#################################
		## START: BUILD SIDE NAVIGATION
		#################################
		my %count_items_for_group;
		my %direct_page_link_for_group;
		my $command_count_group_pages = "select intranet_section_group.isg_id, count(intranet_section_group.isg_id),
								intranet_pages.* 
						FROM intranet_section, intranet_section_group, intranet_pages 
						WHERE intranet_section.is_id = intranet_section_group.isg_is_id
						AND intranet_section_group.isg_id = intranet_pages.page_isg_id
						AND intranet_section.is_id like '$show_s'
						GROUP BY intranet_section_group.isg_id
						order by intranet_section_group.isg_seq_num, intranet_pages.page_group_seq";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_count_group_pages) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#		print "<p>COMMAND: $command_count_group_pages<BR><BR>MATCHES: $num_matches</p>";
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		    my ($isg_id, $count_isg_id, $page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav, $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author) = @arr;
#print "<p class=\"info\">$isg_id, $count_isg_id</p>";
			$count_items_for_group{$isg_id} = $count_isg_id;
			$direct_page_link_for_group{$isg_id} = "/cgi-bin/mysql/staff/index.cgi?pid=$page_id";
			$direct_page_link_for_group{$isg_id} = "/cgi-bin/mysql/staff/index.cgi?page=$page_id_text" if ($page_id_text ne '');
			$direct_page_link_for_group{$isg_id} = $page_redirect_tourl if ($page_redirect_tourl ne '');
		} # END DB QUERY LOOP

		my $command = "select intranet_section.is_id, intranet_section.is_id_text, intranet_section.is_title, 
								intranet_section_group.isg_id, intranet_section_group.isg_id_text, intranet_section_group.isg_title, 
								intranet_pages.* 
						FROM intranet_section, intranet_section_group, intranet_pages 
						WHERE intranet_section.is_id = intranet_section_group.isg_is_id
						AND intranet_section_group.isg_id = intranet_pages.page_isg_id
						AND intranet_section.is_id like '$show_s'
						order by intranet_section_group.isg_seq_num, intranet_pages.page_group_seq";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#print "COMMAND: $command <P>MATCHES: $num_matches";
#		$currentpage_leftnav .= "<TABLE BORDER=0 CELLPADDING= 2 CELLSPACING=0>";
		$currentpage_leftnav .= "<div id=\"menu-main\">";

		my $last_isg_id = "";
		my $set_section_title = "no";
		my $inside_active_group_block = "no";
		my $counter_passes = "0";
		
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		    my ($is_id, $is_id_text, $is_title, $isg_id, $isg_id_text, $isg_title, $page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav, $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author) = @arr;
			$counter_passes++;
			## START: SET SECTION TITLE AT TOP OF LEFT NAV
			if (($is_title ne '') && ($set_section_title ne 'yes')) {
#				$currentpage_leftnav .= "<TR><TD COLSPAN=2><H2>Inside...<BR>$section_title</H2></TD></TR>";
				$currentpage_leftnav .= "<a href=\"/cgi-bin/mysql/staff/index.cgi?section=$is_id_text\"><img src=\"/staff/images/sidenav/$is_id.gif\" height=\"40\" width=\"180\" alt=\"Inside... $is_title\" class=\"noBorder decoration\"></a>\n<ul>\n";
				$set_section_title = "yes";
			}
			## END: SET SECTION TITLE AT TOP OF LEFT NAV

			if (($isg_id ne $last_isg_id) && ($count_items_for_group{$isg_id} == 1)) {
				if ($show_sg == $isg_id) {
#					$currentpage_leftnav .= "</li>\n" if ($counter_passes > 1); # SEEMS REDUNDANT 2/15/2012 (BL)
					$currentpage_leftnav .= "<li><a href=\"$direct_page_link_for_group{$isg_id}\" id=\"active_menu\">$isg_title</a>\n	<div id=\"menu-main2\"><ul>\n";
					$inside_active_group_block = "yes";
				} else {
					if ($inside_active_group_block eq 'yes') {
						$currentpage_leftnav .= "	</ul></div></li>\n";
						$inside_active_group_block = "no";
					}
					$currentpage_leftnav .= "<li><a href=\"$direct_page_link_for_group{$isg_id}\">$isg_title</a></li>\n";
					$inside_active_group_block = "no";
				}
			} else {

			## START: PRINT SECTION GROUP, IF NEEDED
			if ($isg_id ne $last_isg_id) {
				if ($show_sg == $isg_id) {
#					$currentpage_leftnav .= "<TR><TD VALIGN=\"TOP\" COLSPAN=2 BGCOLOR=\"#FAF3A5\"><strong><FONT COLOR=\"#B28E39\">$section_group_title</FONT></strong></TD></TR>";
#					$currentpage_leftnav .= "</li>\n" if ($counter_passes > 1); # SEEMS REDUNDANT 2/15/2012 (BL)
					if ($isg_id_text eq '') {
						$currentpage_leftnav .= "<li><a href=\"/cgi-bin/mysql/staff/index.cgi?show_sg=$isg_id\" id=\"active_menu\">$isg_title</a>\n	<div id=\"menu-main2\"><ul>\n";
					} else {
						$currentpage_leftnav .= "<li><a href=\"/cgi-bin/mysql/staff/index.cgi?group=$isg_id_text\" id=\"active_menu\">$isg_title</a>\n	<div id=\"menu-main2\"><ul>\n";
					}
					$inside_active_group_block = "yes";
				} else {
					if ($inside_active_group_block eq 'yes') {
						$currentpage_leftnav .= "	</ul></div></li>\n";
						$inside_active_group_block = "no";
					} else {
#						$currentpage_leftnav .= "</li>\n";
					}
					if ($isg_id_text eq '') {
						$currentpage_leftnav .= "<li><a href=\"/cgi-bin/mysql/staff/index.cgi?show_sg=$isg_id\">$isg_title</a></li>\n";
					} else {
						$currentpage_leftnav .= "<li><a href=\"/cgi-bin/mysql/staff/index.cgi?group=$isg_id_text\">$isg_title</a></li>\n";
					}
					$inside_active_group_block = "no";
				}
			}
			## END: PRINT SECTION GROUP, IF NEEDED

			}


			## START: PRINT GROUP PAGES, IF ANY
			if (($show_sg == $isg_id) && ($page_display_in_leftnav ne 'no')) {
				if ($pid == $page_id) {
#					$currentpage_leftnav .= "<TR><TD VALIGN=\"TOP\" BGCOLOR=\"#FFFDE5\">-</TD><TD VALIGN=\"TOP\" BGCOLOR=\"#FFFDE5\"><em>$temp_page_title</em></TD></TR>";
					if (($page_redirect_tourl ne '') && ($page_redirect_delay == 0)) {
						$currentpage_leftnav .= "	<li><a href=\"$page_redirect_tourl\" class=\"nu\" id=\"active_submenu\">$page_title</a></li>\n";
					} elsif ($page_id_text eq '') {
						$currentpage_leftnav .= "	<li><a href=\"/cgi-bin/mysql/staff/index.cgi?pid=$page_id\" class=\"nu\" id=\"active_submenu\">$page_title</a></li>\n";
					} else {
						$currentpage_leftnav .= "	<li><a href=\"/cgi-bin/mysql/staff/index.cgi?page=$page_id_text\" class=\"nu\" id=\"active_submenu\">$page_title</a></li>\n";
					}
				} else {
#						$currentpage_leftnav .= "<TR><TD VALIGN=\"TOP\" BGCOLOR=\"#FFFDE5\">-</TD><TD VALIGN=\"TOP\" BGCOLOR=\"#FFFDE5\"><a href=\"/cgi-bin/mysql/staff/index.cgi?show_s=$section&show_sg=$section_group\" class=\"nu\">$page_title</a></TD></TR>";
					if (($page_redirect_tourl ne '') && ($page_redirect_delay == 0)) {
						$currentpage_leftnav .= "	<li><a href=\"$page_redirect_tourl\" class=\"nu\">$page_title</a></li>\n";
					} elsif ($page_id_text eq '') {
						$currentpage_leftnav .= "	<li><a href=\"/cgi-bin/mysql/staff/index.cgi?pid=$page_id\" class=\"nu\">$page_title</a></li>\n";
					} else {
						$currentpage_leftnav .= "	<li><a href=\"/cgi-bin/mysql/staff/index.cgi?page=$page_id_text\" class=\"nu\">$page_title</a></li>\n";
					}
				}
			}
			## END: PRINT GROUP PAGES, IF ANY
			$last_isg_id = $isg_id;
		}
		
		if ($inside_active_group_block eq 'yes') {
			$currentpage_leftnav .= "	</ul></div></li>\n";
			$inside_active_group_block = "no";
		}
#		$currentpage_leftnav .= "</TABLE>";
		$currentpage_leftnav .= "</ul>\n</div>\n";
		#################################
		## END: BUILD SIDE NAVIGATION
		#################################


	return($currentpage_leftnav);

}
####################################################################
## END: SUBROUTINE get_side_nav_menu_code
####################################################################

####################################################################
## START: SUBROUTINE printform_stafflist_select 
####################################################################
sub printform_stafflist_select {
	use DBI;
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	my $sortby = $_[2];
		if ($sortby eq 'lastname') {
			$sortby = "lastname, firstname";
		} else {
			# DEFAULT TO A FIRSTNAME SORT
			$sortby = "firstname, lastname";
		}
print<<EOM;
	<select name="name" id="name">
	<option value="">
EOM
		my $command = "select userid, firstname, lastname FROM staff_profiles order by firstname, lastname";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		my ($userid, $firstname, $lastname) = @arr;
			print "<option value=\"$userid\"";
			print " SELECTED" if ($userid eq $previous_selection);
			print ">$firstname $lastname</option>\n";
		} # END DB QUERY LOOP

print "	</select>\n";

}
####################################################################
## END: printform_stafflist_select
####################################################################

1;
