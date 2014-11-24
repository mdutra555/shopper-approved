<?php 
$site = ($_GET['site']) ? $_GET['site'] : null;
$id = ($_GET['id']) ? $_GET['id'] : null;
$optionSiteList = "";
if($site){
	$siteList = json_decode(file_get_contents("http://www.shopperapproved.com/local/PluginInfo/" . $site));
	$a=0;
	if(count($siteList)){
		
		if($id==null){
			$optionSiteList .= '<option value="0" selected="selected">Select Site</option>';
			$a++;
		}else{
			$optionSiteList .= '<option value="0">Select Site</option>';
		}
		
		
		foreach($siteList as $info)
		{		
			if($id==$info->id){
				$optionSiteList .= '<option domain=' . strip_tags($info->domain) . ' value="' . strip_tags($info->id) . '" selected="selected">' . strip_tags($info->title) . '</option>'; 
				$a++;
			}
			else{
				$optionSiteList .= '<option domain=' . strip_tags($info->domain) . ' value="' . strip_tags($info->id) . '">' . strip_tags($info->title) . '</option>'; 
			}
		}
		if($a==0){
			$optionSiteList .= '<option value="" selected="selected">Custom ID and Review URL</option>';
		}
	}else{
		$optionSiteList .= '<option value="0">No Site Available</option>';
	}
}else{
	$optionSiteList .= '<option value="0">No Site Available</option>';
}

echo $optionSiteList;
?>
