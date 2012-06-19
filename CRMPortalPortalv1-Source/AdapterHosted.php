<?php

/*

Copyright 2010-2011 Twentyone Logs Inc, 2012 Planet Technologies Inc

file: Adapter.php

author:Srini Raja, Paul Reitmeyer, Dobroslav Kolev

version: 1.0.0

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

require_once ('Adapter.php');

class AdapterHosted extends Adapter
{
	function doAuth(){
	
		$this->loadPresets();
	/*
		//region Step 2: Register Device Credentials and get binaryDAToken
		$response =  $this->getBinaryDAToken();
	
		$responsedom = new DomDocument();
		$responsedom->loadXML($response);
	
		$cipherValues = $responsedom->getElementsbyTagName("CipherValue");
	
		$this->securityToken0 =  $cipherValues->item(0)->textContent;
		$this->securityToken1 =  $cipherValues->item(1)->textContent;
	
		$this->keyIdentifier = $responsedom->getElementsbyTagName("KeyIdentifier")->item(0)->textContent;
		*/
	}
	
	protected function getSoapRequestHeader($mustUnderstand, $CRMURL)
	{
		//none yet
		return '<s:Header>
    		<a:Action s:mustUnderstand="1">'.$mustUnderstand.'</a:Action>
    			<a:MessageID>
				urn:uuid:'.$this->messageid.'</a:MessageID>
    			<a:ReplyTo>
      				<a:Address>http://www.w3.org/2005/08/addressing/anonymous</a:Address>
    			</a:ReplyTo>
    			<a:To s:mustUnderstand="1">
				'.$CRMURL.'</a:To>
  		</s:Header>';
	}
}

?>