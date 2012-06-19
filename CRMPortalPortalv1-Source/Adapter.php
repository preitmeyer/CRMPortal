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
class Account{
	public $accountId = "";
	public $name = "";
	public $address = "";
	public $telephone = "";	

}
class Cases{
	public $incidentId = "";
	public $subject = "";
	public $notetext = "";
	public $createdon = "";
	public $title = "";
	public $description = "";	
	public $ticketnumber = "";

}
abstract class Adapter
{


	public $deviceUserName = "username123456";
	public $devicePassword = "password123456";
	public $messageid = '';
	public $accountId = '';

	public $email = '';
	public $password = '';
	
	public $securityToken0 = '';
	public $securityToken1 = '';
	public $keyIdentifier = '';
	
	function setEmail($email){
		$this->email = $email;
	}
	function getEmail(){
		return $this->email;
	}

	function setPassword($password){
		$this->password = $password;
	}
	function getPassword(){
		return $this->password;
	}

	function setAccountId($accountId){
		$this->accountId = $accountId;
	}
	function getAccountId(){
		return $accountId;
	}
	
	

	function loadPresets(){
		date_default_timezone_set('UTC');
		$currentTime = strval(time());
		$this->deviceUserName = $currentTime . "username123456";
		$this->devicePassword = "password123456" . $currentTime;
		$this->messageid = $this->getMessageId();
	}
	
	function getMessageId()
	{
		list($usec, $sec) = explode(" ", microtime());
		$temp = sprintf("%d-%f-%s", $sec, $usec, uniqid());		
		return md5($temp);		
	}
	
	
	abstract function doAuth();
	
	
	function createLead($values,$CRMURL){
	
        $domainname = substr($CRMURL,8,-1);
        $pos = strpos($domainname, "/");
        $domainname = substr($domainname,0,$pos);

		date_default_timezone_set('UTC');
		
		$mustUnderstand = 'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/Create';
		
		$createSoapTemplate = '
			<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
			xmlns:a="http://www.w3.org/2005/08/addressing"
			xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
			  '.$this->getSoapRequestHeader($mustUnderstand, $CRMURL).'
                <s:Body>
                    <Create xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                    <entity xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                        <b:Attributes xmlns:c="http://schemas.datacontract.org/2004/07/System.Collections.Generic">
                            <b:KeyValuePairOfstringanyType>
                                <c:key>firstname</c:key>
                                <c:value i:type="d:string" xmlns:d="http://www.w3.org/2001/XMLSchema">'.$values['firstname'].'</c:value>
                            </b:KeyValuePairOfstringanyType>
                            <b:KeyValuePairOfstringanyType>
                                <c:key>lastname</c:key>
                                <c:value i:type="d:string" xmlns:d="http://www.w3.org/2001/XMLSchema">'.$values['lastname'].'</c:value>
                            </b:KeyValuePairOfstringanyType>
                            <b:KeyValuePairOfstringanyType>
                                <c:key>telephone1</c:key>
                                <c:value i:type="d:string" xmlns:d="http://www.w3.org/2001/XMLSchema">'.$values['phonenumber'].'</c:value>
                            </b:KeyValuePairOfstringanyType>
                            <b:KeyValuePairOfstringanyType>
                                <c:key>emailaddress1</c:key>
                                <c:value i:type="d:string" xmlns:d="http://www.w3.org/2001/XMLSchema">'.$values['email'].'</c:value>
                            </b:KeyValuePairOfstringanyType>
							<b:KeyValuePairOfstringanyType>
                                <c:key>description</c:key>
                                <c:value i:type="d:string" xmlns:d="http://www.w3.org/2001/XMLSchema">'.$values['description'].'</c:value>
                            </b:KeyValuePairOfstringanyType>
							<b:KeyValuePairOfstringanyType>
                                <c:key>subject</c:key>
                                <c:value i:type="d:string" xmlns:d="http://www.w3.org/2001/XMLSchema">'.$values['topic'].'</c:value>
                            </b:KeyValuePairOfstringanyType>
						</b:Attributes>
                        <b:EntityState i:nil="true"/>
                        <b:FormattedValues xmlns:c="http://schemas.datacontract.org/2004/07/System.Collections.Generic"/>
                        <b:Id>00000000-0000-0000-0000-000000000000</b:Id>
                        <b:LogicalName>lead</b:LogicalName>
                        <b:RelatedEntities xmlns:c="http://schemas.datacontract.org/2004/07/System.Collections.Generic"/>
                    </entity>
                    </Create>
                </s:Body>
            </s:Envelope>
			';
	   		$response =  $this->getSoapResponse($domainname, $CRMURL, $createSoapTemplate);
			preg_match('/<CreateResult>(.*)<\/CreateResult>/', $response, $matches);
            if(sizeof($matches)>0){
				$createResult =  $matches[1];
				return $createResult;
			}else return "nono";
    }

	//BEGIN CASE CODE

		function createCase($values,$CRMURL){
	
        $domainname = substr($CRMURL,8,-1);
        $pos = strpos($domainname, "/");
        $domainname = substr($domainname,0,$pos);

		date_default_timezone_set('UTC');
		
		$mustUnderstand = 'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/Create';

		$createSoapTemplate = '
			<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
			xmlns:a="http://www.w3.org/2005/08/addressing"
			xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
			  	'.$this->getSoapRequestHeader($mustUnderstand, $CRMURL).'			  
				<s:Body xmlns:s="http://www.w3.org/2003/05/soap-envelope">
					<ns6:Create xmlns:ns6="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
						<ns6:entity>
							<Attributes xmlns="http://schemas.microsoft.com/xrm/2011/Contracts"> 
								<KeyValuePairOfstringanyType>
									<ns3:key xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic">title</ns3:key>
									<ns3:value xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic" xmlns:s1="http://www.w3.org/2001/XMLSchema-instance" xmlns:s2="http://www.w3.org/2001/XMLSchema" s1:type="s2:string">'.$values['firstname'].'</ns3:value>
								</KeyValuePairOfstringanyType>
								<KeyValuePairOfstringanyType>
									<ns3:key xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic">planet_callbacknumber</ns3:key>
									<ns3:value xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic" xmlns:s1="http://www.w3.org/2001/XMLSchema-instance" xmlns:s2="http://www.w3.org/2001/XMLSchema" s1:type="s2:string">'.$values['phonenumber'].'</ns3:value>
								</KeyValuePairOfstringanyType>
								<KeyValuePairOfstringanyType>
									<ns3:key xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic">description</ns3:key>
									<ns3:value xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic" xmlns:s1="http://www.w3.org/2001/XMLSchema-instance" xmlns:s2="http://www.w3.org/2001/XMLSchema" s1:type="s2:string">'.$values['description'].'</ns3:value>
								</KeyValuePairOfstringanyType> 
								<KeyValuePairOfstringanyType>
									<ns3:key xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic">customerid</ns3:key>
									<value xmlns="http://schemas.datacontract.org/2004/07/System.Collections.Generic" xmlns:ns2="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="ns2:EntityReference">
										<ns2:Id>'.$values['guid'].'</ns2:Id>
										<ns2:LogicalName>account</ns2:LogicalName></value>
								</KeyValuePairOfstringanyType>
							</Attributes>
							<ns2:LogicalName xmlns:ns2="http://schemas.microsoft.com/xrm/2011/Contracts">incident</ns2:LogicalName>
						</ns6:entity>
					</ns6:Create> 
				</s:Body>
            </s:Envelope>
			';
	   		$response =  $this->getSoapResponse($domainname, $CRMURL, $createSoapTemplate);
			preg_match('/<CreateResult>(.*)<\/CreateResult>/', $response, $matches);
            if(sizeof($matches)>0){
				$createResult =  $matches[1];
				return $createResult;
			}else return "nono";
    }
	
	
	

	//END CASE  CODE
	
	
	//BEGIN Note CODE

		function createNote($values,$CRMURL){
	
        $domainname = substr($CRMURL,8,-1);
        $pos = strpos($domainname, "/");
        $domainname = substr($domainname,0,$pos);

		date_default_timezone_set('UTC');
		
		$mustUnderstand = 'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/Create';

		$createSoapTemplate = '
			<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
			xmlns:a="http://www.w3.org/2005/08/addressing"
			xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
				'.$this->getSoapRequestHeader($mustUnderstand, $CRMURL).'			  
				<s:Body xmlns:s="http://www.w3.org/2003/05/soap-envelope">
					<ns6:Create xmlns:ns6="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
						<ns6:entity>
							<Attributes xmlns="http://schemas.microsoft.com/xrm/2011/Contracts"> 
								<KeyValuePairOfstringanyType>
									<ns3:key xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic">subject</ns3:key>
									<ns3:value xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic" xmlns:s1="http://www.w3.org/2001/XMLSchema-instance" xmlns:s2="http://www.w3.org/2001/XMLSchema" s1:type="s2:string">'.$values['topic'].'</ns3:value>
								</KeyValuePairOfstringanyType>
								<KeyValuePairOfstringanyType>
									<ns3:key xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic">notetext</ns3:key>
									<ns3:value xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic" xmlns:s1="http://www.w3.org/2001/XMLSchema-instance" xmlns:s2="http://www.w3.org/2001/XMLSchema" s1:type="s2:string">'.$values['description'].'</ns3:value>
								</KeyValuePairOfstringanyType> 
								<KeyValuePairOfstringanyType>
									<ns3:key xmlns:ns3="http://schemas.datacontract.org/2004/07/System.Collections.Generic">objectid</ns3:key>
									<value xmlns="http://schemas.datacontract.org/2004/07/System.Collections.Generic" xmlns:ns2="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="ns2:EntityReference">
										<ns2:Id>'.$values['guid'].'</ns2:Id>
										<ns2:LogicalName>incident</ns2:LogicalName></value>
								</KeyValuePairOfstringanyType>
							</Attributes>
							<ns2:LogicalName xmlns:ns2="http://schemas.microsoft.com/xrm/2011/Contracts">annotation</ns2:LogicalName>
						</ns6:entity>
					</ns6:Create> 
				</s:Body>
            </s:Envelope>
			';
	   		$response =  $this->getSoapResponse($domainname, $CRMURL, $createSoapTemplate);
			preg_match('/<CreateResult>(.*)<\/CreateResult>/', $response, $matches);
            if(sizeof($matches)>0){
				$createResult =  $matches[1];
				return $createResult;
			}else return "nono";
    }
	
	
	

	//END NOTE  CODE

	
	//BEGIN DISPCASE CODE

		function getallCase($values,$CRMURL){
	
        $domainname = substr($CRMURL,8,-1);
        $pos = strpos($domainname, "/");
        $domainname = substr($domainname,0,$pos);

		date_default_timezone_set('UTC');
		
		$mustUnderstand = 'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/Execute';

		$createSoapTemplate = '
			<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
			xmlns:a="http://www.w3.org/2005/08/addressing"
			xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
			  '.$this->getSoapRequestHeader($mustUnderstand, $CRMURL).'
				<s:Body>
				<Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
					<request i:type="b:RetrieveMultipleRequest" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
						<b:Parameters xmlns:c="http://schemas.datacontract.org/2004/07/System.Collections.Generic">
							<b:KeyValuePairOfstringanyType>
								<c:key>Query</c:key>
								<c:value i:type="b:FetchExpression">
									<b:Query>&lt;fetch mapping="logical" count="50" version="1.0"&gt;&#xD;
												&lt;entity name="incident"&gt;&#xD;
												&lt;attribute name="ticketnumber" /&gt;&#xD;
												&lt;attribute name="title" /&gt;&#xD;
												&lt;attribute name="planet_casestatusname" /&gt;&#xD;
												&lt;attribute name="incidentid" /&gt;&#xD;
												&lt;order attribute="ticketnumber" descending="false" /&gt;&#xD;
												&lt;filter type="and"&gt;&#xD;
													&lt;condition attribute="customerid" operator="eq" value="'.$values['guid'].'" /&gt;&#xD;
												&lt;/filter&gt;
												&lt;/entity&gt;&#xD;
												&lt;/fetch&gt;
									</b:Query>
								</c:value>
							</b:KeyValuePairOfstringanyType>
						</b:Parameters>
						<b:RequestId i:nil="true"/><b:RequestName>RetrieveMultiple</b:RequestName>
					</request>
				</Execute>
				</s:Body>
            </s:Envelope>
			';
	   		$response =  $this->getSoapResponse($domainname, $CRMURL, $createSoapTemplate);
			
			$accountsArray = array();

			$responsedom = new DomDocument();
			$responsedom->loadXML($response);
			$entities = $responsedom->getElementsbyTagName("Entity");
			foreach($entities as $entity){
				$account = new Account();
				$kvptypes = $entity->getElementsbyTagName("KeyValuePairOfstringanyType");
				foreach($kvptypes as $kvp){
					$key =  $kvp->getElementsbyTagName("key")->item(0)->textContent;
					$value =  $kvp->getElementsbyTagName("value")->item(0)->textContent;					
					if($key == 'incidentid'){ $account->accountId = $value; }
					if($key == 'title'){ $account->name = $value; }
					if($key == 'planet_casestatusname'){ $account->telephone = $value; }					
					if($key == 'ticketnumber'){ $account->address = $value; }										
				}
				$accountsArray[] = $account;
			}
			return $accountsArray;
			
			
    }
	
	
	

	//END DISPCASE  CODE
	
	//BEGIN CASE DETAILS
	
		function getCaseDetails($values,$CRMURL){
	
        $domainname = substr($CRMURL,8,-1);
        $pos = strpos($domainname, "/");
        $domainname = substr($domainname,0,$pos);

		date_default_timezone_set('UTC');
		
		$mustUnderstand = 'http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/Execute';

		$createSoapTemplate = '
			<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
			xmlns:a="http://www.w3.org/2005/08/addressing"
			xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
			  '.$this->getSoapRequestHeader($mustUnderstand, $CRMURL).'
				<s:Body>
				<Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
					<request i:type="b:RetrieveMultipleRequest" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
						<b:Parameters xmlns:c="http://schemas.datacontract.org/2004/07/System.Collections.Generic">
							<b:KeyValuePairOfstringanyType>
								<c:key>Query</c:key>
								<c:value i:type="b:FetchExpression">
									<b:Query>&lt;fetch mapping="logical" count="50" version="1.0"&gt;&#xD;
												&lt;entity name="annotation"&gt;&#xD;
												&lt;attribute name="subject" /&gt;&#xD;
												&lt;attribute name="notetext" /&gt;&#xD;
												&lt;attribute name="annotationid" /&gt;&#xD;
												&lt;attribute name="createdon" /&gt;&#xD;
												&lt;order attribute="createdon" descending="false" /&gt;&#xD;
												&lt;filter type="and"&gt;&#xD;
													&lt;condition attribute="isdocument" operator="eq" value="0" /&gt;&#xD;
												&lt;/filter&gt;
												&lt;link-entity name="incident" from="incidentid" to="objectid" alias="aa"&gt;&#xD;
												&lt;attribute name="title" /&gt;&#xD;
												&lt;attribute name="description" /&gt;&#xD;
												&lt;attribute name="ticketnumber" /&gt;&#xD;
												&lt;filter type="and"&gt;&#xD;
													&lt;condition attribute="incidentid" operator="eq" value="'.$values['guid'].'" /&gt;&#xD;
												&lt;/filter&gt;
												&lt;/link-entity&gt;												
												&lt;/entity&gt;&#xD;
												&lt;/fetch&gt;
									</b:Query>
								</c:value>
							</b:KeyValuePairOfstringanyType>
						</b:Parameters>
						<b:RequestId i:nil="true"/><b:RequestName>RetrieveMultiple</b:RequestName>
					</request>
				</Execute>
				</s:Body>
            </s:Envelope>
			';
	   		$response =  $this->getSoapResponse($domainname, $CRMURL, $createSoapTemplate);
			
			$accountsArray = array();

			$responsedom = new DomDocument();
			$responsedom->loadXML($response);
			$entities = $responsedom->getElementsbyTagName("Entity");
			foreach($entities as $entity){
				$cases = new Cases();
				$kvptypes = $entity->getElementsbyTagName("KeyValuePairOfstringanyType");
				foreach($kvptypes as $kvp){
					$key =  $kvp->getElementsbyTagName("key")->item(0)->textContent;
					$value =  $kvp->getElementsbyTagName("value")->item(0)->textContent;					
					if($key == 'incidentid'){ $cases->accountId = $value; }
					if($key == 'subject'){ $cases->subject = $value; }
					if($key == 'notetext'){ $cases->notetext = $value; }		
					if($key == 'createdon'){ $cases->createdon = $value; }
					if($key == 'description'){ $cases->description = $value; }						
					if($key == 'ticketnumber'){ $cases->ticketnubmer = $value; }										
				}
				$caseArray[] = $cases;
			}
			return $caseArray;
			
			
    }
	
	
	
	
	//END CASE DETAILS
	

	
	protected function getCurrentTime(){
		return substr(date('c'),0,-6) . ".00";
    }
    
    protected function getNextDayTime(){
		return substr(date('c', strtotime('+1 day')),0,-6) . ".00"; 
    }
    
    protected abstract function getSoapRequestHeader($mustUnderstand, $CRMURL);    

	protected function getSoapResponse($hostname, $soapUrl, $content){
	
		$doc = new DOMDocument();
		$doc->loadXML($content);
		
		$content = $doc->C14N();
		
		//print_r(array('request' => $content));
		
		// setup headers
		$headers = array(
				"Host: " . $hostname,
				'Connection: Keep-Alive',
				"Content-type: application/soap+xml; charset=UTF-8",
				"Content-length: ".strlen($content),
		);
	
		$cURLHandle = curl_init();
		curl_setopt($cURLHandle, CURLOPT_URL, $soapUrl);
		curl_setopt($cURLHandle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cURLHandle, CURLOPT_TIMEOUT, 60);
		curl_setopt($cURLHandle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($cURLHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($cURLHandle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($cURLHandle, CURLOPT_POST, 1);
		curl_setopt($cURLHandle, CURLOPT_POSTFIELDS, $content);
		$response = curl_exec($cURLHandle);
		curl_close($cURLHandle);
		
		//print_r(array('response' => $response));
		
		return $response;
	}    
	
}	

?>