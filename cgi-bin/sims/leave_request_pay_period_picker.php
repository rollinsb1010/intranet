<?php
session_start();


if($_SESSION['employee_type'] == 'Exempt'){



		 if (isset($_GET['month'])){
		
			 switch($_GET['month']){
			
				 case '01' :
				
				 echo "31-31";
				
				 break;
				
				 case '02' :
				
				 echo "28-28;29-29";
				
				 break;
		
				 case '03' :
				
				 echo "31-31";
				
				 break;
			
				 case '04' :
				
				 echo "30-30";
				
				 break;
			
				 case '05' :
				
				 echo "31-31";
				
				 break;
			
				 case '06' :
				
				 echo "30-30";
				
				 break;
			
				 case '07' :
				
				 echo "31-31";
				
				 break;
			
				 case '08' :
				
				 echo "31-31";
				
				 break;
			
				 case '09' :
				
				 echo "30-30";
				
				 break;
			
				 case '10' :
				
				 echo "31-31";
				
				 break;
			
				 case '11' :
				
				 echo "30-30";
				
				 break;
			
				 case '12' :
				
				 echo "31-31";
				
				 break;
			
			 }
		
		 }


}else{

		 if (isset($_GET['month'])){
		
			 switch($_GET['month']){
			
				 case '01' :
				
				 echo "15-15;31-31";
				
				 break;
				
				 case '02' :
				
				 echo "15-15;28-28;29-29";
				
				 break;
		
				 case '03' :
				
				 echo "15-15;31-31";
				
				 break;
			
				 case '04' :
				
				 echo "15-15;30-30";
				
				 break;
			
				 case '05' :
				
				 echo "15-15;31-31";
				
				 break;
			
				 case '06' :
				
				 echo "15-15;30-30";
				
				 break;
			
				 case '07' :
				
				 echo "15-15;31-31";
				
				 break;
			
				 case '08' :
				
				 echo "15-15;31-31";
				
				 break;
			
				 case '09' :
				
				 echo "15-15;30-30";
				
				 break;
			
				 case '10' :
				
				 echo "15-15;31-31";
				
				 break;
			
				 case '11' :
				
				 echo "15-15;30-30";
				
				 break;
			
				 case '12' :
				
				 echo "15-15;31-31";
				
				 break;
			
			 }
		
		 }



}
?>

