<?php

require_once ('Adapter.php');

class AdapterOnline extends Adapter
{

	function doAuth(){	
		
		$this->loadPresets();	

		//step1 : Register the device
		$response = $this->registerDevice();
		
		
		//region Step 2: Register Device Credentials and get binaryDAToken
		$response =  $this->getBinaryDAToken($this->messageid,$this->deviceUserName,$this->devicePassword);
		
		
		preg_match('/<CipherValue>(.*)<\/CipherValue>/', $response, $matches);
		$cipherValue =  $matches[1];

		
		// region Step 3: Get Security Token by sending WLID username, password and device binaryDAToken
		$response = $this->getSecurityTokens($cipherValue);
		
		$responsedom = new DomDocument();
		$responsedom->loadXML($response);
		
		$cipherValues = $responsedom->getElementsbyTagName("CipherValue");
		
		$this->securityToken0 =  $cipherValues->item(0)->textContent;
		$this->securityToken1 =  $cipherValues->item(1)->textContent;
		
		$this->keyIdentifier = $responsedom->getElementsbyTagName("KeyIdentifier")->item(0)->textContent;	
	}
	
	private function getSecurityTokens($cipherValue){
		$securityTokenSoapTemplate = '
		<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
		xmlns:a="http://www.w3.org/2005/08/addressing"
		xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
			<s:Header>
				<a:Action s:mustUnderstand="1">
					http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue</a:Action>
				<a:MessageID>
					urn:uuid:'.$this->messageid.'</a:MessageID>
				<a:ReplyTo>
				<a:Address>
					http://www.w3.org/2005/08/addressing/anonymous</a:Address>
				</a:ReplyTo>
				<VsDebuggerCausalityData xmlns="http://schemas.microsoft.com/vstudio/diagnostics/servicemodelsink">
					uIDPozBEz+P/wJdOhoN2XNauvYcAAAAAK0Y6fOjvMEqbgs9ivCmFPaZlxcAnCJ1GiX+Rpi09nSYACQAA</VsDebuggerCausalityData>
				<a:To s:mustUnderstand="1">
					https://login.live.com/liveidSTS.srf</a:To>
				<o:Security s:mustUnderstand="1"
					xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
					<u:Timestamp u:Id="_0">
						<u:Created>'.$this->getCurrentTime().'Z</u:Created>
						<u:Expires>'.$this->getNextDayTime().'Z</u:Expires>
					</u:Timestamp>
					<o:UsernameToken u:Id="user">
					<o:Username>'.$this->getEmail().'</o:Username>
					<o:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">
						'.$this->getPassword().'</o:Password>
					</o:UsernameToken>
					<wsse:BinarySecurityToken ValueType="urn:liveid:device"
						xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
						<EncryptedData Id="BinaryDAToken0"
							Type="http://www.w3.org/2001/04/xmlenc#Element"
							xmlns="http://www.w3.org/2001/04/xmlenc#">
							<EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#tripledes-cbc">
							</EncryptionMethod>
							<ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
								<ds:KeyName>http://Passport.NET/STS</ds:KeyName>
							</ds:KeyInfo>
							<CipherData>
								<CipherValue>
									'.$cipherValue.'
								</CipherValue>
							</CipherData>
						</EncryptedData>
					</wsse:BinarySecurityToken>
				</o:Security>
			</s:Header>
			<s:Body>
				<t:RequestSecurityToken xmlns:t="http://schemas.xmlsoap.org/ws/2005/02/trust">
					<wsp:AppliesTo xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy">
						<a:EndpointReference>
							<a:Address>urn:crm:dynamics.com</a:Address>
						</a:EndpointReference>
					</wsp:AppliesTo>
					<wsp:PolicyReference URI="MBI_FED_SSL"
						xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy" />
					<t:RequestType>
						http://schemas.xmlsoap.org/ws/2005/02/trust/Issue</t:RequestType>
				</t:RequestSecurityToken>
			</s:Body>
		</s:Envelope>
		';
		return $this->getSoapResponse("login.live.com", "https://login.live.com/liveidSTS.srf", $securityTokenSoapTemplate);
	}
	
	
	private function getBinaryDAToken(){
	
		$deviceCredentialsSoapTemplate = '<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
			xmlns:a="http://www.w3.org/2005/08/addressing"
			xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
			<s:Header>
				<a:Action s:mustUnderstand="1">
					http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue</a:Action>
				<a:MessageID>
					urn:uuid:'.$this->messageid.'</a:MessageID>
					<a:ReplyTo>
						<a:Address>
							http://www.w3.org/2005/08/addressing/anonymous</a:Address>
					</a:ReplyTo>
					<VsDebuggerCausalityData xmlns="http://schemas.microsoft.com/vstudio/diagnostics/servicemodelsink">
						uIDPoy9Ez+P/wJdOhoN2XNauvYcAAAAAK0Y6fOjvMEqbgs9ivCmFPaZlxcAnCJ1GiX+Rpi09nSYACQAA</VsDebuggerCausalityData>
					<a:To s:mustUnderstand="1">
						https://login.live.com/liveidSTS.srf</a:To>
					<o:Security s:mustUnderstand="1"
						xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
						<u:Timestamp u:Id="_0">
							<u:Created>'.$this->getCurrentTime().'Z</u:Created>
							<u:Expires>'.$this->getNextDayTime().'Z</u:Expires>
						</u:Timestamp>
						<o:UsernameToken u:Id="devicesoftware">
							<o:Username>'.$this->deviceUserName.'</o:Username>
							<o:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$this->devicePassword.'</o:Password>
						</o:UsernameToken>
					</o:Security>
			</s:Header>
			<s:Body>
				<t:RequestSecurityToken xmlns:t="http://schemas.xmlsoap.org/ws/2005/02/trust">
					<wsp:AppliesTo xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy">
						<a:EndpointReference>
							<a:Address>http://passport.net/tb</a:Address>
						</a:EndpointReference>
					</wsp:AppliesTo>
					<t:RequestType>
						http://schemas.xmlsoap.org/ws/2005/02/trust/Issue</t:RequestType>
				</t:RequestSecurityToken>
			</s:Body>
		</s:Envelope>';
		$response = $this->getSoapResponse("login.live.com" , "https://login.live.com/liveidSTS.srf", $deviceCredentialsSoapTemplate);
	
		return $response;
	
	}
	
	private function registerDevice(){
	
		$registrationEndpointUri = "https://login.live.com/ppsecure/DeviceAddCredential.srf";
	
		$registrationEnvelope = '<DeviceAddRequest>
			<ClientInfo name="8758DD28-6EBD-DF11-855A-78E7D1623F35" version="1.0"/>
				<Authentication>
					<Membername>'.$this->deviceUserName.'</Membername>
					<Password>'.$this->devicePassword.'</Password>
				</Authentication>
			</DeviceAddRequest>';
		$credentials = $this->getSoapResponse('login.live.com', $registrationEndpointUri, $registrationEnvelope);
		return $credentials;
	}
	
	
    protected function getSoapRequestHeader($mustUnderstand, $CRMURL)
    {
		return '<s:Header>
			<a:Action s:mustUnderstand="1">
			'.$mustUnderstand.'</a:Action>
			<a:MessageID>
			urn:uuid:'.$this->messageid.'</a:MessageID>
			<a:ReplyTo>
			  <a:Address>
			  http://www.w3.org/2005/08/addressing/anonymous</a:Address>
			</a:ReplyTo>
			<VsDebuggerCausalityData xmlns="http://schemas.microsoft.com/vstudio/diagnostics/servicemodelsink">
			uIDPozJEz+P/wJdOhoN2XNauvYcAAAAAK0Y6fOjvMEqbgs9ivCmFPaZlxcAnCJ1GiX+Rpi09nSYACQAA</VsDebuggerCausalityData>
			<a:To s:mustUnderstand="1">
			'.$CRMURL.'</a:To>
			<o:Security s:mustUnderstand="1"
			xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
			  <u:Timestamp u:Id="_0">
				<u:Created>'.$this->getCurrentTime().'Z</u:Created>
				<u:Expires>'.$this->getNextDayTime().'Z</u:Expires>
			  </u:Timestamp>
			  <EncryptedData Id="Assertion0"
			  Type="http://www.w3.org/2001/04/xmlenc#Element"
			  xmlns="http://www.w3.org/2001/04/xmlenc#">
				<EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#tripledes-cbc">
				</EncryptionMethod>
				<ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
				  <EncryptedKey>
					<EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p">
					</EncryptionMethod>
					<ds:KeyInfo Id="keyinfo">
					  <wsse:SecurityTokenReference xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">

						<wsse:KeyIdentifier EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary"
						ValueType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509SubjectKeyIdentifier">'.$this->keyIdentifier.'</wsse:KeyIdentifier>
					  </wsse:SecurityTokenReference>
					</ds:KeyInfo>
					<CipherData>
					  <CipherValue>'.$this->securityToken0.'</CipherValue>
					</CipherData>
				  </EncryptedKey>
				</ds:KeyInfo>
				<CipherData>
				  <CipherValue>'.$this->securityToken1.'</CipherValue>
				</CipherData>
			  </EncryptedData>
			</o:Security>
		  </s:Header>';    	
    }
	
}	

?>