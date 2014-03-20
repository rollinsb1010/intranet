package staffprofiles_shared_functions;

########################################################################
## START: SUBROUTINE print_additional_numbers
########################################################################
sub print_additional_numbers {

my $table_heading_text = "
<table border=\"1\" cellpadding=\"1\" cellspacing=\"0\" style=\"width:94%;\">
<tr style=\"background-color:#ebebeb;\">
	<th><strong>Description</strong></th>
	<th><strong>Room</strong></th>
	<th align=\"center\"><strong>Phone</strong></th>
</tr>
";

print<<EOM;
<div class="padding15_no_print_padding">
<span style="font-size:12px;"><strong>SEDL Staff Directory - Additional Numbers</strong></span><br>

<table border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-top:2px;">
<tr><td valign="top" style="width:50%;text-align:center;">

$table_heading_text
<tr><td>Analog/Polycom - Conf. Center</td>
	<td style="text-align:center;">108 (W. wall)</td>
	<td style="text-align:center;">391-6605</td></tr>
<tr><td>Analog/Polycom - Board Rm.</td>
	<td style="text-align:center;">201 (floor)</td>
	<td style="text-align:center;">5539</td></tr>
<tr><td>Analog/Polycom - Hindsman</td>
	<td style="text-align:center;">233 (table)</td>
	<td style="text-align:center;">5536</td></tr>
<tr><td>Analog/Polycom - Kronkosky</td>
	<td style="text-align:center;">325 (table)</td>
	<td style="text-align:center;">5540</td></tr>
<tr><td>Analog/Polycom - Training Rm.</td>
	<td style="text-align:center;">307 (floor)</td>
	<td style="text-align:center;">5544</td></tr>
<tr><td>Conference Center</td>
	<td style="text-align:center;">108 (N. wall)</td>
	<td style="text-align:center;">512-391-6655</td></tr>
<tr><td>Conference Center</td>
	<td style="text-align:center;">108 (S. wall)</td>
	<td style="text-align:center;">512-391-6656</td></tr>
<tr><td>Conference Rm.</td>
	<td style="text-align:center;">117</td>
	<td style="text-align:center;">512-391-6657</td></tr>
<tr><td>Conference Rm. - Board</td>
	<td style="text-align:center;">201 (wall)</td>
	<td style="text-align:center;">512-391-6659</td></tr>
<tr><td>Conference Rm.</td>
	<td style="text-align:center;">224 (table)</td>
	<td style="text-align:center;">512-391-6660</td></tr>
<tr><td>Conference Rm.</td>
	<td style="text-align:center;">224 (wall)</td>
	<td style="text-align:center;">512-391-6661</td></tr>
<tr><td>Conference Rm. - Hindsman</td>
	<td style="text-align:center;">233 (table)</td>
	<td style="text-align:center;">512-391-6658</td></tr>
<tr><td>Conference Rm.</td>
	<td style="text-align:center;">248 (table)</td>
	<td style="text-align:center;">512-391-6662</td></tr>
<tr><td>Conference Rm. - Training</td>
	<td style="text-align:center;">307</td>
	<td style="text-align:center;">512-391-6663</td></tr>
<tr><td>Conference Rm.</td>
	<td style="text-align:center;">316 (table)</td>
	<td style="text-align:center;">512-391-6664</td></tr>
<tr><td>Conference Rm.</td>
	<td style="text-align:center;">316 (wall)</td>
	<td style="text-align:center;">512-391-6665</td></tr>
<tr><td>Conference Rm. - Kronkosky</td>
	<td style="text-align:center;">325 (table)</td>
	<td style="text-align:center;">512-391-6666</td></tr>
<tr><td>Conference Rm.</td>
	<td style="text-align:center;">340 (table)</td>
	<td style="text-align:center;">512-391-6668</td></tr>
<tr><td>Conference Rm.</td>
	<td style="text-align:center;">340 (wall)</td>
	<td style="text-align:center;">512-391-6667</td></tr>
<tr><td>Conference Rm.</td>
	<td style="text-align:center;">349 (table)</td>
	<td style="text-align:center;">512-391-6669</td></tr>
<tr><td>Conference Rm.</td>
	<td style="text-align:center;">349 (wall)</td>
	<td style="text-align:center;">512-391-6670</td></tr>
<tr><td>FAX - In - Austin</td>
	<td style="text-align:center;">130</td>
	<td style="text-align:center;">512-476-2286</td></tr>
</table>

</td>
<td valign=\"top\" style=\"width:50%;text-align:center;\">

$table_heading_text
<tr><td>FAX - Metairie</td><td style="text-align:center;">&nbsp;</td><td style="text-align:center;">504-831-5242</td></tr>
<tr><td>FAX - Alabama<sup>1</sup></td><td style="text-align:center;">&nbsp;</td><td style="text-align:center;">256-757-7790</td></tr>
<tr><td>FAX - Robin Jarvis<sup>2</sup></td><td style="text-align:center;">BR</td><td style="text-align:center;">225-257-4991</td></tr>
<tr><td>FAX - Robyn Madison-Harris<sup>2</sup></td><td style="text-align:center;">BR</td><td style="text-align:center;">225-751-8323</td></tr>
<tr><td>FAX - Georgia<sup>3</sup></td><td style="text-align:center;">&nbsp;</td><td style="text-align:center;">770-432-4272</td></tr>
<tr><td>FAX - Mississippi<sup>4</sup></td><td style="text-align:center;">&nbsp;</td><td style="text-align:center;">601-605-2226</td></tr>
<tr><td>FAX - Out - Austin 1st Floor</td><td style="text-align:center;">130</td><td style="text-align:center;">5519</td></tr>
<tr><td>FAX - Out - Austin 2nd Floor</td><td style="text-align:center;">251</td><td style="text-align:center;">5546</td></tr>
<tr><td>FAX - Out - Austin 3rd Floor</td><td style="text-align:center;">343</td><td style="text-align:center;">5547</td></tr>
<tr><td>Hoteling - 1st Floor</td><td style="text-align:center;">105</td><td style="text-align:center;">5520</td></tr>
<tr><td>Hoteling - 1st Floor</td><td style="text-align:center;">107</td><td style="text-align:center;">5521</td></tr>
<tr><td>Hoteling - 2nd Floor</td><td style="text-align:center;">204</td><td style="text-align:center;">5527</td></tr>
<tr><td>Hoteling - 2nd Floor</td><td style="text-align:center;">205</td><td style="text-align:center;">5526</td></tr>
<tr><td>Library</td><td style="text-align:center;">216</td><td style="text-align:center;">5528</td></tr>
<tr><td>Lobby Waiting Area - 1st Floor</td><td style="text-align:center;">&nbsp;</td><td style="text-align:center;">5522</td></tr>
<tr><td>Print/Copy - 2nd Floor</td><td style="text-align:center;">251</td><td style="text-align:center;">5524</td></tr>
<tr><td>Print/Copy - 3rd Floor</td><td style="text-align:center;">343</td><td style="text-align:center;">5530</td></tr>
<tr><td>Publications</td><td style="text-align:center;">129</td><td style="text-align:center;">5517</td></tr>
<tr><td>Security - Emerald PI</td><td style="text-align:center;">Lobby</td><td style="text-align:center;">512-552-5522</td></tr>
<tr><td>Server/Switchroom</td><td style="text-align:center;">135-136</td><td style="text-align:center;">512-391-6599</td></tr>
<tr><td>Staff Lounge</td><td style="text-align:center;">301</td><td style="text-align:center;">5531</td></tr>
<tr><td>Toll Free - Austin</td><td style="text-align:center;">&nbsp;</td><td style="text-align:center;">800-476-6861</td></tr>
<tr><td>Toll Free - Metairie</td><td style="text-align:center;">&nbsp;</td><td style="text-align:center;">800-644-8671</td></tr>
<tr><td>Toll Free - NCDDR</td><td style="text-align:center;">&nbsp;</td><td style="text-align:center;">800-266-1832</td></tr>
<tr><td>Toll Free - VR-AUTISM</td><td style="text-align:center;">&nbsp;</td><td style="text-align:center;">800-761-7874</td></tr>
<tr><td>TDD</td><td style="text-align:center;">140</td><td style="text-align:center;">512-391-6578</td></tr>
<tr><td>Video Conferencing</td><td style="text-align:center;">152</td><td style="text-align:center;">5555</td></tr>
</table>

</td></tr>
</table>
<p>
1 Alabama Staff: TBH<br>
2 Baton Rouge Staff: Robin Jarvis, Robyn Madison-Harris<br>
3 Georgia Staff: Glenda Copeland<br>
4 Mississippi Staff: Camille Chapman, Debra Meibaum
</p>
<p>
<strong>Remote Access to Voicemail for Austin Staff:</strong>
</p>

<ul>
	<li>From the <strong>local calling area</strong>, dial your own DID (Direct Inward Dial) number and press # during or after the message, enter your User ID (your 4-digit phone extension), and enter your password.</li>
	<li>From a <strong>long distance calling area, <u>during</u> normal working hours</strong> (8:00 AM-5:00 PM Central), for <strong>toll free access</strong> to your voicemail, dial 800-476-6861, request your phone extension, then press # during or after the message, enter your User ID (your 4-digit phone extension), and enter your password.</li>
	<li>From a <strong>long distance calling area, <u>outside</u> normal working hours</strong>, for <strong>toll free access</strong> to your voicemail, dial 800-476-6861, dial your phone extension, then press # during or after the message, enter your User ID (your 4-digit phone extension), and enter your password.</li>
</ul>

<table width="100%">
<tr><td width="46%">SEDL Austin Headquarters<br>
		4700 Mueller Blvd.<br>
		Austin, TX 78723-3081<br>
		512-476-6861
	</td>
	<td width="8%"></td>
	<td width="46%" style="text-align:right;">SEDL Metairie Office<br>
		3501 North Causeway Blvd., Suite 700<br>
		Metairie, LA 70002-3664<br>
		504-838-6861
	</td>
</tr>
</table>
</div>
EOM
}
########################################################################
## END: SUBROUTINE print_additional_numbers
########################################################################

1;