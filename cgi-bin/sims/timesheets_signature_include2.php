<?php
session_start();
?>

<!--BEGIN FIFTH SECTION: APPROVAL SIGNATURES-->

<?php
		
$signature_debug = "off"; //change to "on" to print variable values at top of signature section		
		
		
		
		$default_signers[0] = $_SESSION['signer_ID_owner'];
		$default_signers[1] = $_SESSION['signer_ID_imm_spvsr'];
		$default_signers[2] = $_SESSION['signer_ID_pba'];
		
		$default_signers = array_unique($default_signers);
		
		$all_signers = array_merge($default_signers,$bgt_auths);
											
		$unique_signers = array_unique($all_signers);
		$total_signers = count($unique_signers);
		$_SESSION['total_signers_count'] = $total_signers;
		
		$other_signers = $unique_signers;
		
		$key1 = array_search($_SESSION['signer_ID_imm_spvsr'], $other_signers);
		if(in_array($_SESSION['signer_ID_imm_spvsr'], $other_signers)){
		//if(isset($key1)){
		unset($other_signers[$key1]);
		$other_signers = array_values($other_signers);
		}
		$other_signers_no_IS = $other_signers;

		$key2 = array_search($_SESSION['signer_ID_owner'], $other_signers);
		if(in_array($_SESSION['signer_ID_owner'], $other_signers)){
		//if(isset($key2)){
		unset($other_signers[$key2]);
		
		$other_signers = array_values($other_signers);
		}
		$_SESSION['other_signers'] = $other_signers;
		$other_signers_no_SM = $other_signers;

		$total_other_signers = count($other_signers);
		$_SESSION['total_other_signers'] = $total_other_signers;

		$other_signers2 = $other_signers;
		$key3 = array_search($_SESSION['signer_ID_pba'], $other_signers2);
		if(in_array($_SESSION['signer_ID_pba'], $other_signers2)){
		//if(isset($key3)){
		unset($other_signers2[$key3]);
		$key3_status = 'set:'.$key3;
		$other_signers2 = array_values($other_signers2); //SET ARRAY TO OTHER SIGNERS BESIDES IMM SPVSR AND PBA AND OWNER
		}
		$_SESSION['other_signers2'] = $other_signers2;
		$other_signers_no_PBA = $other_signers2;

		$total_other_signers2 = count($other_signers2);
		$_SESSION['total_other_signers2'] = $total_other_signers2;


		
		$_SESSION['bgt_auths'] = $bgt_auths;
		
		foreach($unique_signers as $current){
		$signatures_required .= ','.$current; 
		}
		
		$_SESSION['signatures_required'] = $signatures_required;
		
		
		
		foreach($other_signers as $current){
		$signatures_required_oba .= ','.$current; 
		}

?>
							
<tr><td class="section_head" colspan="<?php echo $header_colspan;?>"><strong>Signatures</strong><div style="float:right;text-align:right"><font color="red">Last day to make changes to this timesheet: <strong><?php echo $_SESSION['pay_period_lockout_date'];?></strong></font></div></td></tr>
<tr><td colspan="<?php echo $header_colspan;?>">
	<table>
		
		<tr valign="bottom">								
									
<!--BEGIN: STAFF SIGNATURE-->									


<?php if($today_stamp > $lockout_day_stamp){ //IF THE TIMESHEET IS LOCKED ?>

			

			<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff"><?php if($_SESSION['signer_status_owner'] == 1){ ?><img src="/staff/sims/signatures/<?php echo $_SESSION['signer_ID_owner'];?>.png"><?php } else { ?><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_rpt_out_empl.php?Timesheet_ID=<?php echo $_SESSION['timesheet_ID'];?>&action=view" onclick="return lockoutMessage()">Sign Timesheet</a><p><?php } ?><p><span class="tiny">Staff Member<br><font color="999999">[<?php echo $_SESSION['signer_timestamp_owner'];?>]</font></span></td>



<?php }elseif($today_stamp <= $lockout_day_stamp){ //IF THE TIMESHEET IS NOT LOCKED ?>

	
	
	
			<?php if($_SESSION['signer_status_owner'] != 1) { //IF THE TIMESHEET HAS NOT BEEN SIGNED YET ?>
				
				
				
				
						<?php if($recordData['timesheets::RptOutsideEmplFormSigned'][0] != 1) { //IF THE REPORT OF OUTSIDE EMPLOYMENT FORM HAS NOT BEEN SIGNED YET ?>
					
					
								<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff"><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_rpt_out_empl.php?Timesheet_ID=<?php echo $_SESSION['timesheet_ID'];?>&action=view" onclick="return oefUnsigned()" title="Click here to sign this timesheet."><?php echo $_SESSION['signer_ID_owner'];?></a><p><span class="tiny">Staff Member</span></td>
				
						
						<?php } elseif(round($AllHrsT_total,1) != round($_SESSION['payperiod_workhrs'],1) && ($_SESSION['employee_type_owner'] == 'Exempt') && ($_SESSION['new_employee_status'] == 'no') && ($_SESSION['allow_variable_timesheet_hrs'] != 'Yes')) { //IF THE TOTAL TIMESHEET HOURS DON'T MATCH THE HOURS ENTERED AND THE TIMESHEET OWNER IS EXEMPT ?>
						
								
								<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff"><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheet_process.php" onclick="return invalidHours()" title="Click here to sign this timesheet."><?php echo $_SESSION['signer_ID_owner'];?></a><p><span class="tiny">Staff Member</span></td>
						

						<?php } elseif(($_SESSION['blank_rows_check'] > 0)&&($_SESSION['AllHrsT_total'] != $_SESSION['payperiod_workhrs'])) { //IF THERE ARE BLANK ROWS ON THE TIMESHEET AND TIMESHEET DOES NOT HAVE ALL LEAVE HRS ?>
						
								
								<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff"><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheet_process.php" onclick="return blankRows()" title="Click here to sign this timesheet."><?php echo $_SESSION['signer_ID_owner'];?></a><p><span class="tiny">Staff Member</span></td>

						
						<?php } elseif($recordData['timesheets::c_timesheet_has_multiple_lv_type_rows'][0] == 1) { //IF THERE ARE PD LEAVE ROWS WITH DUPLICATE LV TYPES ON THE TIMESHEET ?>
						
								
								<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff"><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheet_process.php" onclick="return multipleLeavetypes()" title="Click here to sign this timesheet."><?php echo $_SESSION['signer_ID_owner'];?></a><p><span class="tiny">Staff Member</span></td>

						<?php } else { //IF TIMESHEET PASSES VALIDATION ?>
						
								<?php if($_SESSION['timesheet_owner_is_admin'] == '1'){ //IF THE TIMESHEET OWNER IS A TIME/LEAVE ADMIN ?>

									<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff"><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process.php?action=staff_sign_ar&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" onclick="return confirmSign()" title="Click here to sign this timesheet."><?php echo $_SESSION['signer_ID_owner'];?></a><p><span class="tiny">Staff Member</span></td>

								<?php } else { //IF THE TIMESHEET OWNER IS NOT A TIME/LEAVE ADMIN ?>

									<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff"><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process.php?action=staff_sign&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" onclick="return confirmSign()" title="Click here to sign this timesheet."><?php echo $_SESSION['signer_ID_owner'];?></a><p><span class="tiny">Staff Member</span></td>
								
								<?php } ?>
						
						<?php } ?>
			
			


			<?php } elseif($_SESSION['signer_status_owner'] == 1) { //IF THE TIMESHEET HAS BEEN SIGNED ?>

			

			
						<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff"><img src="/staff/sims/signatures/<?php echo $_SESSION['signer_ID_owner'];?>.png"><p><span class="tiny">Staff Member<br><font color="999999">[<?php echo $_SESSION['signer_timestamp_owner'];?>]</font></span></td>


			
			
			<?php } else { ?>

			

						<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff">Error</td>


			
			<?php } ?>




<?php } ?>
									
									

<!--END: STAFF SIGNATURE-->									

<?php if($_SESSION['timesheet_approval_not_required'] != '1') { //IF ADDITIONAL APPROVALS ARE REQUIRED OTHER THAN CEO - (CEO SELF-CHECK) ?>									
									

<!--BEGIN: IMMEDIATE SUPERVISOR / PRIMARY BUDGET AUTHORITY SIGNATURE-->									
									
			<?php if($_SESSION['signer_pba_is_spvsr'] == 1) { //IF THE TIMESHEET OWNER'S PRIMARY BGT AUTH AND IMMEDIATE SPVSR IS THE SAME PERSON ?>									
												
			
						<?php if(($_SESSION['signer_status_pba'] != 1) && ($_SESSION['current_submitted_status'] != 'Revised')) { //IF THE TIMESHEET OWNER'S PRIMARY BGT AUTH HAS NOT SIGNED THE TIMESHEET YET AND THE TIMESHEET HAS NOT BEEN REVISED ?>
							
							
							<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap>
							
							<a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process.php?action=pba_sign&bgt_auth=<?php echo $_SESSION['signer_ID_pba'];?>&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" 
							<?php if($signature_status != 'locked'){?>
							
							<?php if($_SESSION['signer_ID_pba'] == $_SESSION['user_ID']){?>
							
								<?php if(($_SESSION['oba_approvals_complete'] == '0') && ($_SESSION['approved_by_auth_rep_status'] == '1')){?>onclick="return confirmSign()"
								
								<?php }elseif(($_SESSION['oba_approvals_complete'] == '0') && ($_SESSION['approved_by_auth_rep_status'] == '')){ ?>onclick="return confirmSign2()"
								
								<?php }else{ ?>onclick="return baMessage2()"<?php }?>
								
								<?php }else{ ?>onclick="return baMessage()"<?php }?>
								
								<?php }else{ ?>onclick="return arMessage()"<?php }?>
							
							
							title="Click here to sign this timesheet."><?php echo $_SESSION['signer_ID_pba'];?></a><p><span class="tiny">Primary Budget Authority</span></td>
									
									
						<?php } elseif(($_SESSION['signer_status_pba'] != 1) && ($_SESSION['current_submitted_status'] == 'Revised')) { //IF THE TIMESHEET OWNER'S PRIMARY BGT AUTH HAS NOT SIGNED THE TIMESHEET YET AND THE TIMESHEET HAS BEEN REVISED ?>
							
							
							<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process_revised.php?action=pba_sign&bgt_auth=<?php echo $_SESSION['signer_ID_pba'];?>&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" <?php if($signature_status != 'locked'){?><?php if($_SESSION['signer_ID_pba'] == $_SESSION['user_ID']){?><?php if($_SESSION['oba_approvals_complete'] == '0'){?>onclick="return confirmSign()"<?php }else{ ?>onclick="return baMessage2()"<?php }?><?php }else{ ?>onclick="return baMessage()"<?php }?><?php }else{ ?>onclick="return arMessage()"<?php }?> title="Click here to sign this timesheet."><?php echo $_SESSION['signer_ID_pba'];?></a><p><span class="tiny">Primary Budget Authority</span></td>
					
						
						
						
						<?php } elseif($_SESSION['signer_status_pba'] == 1) { //IF THE TIMESHEET OWNER'S PRIMARY BGT AUTH HAS ALREADY SIGNED THE TIMESHEET ?>
						
						
						
						
									<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><img src="/staff/sims/signatures/<?php echo $_SESSION['signer_ID_pba'];?>.png"><p><span class="tiny">Primary Budget Authority<br><font color="999999">[<?php echo $_SESSION['signer_timestamp_pba'];?>]</font></span></td>
						
						
						
						
						<?php } else { ?>
						
						
						
									<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff">Error_1</td>
						
						
						
						<?php } ?>
			
												
												
			<?php } else { //IF THE TIMESHEET OWNER'S PRIMARY BGT AUTH AND IMMEDIATE SPVSR IS NOT THE SAME PERSON ?>
			
			
						<?php if(($_SESSION['signer_status_imm_spvsr'] != 1) && ($_SESSION['current_submitted_status'] != 'Revised')) { //IF THE TIMESHEET OWNER'S IMMEDIATE SUPERVISOR HAS NOT SIGNED THE TIMESHEET YET AND THE TIMESHEET HAS NOT BEEN REVISED ?>
							
							
							<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process.php?action=imm_spvsr_sign&bgt_auth=<?php echo $_SESSION['signer_ID_imm_spvsr'];?>&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" <?php if($signature_status != 'locked'){?><?php if($_SESSION['signer_ID_imm_spvsr'] == $_SESSION['user_ID']){?>onclick="return confirmSign()"<?php }else{ ?>onclick="return baMessage()"<?php }?><?php }else{ ?>onclick="return arMessage()"<?php }?> title="Click here to sign this timesheet."><?php echo $_SESSION['signer_ID_imm_spvsr'];?></a><p><span class="tiny">Immediate Supervisor</span></td>
									
									
						
						<?php } elseif(($_SESSION['signer_status_imm_spvsr'] != 1) && ($_SESSION['current_submitted_status'] == 'Revised')) { //IF THE TIMESHEET OWNER'S IMMEDIATE SUPERVISOR HAS NOT SIGNED THE TIMESHEET YET AND THE TIMESHEET HAS BEEN REVISED ?>
							
							
							<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process_revised.php?action=imm_spvsr_sign&bgt_auth=<?php echo $_SESSION['signer_ID_imm_spvsr'];?>&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" <?php if($signature_status != 'locked'){?><?php if($_SESSION['signer_ID_imm_spvsr'] == $_SESSION['user_ID']){?>onclick="return confirmSign()"<?php }else{ ?>onclick="return baMessage()"<?php }?><?php }else{ ?>onclick="return arMessage()"<?php }?> title="Click here to sign this timesheet."><?php echo $_SESSION['signer_ID_imm_spvsr'];?></a><p><span class="tiny">Immediate Supervisor</span></td>
						
						
						
						<?php } elseif($_SESSION['signer_status_imm_spvsr'] == 1) { //IF THE TIMESHEET OWNER'S IMMEDIATE SUPERVISOR HAS ALREADY SIGNED THE TIMESHEET ?>
						
						
						
						
									<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><img src="/staff/sims/signatures/<?php echo $_SESSION['signer_ID_imm_spvsr'];?>.png"><p><span class="tiny">Immediate Supervisor<br><font color="999999">[<?php echo $_SESSION['signer_timestamp_imm_spvsr'];?>]</font></span></td>
						
						
						
						
						<?php } else { ?>
						
						
						
									<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff">Error_1</td>
						
						
						
						<?php } ?>
			
			
			
			<?php } ?>
			
			
			
<!--END: IMMEDIATE SUPERVISOR / PRIMARY BUDGET AUTHORITY SIGNATURE-->


<!--BEGIN: BUDGET AUTHORITY SIGNATURES-->
			<?php if($_SESSION['signer_status_owner'] == '1'){ 

							$timesheet_row_ID = $_SESSION['timesheet_row_ID']; // GRAB TIMESHEET ROW_ID FOR PBA IF NO HRS ARE CHARGED TO PBA ?>
			
												
					<?php if(count($other_signers) > 0){ 
														


							$i=1;
														
							foreach($other_signers as $current){ 
														
															
							$search = new FX($serverIP,$webCompanionPort);
							$search -> SetDBData('SIMS_2.fp7','time_hrs');
							$search -> SetDBPassword($webPW,$webUN);
							$search -> AddDBParam('Timesheet_ID',$_SESSION['timesheet_ID']);
							$search -> AddDBParam('BudgetAuthorityLocal',$current);
							
							$searchResult = $search -> FMFind();
							
							//echo $searchResult['errorCode'];
							//echo $searchResult['foundCount'];
							$recordData = current($searchResult['data']);
							
							
					?>
															
												<?php if($_SESSION['current_submitted_status'] != 'Revised'){ //IF THE TIMESHEET HAS NOT BEEN REVISED
															
															
															if($current == $recordData['timesheets::Signer_ID_pba'][0] && (($recordData['c_hrs_approved_bgt_auth_all'][0] > '0') || (($recordData['timesheets::c_cwp_pba_is_spvsr'][0] == '0') && ($recordData['timesheets::Signer_status_pba'][0] == '')))){ //IF THE CURRENT BUDGET AUTHORITY IS THE PBA AND HAS NOT SIGNED THE TIMESHEET ?>
															
															
															<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process.php?action=pba_sign&bgt_auth=<?php echo $current;?>&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" <?php if($signature_status != 'locked'){?><?php if($current == $_SESSION['user_ID']){?><?php if(($_SESSION['oba_approvals_complete'] == '0') && ($recordData['timesheets::Signer_status_imm_spvsr'][0] == '1')){?>onclick="return confirmSign()"<?php }else{ ?>onclick="return baMessage2()"<?php }?><?php }else{ ?>onclick="return baMessage()"<?php }?><?php }else{ ?>onclick="return arMessage()"<?php }?> title="Click here to sign this timesheet."><?php echo $current;?></a><p><span class="tiny">Primary Budget Authority</span></td>
															
															
															<?php } elseif($recordData['c_hrs_approved_bgt_auth_all'][0] > '0'){ //IF THE CURRENT BUDGET AUTHORITY HAS NOT SIGNED THE TIMESHEET ?>
															
															<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process.php?action=oba_sign&bgt_auth=<?php echo $current;?>&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" <?php if($signature_status != 'locked'){?><?php if($current == $_SESSION['user_ID']){?>onclick="return confirmSign()"<?php }else{ ?>onclick="return baMessage()"<?php }?><?php }else{ ?>onclick="return arMessage()"<?php }?> title="Click here to sign this timesheet."><?php echo $current;?></a><p><span class="tiny">Budget Authority</span></td>
															
															
															
															
															<?php } elseif($recordData['c_hrs_approved_bgt_auth_all'][0] == '0'){ //IF THE CURRENT BUDGET AUTHORITY HAS SIGNED THE TIMESHEET ?>
															
																<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><img src="/staff/sims/signatures/<?php echo $current;?>.png"><p><span class="tiny">Budget Authority
																
																<font color="999999"><?php echo '<br>['.$recordData['hrs_approved_timestamp'][0].']'; ?></font></span></td>
															


															<?php } elseif(($_SESSION['signer_ID_pba'] == $current) && ($searchResult['errorCode'] == '401') && ($_SESSION['signer_status_pba'] == '')){ //IF THE CURRENT BUDGET AUTHORITY IS THE PBA AND NO HOURS ARE CHARGED TO THIS PBA AND THE PBA HAS NOT SIGNED THE TIMESHEET ?>
															
															<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process.php?action=pba_sign&bgt_auth=<?php echo $current;?>&row_ID=<?php echo $timesheet_row_ID;?>" <?php if($signature_status != 'locked'){?><?php if($current == $_SESSION['user_ID']){?><?php if($_SESSION['oba_approvals_complete'] == '0'){?>onclick="return confirmSign()"<?php }else{ ?>onclick="return baMessage2()"<?php }?><?php }else{ ?>onclick="return baMessage()"<?php }?><?php }else{ ?>onclick="return arMessage()"<?php }?> title="Click here to sign this timesheet."><?php echo $current;?></a><p><span class="tiny">Primary Budget Authority</span></td>
															

															<?php } elseif(($_SESSION['signer_ID_pba'] == $current) && ($searchResult['errorCode'] == '401') && ($_SESSION['signer_status_pba'] == '1')){ //IF THE CURRENT BUDGET AUTHORITY IS THE PBA AND NO HOURS ARE CHARGED TO THIS PBA AND THE PBA HAS SIGNED THE TIMESHEET ?>
															
																<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><img src="/staff/sims/signatures/<?php echo $current;?>.png"><p><span class="tiny">Budget Authority
																
																<font color="999999"><?php echo '<br>['.$_SESSION['signer_timestamp_pba'].']'; ?></font></span></td>
															
															
															<?php } else { ?>
			
																<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap>Error_2: <?php echo $searchResult['errorCode'];?></td>
															
															<?php } ?>
															
															
												<?php }elseif($_SESSION['current_submitted_status'] == 'Revised'){ //IF THE TIMESHEET HAS BEEN REVISED 
												
															if($current == $recordData['timesheets::Signer_ID_pba'][0] && (($recordData['c_hrs_approved_bgt_auth_all'][0] > '0') || (($recordData['timesheets::c_cwp_pba_is_spvsr'][0] == '0') && ($recordData['timesheets::Signer_status_pba'][0] == '')))){ //IF THE CURRENT BUDGET AUTHORITY IS THE PBA AND HAS NOT SIGNED THE TIMESHEET ?>
															
															
															
															
															<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process_revised.php?action=pba_sign&bgt_auth=<?php echo $current;?>&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" <?php if($signature_status != 'locked'){?><?php if($current == $_SESSION['user_ID']){?><?php if($_SESSION['oba_approvals_complete'] == '0'){?>onclick="return confirmSign()"<?php }else{ ?>onclick="return baMessage2()"<?php }?><?php }else{ ?>onclick="return baMessage()"<?php }?><?php }else{ ?>onclick="return arMessage()"<?php }?> title="Click here to sign this timesheet."><?php echo $current;?></a><p><span class="tiny">Primary Budget Authority</span></td>
															
															
															<?php } elseif($recordData['c_hrs_approved_bgt_auth_all'][0] > '0'){ //IF THE CURRENT BUDGET AUTHORITY HAS NOT SIGNED THE TIMESHEET ?>
															
															<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process_revised.php?action=oba_sign&bgt_auth=<?php echo $current;?>&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" <?php if($signature_status != 'locked'){?><?php if($current == $_SESSION['user_ID']){?>onclick="return confirmSign()"<?php }else{ ?>onclick="return baMessage()"<?php }?><?php }else{ ?>onclick="return arMessage()"<?php }?> title="Click here to sign this timesheet."><?php echo $current;?></a><p><span class="tiny">Budget Authority</span></td>
															
															
															
															
															<?php } elseif($recordData['c_hrs_approved_bgt_auth_all'][0] == '0'){ //IF THE CURRENT BUDGET AUTHORITY HAS SIGNED THE TIMESHEET ?>
															
																<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><img src="/staff/sims/signatures/<?php echo $current;?>.png"><p><span class="tiny">Budget Authority
																
																<font color="999999"><?php echo '<br>['.$recordData['hrs_approved_timestamp'][0].']'; ?></font></span></td>
															


															<?php } elseif(($_SESSION['signer_ID_pba'] == $current) && ($searchResult['errorCode'] == '401') && ($_SESSION['signer_status_pba'] == '')){ //IF THE CURRENT BUDGET AUTHORITY IS THE PBA AND NO HOURS ARE CHARGED TO THIS PBA AND THE PBA HAS NOT SIGNED THE TIMESHEET ?>
															
															<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><a style="font-weight:normal;color:#0000FF;font-size:12px;text-decoration:underline;" href="timesheets_process_revised.php?action=pba_sign&bgt_auth=<?php echo $current;?>&row_ID=<?php echo $timesheet_row_ID;?>" <?php if($signature_status != 'locked'){?><?php if($current == $_SESSION['user_ID']){?><?php if($_SESSION['oba_approvals_complete'] == '0'){?>onclick="return confirmSign()"<?php }else{ ?>onclick="return baMessage2()"<?php }?><?php }else{ ?>onclick="return baMessage()"<?php }?><?php }else{ ?>onclick="return arMessage()"<?php }?> title="Click here to sign this timesheet."><?php echo $current;?></a><p><span class="tiny">Primary Budget Authority</span></td>
															

															<?php } elseif(($_SESSION['signer_ID_pba'] == $current) && ($searchResult['errorCode'] == '401') && ($_SESSION['signer_status_pba'] == '1')){ //IF THE CURRENT BUDGET AUTHORITY IS THE PBA AND NO HOURS ARE CHARGED TO THIS PBA AND THE PBA HAS SIGNED THE TIMESHEET ?>
															
																<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap><img src="/staff/sims/signatures/<?php echo $current;?>.png"><p><span class="tiny">Budget Authority
																
																<font color="999999"><?php echo '<br>['.$_SESSION['signer_timestamp_pba'].']'; ?></font></span></td>
															
															
															<?php } else { ?>
			
																<td align="center" style="text-align:center;border:1px solid #ffffff" bgcolor="ffffff" nowrap>Error_3: <?php echo $searchResult['errorCode'];?></td>
															
															<?php } ?>

												
												<?php } ?>
														
							<?php
														
							$i++;
														
							 } ?>
														
														
					<?php }?>
												
			
			<?php } ?>
			
<!--END: BUDGET AUTHORITY SIGNATURES-->	

<?php } ?>
									
									</tr>
									
																	
									
								
								</table>
							</td></tr>
							
<!--END FIFTH SECTION: APPROVAL SIGNATURES-->

<?php 

if($signature_debug == "on") {

		echo '<tr><td colspan='.$header_colspan.'>';
		echo '<p>Default Signers:<br>';
		foreach($default_signers as $current){
		echo $current.'<br>'; 
		}
		
		echo '<p>Bgt Auths:<br>';
		foreach($bgt_auths as $current){
		echo $current.'<br>'; 
		}

		echo '<p>All Signers:<br>';
		foreach($all_signers as $current){
		echo $current.'<br>'; 
		}
		
		echo '<p>Unique Signers (this is the number of signature boxes - '.$total_signers.'):<br>';
		foreach($unique_signers as $current){
		echo $current.'<br>'; 
		}

		echo 'Signatures Required: '.$signatures_required.'<br>';
		echo '<p>Other Signers besides staff member or immediate supervisor: ('.$total_other_signers.')<br>';
				foreach($other_signers as $current){
				echo $current.'<br>'; 
				}

		echo 'OBA Signatures Required: '.$signatures_required_oba.'<br>';
		$_SESSION['signatures_required_oba'] = $signatures_required_oba;

		echo '<p>Other Signers besides staff member or immediate supervisor or primary budget authority: ('.$total_other_signers2.')<br>';
				foreach($other_signers2 as $current){
				echo $current.'<br>'; 
				}

		echo '<p>Other Signers besides IS:<br>';
				foreach($other_signers_no_IS as $current){
				echo $current.'<br>'; 
				}
		echo '<p>Key1: '.$key1;

		echo '<p>Other Signers besides IS & SM:<br>';
				foreach($other_signers_no_SM as $current){
				echo $current.'<br>'; 
				}
		echo '<p>Key2: '.$key2;
		
		echo '<p>Other Signers besides IS & SM & PBA:<br>';
				foreach($other_signers_no_PBA as $current){
				echo $current.'<br>'; 
				}
		echo '<p>Key3 Status: '.$key3_status;

		
		echo '<p>AllHrsTotal: '.$AllHrsT_total;
		echo '<p>WkHrsTotal: '.$WkHrsT_total;
		echo '<p>PdLvHrsTotal: '.$PdLvHrsT_total;
		echo '<p>RegHrsTotal: '.$RegHrsT_total;
		echo '<p>UnPdLvHrsTotal: '.$UnPdLvHrsT_total;
		echo '<p>OTHrsTotal: '.$OTHrsT_total;
		echo '<p>SIGNATURE VARIABLES: ';
		echo '<p>$_SESSION[signer_status_owner]: '.$_SESSION['signer_status_owner'].': '.$_SESSION['signer_ID_owner'];
		echo '<p>$_SESSION[signer_status_imm_spvsr]: '.$_SESSION['signer_status_imm_spvsr'].': '.$_SESSION['signer_ID_imm_spvsr'];
		echo '<p>$_SESSION[signer_status_pba]: '.$_SESSION['signer_status_pba'].': '.$_SESSION['signer_ID_pba'];
		echo '<p>$_SESSION[signer_status_bgt_auth_1]: '.$_SESSION['signer_status_bgt_auth_1'].': '.$_SESSION['signer_ID_bgt_auth_1'];
		echo '<p>$_SESSION[signer_status_bgt_auth_2]: '.$_SESSION['signer_status_bgt_auth_2'].': '.$_SESSION['signer_ID_bgt_auth_2'];
		echo '<p>$_SESSION[signer_status_bgt_auth_3]: '.$_SESSION['signer_status_bgt_auth_3'].': '.$_SESSION['signer_ID_bgt_auth_3'];
		echo '<p>$_SESSION[signer_status_bgt_auth_4]: '.$_SESSION['signer_status_bgt_auth_4'].': '.$_SESSION['signer_ID_bgt_auth_4'];
		echo '<p>$_SESSION[signer_status_bgt_auth_5]: '.$_SESSION['signer_status_bgt_auth_5'].': '.$_SESSION['signer_ID_bgt_auth_5'];
		echo '<p>$_SESSION[signer_status_bgt_auth_6]: '.$_SESSION['signer_status_bgt_auth_6'].': '.$_SESSION['signer_ID_bgt_auth_6'];
		echo '<p>$_SESSION[signer_status_bgt_auth_7]: '.$_SESSION['signer_status_bgt_auth_7'].': '.$_SESSION['signer_ID_bgt_auth_7'];
		echo '<p>$_SESSION[signer_status_bgt_auth_8]: '.$_SESSION['signer_status_bgt_auth_8'].': '.$_SESSION['signer_ID_bgt_auth_8'];
		echo '<p>$_SESSION[signer_status_bgt_auth_OT]: '.$_SESSION['signer_status_bgt_auth_OT'].': '.$_SESSION['signer_ID_bgt_auth_OT'];
		echo '<p>$_recordData[timesheets::RptOutsideEmplFormSigned]: '.$recordData['timesheets::RptOutsideEmplFormSigned'][0];
		echo '<p>$_SESSION[payperiod_workhrs]: '.$_SESSION['payperiod_workhrs'];
		echo '<p>$_SESSION[oba_approvals_complete]: '.$_SESSION['oba_approvals_complete'];
		echo '<p>$_SESSION[approved_by_auth_rep_status]: '.$_SESSION['approved_by_auth_rep_status'];
		echo '</td></tr>';

}


 

?>