<?php
		$container = "";		
		if(sizeof($result)>0){			
			$container .= "FLEXBILE
";
			$container .= strtoupper($result[0]['cfrom']);
			$container .= " ";
			$container .= date("dMy",strtotime($result[0]['cdate']));
			$container .= "

";			
			$type_list = array();
			foreach($mresult as $type){
				$text = "";
				if(strlen($type['csms_title'])>0){
					$text .= strtoupper($type['csms_title']."
");
				}					
				$count = $type['cprize'];
				$num_list = array();
				for($i=0; $i<sizeof($result); $i++){
					$num = $result[$i];
					if(strtolower($num['cfrom'])==strtolower($type['cfrom']) && strtolower($num['ctitle'])==strtolower($type['ctitle'])){
						$text2 = "";
						$temp_num = explode("$",$type['csms_result_template']);
						for($j=0; $j<sizeof($temp_num); $j++){							
							if(strtolower($temp_num[$j][0])=="c"){
								$text2 .= $count.substr($temp_num[$j], 1);
							}else if(is_numeric(strtolower($temp_num[$j][0]))){
								$tn = "n".strtolower($temp_num[$j][0]);
								$text2 .= $num[$tn].substr($temp_num[$j], 1);
							}
						}
						$text2 = preg_replace('#(^[^0-9])|([^0-9]$)#i', '', $text2);
						$num_list[] = $text2;
						if($type['cprize_fixed']==0){
							$count += 1;
						}
					}
				}	
				if($type['cprize_fixed']==1){
					$text .= implode(" ",$num_list);
				}else{
					$text .= implode("
",$num_list);
				}				
				$type_list[] = $text;
			}
			
			$container .= implode("

",$type_list);
			
		}
		
		if(strlen($container)>0){
			echo $container;
		}
?>
