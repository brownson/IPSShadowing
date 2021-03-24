<?php

class IPSShadowingConditionTimeInRange extends IPSModule
{

	// -------------------------------------------------------------------------
	public function Create() {
		parent::Create();

		$this->RegisterPropertyInteger ('PropertyVariableID1Value',      0);
		$this->RegisterPropertyInteger ('PropertyVariableID1Lower',      0);
		$this->RegisterPropertyInteger ('PropertyVariableID1Upper',      0);
		$this->RegisterPropertyInteger ('PropertyVariableID2Value',      0);
		$this->RegisterPropertyInteger ('PropertyVariableID2Lower',      0);
		$this->RegisterPropertyInteger ('PropertyVariableID2Upper',      0);
		$this->RegisterPropertyString  ('PropertyStatusMessageTrue',     '');
		$this->RegisterPropertyString  ('PropertyStatusMessageFalse',    '');
	}

	// -------------------------------------------------------------------------
	public function RequestAction($Ident, $Value) {
		$this->SetValue($Ident, $Value);
		switch($Ident) {
			case 'Evaluated':
				break;
			default:
				throw new Exception("Invalid ident");
		}
	}
	
	// -------------------------------------------------------------------------
	public function ApplyChanges() {
		parent::ApplyChanges();

		if (IPS_GetKernelRunlevel() !== KR_READY) {
			return;
		}

		if (!IPS_VariableProfileExists('ShdCond.Evaluated')) {
			IPS_CreateVariableProfile('ShdCond.Evaluated', 0);
		} 
		IPS_SetVariableProfileIcon('ShdCond.Evaluated', 'Information');
		IPS_SetVariableProfileText('ShdCond.Evaluated', '' /*Prefix*/, '' /*Suffix*/);
		IPS_SetVariableProfileDigits('ShdCond.Evaluated', 0 /*Digits*/); 
		IPS_SetVariableProfileValues('ShdCond.Evaluated', 0 /*Min*/, 1 /*Max*/, 0 /*Step*/); 
		IPS_SetVariableProfileAssociation('ShdCond.Evaluated', 0, $this->Translate('No'), '', 255*256*256);
		IPS_SetVariableProfileAssociation('ShdCond.Evaluated', 1, $this->Translate('Yes'), '',255*256);
		
		$this->RegisterVariableBoolean('Evaluated', $this->Translate('Evaluated'), 'ShdCond.Evaluated');
		$this->RegisterVariableString('StatusMessage', $this->Translate('StatusMessage'), '~TextBox');
		
		$this->ValidateSettings();
	}
	
	// -------------------------------------------------------------------------
	private function ValidateSettings() {
		if ($this->ReadPropertyInteger('PropertyVariableID1Value') == 0) {
			$this->SetStatus(200);
			return;
		} else if ($this->ReadPropertyInteger('PropertyVariableID2Value') == 0) {
			$this->SetStatus(201);
			return;
		} else {
			$this->SetStatus(102);
		}
	}
	
	// -------------------------------------------------------------------------
	private function GetTimeValue($property) {
		$variableID   = $this->ReadPropertyInteger($property);
		$variableType = IPS_GetVariable($variableID)['VariableType'];
		if ($variableType == 1) {
			$result = date('H:i', GetValue($variableID));
			$this->SendDebug('GetTimeValue', "Get TimeValue from Integer $result", 0);
		} else {
			$result = GetValue($variableID);
			$this->SendDebug('GetTimeValue', "Get TimeValue from String $result", 0);
		}
		return $result;
	}

	// -------------------------------------------------------------------------
	private function GetTimeValueWithDefault($property, $defaultValue) {
		$variableID   = $this->ReadPropertyInteger($property);
		if ($variableID == 0) {
			$result = $defaultValue;
		} else {
			$result = $this->GetTimeValue($property);
		}
		return $result;
	}

	// -------------------------------------------------------------------------
	public function Evaluate() {
		$valueStart        = $this->GetTimeValue('PropertyVariableID1Value');
		$valueStartLower   = $this->GetTimeValueWithDefault('PropertyVariableID1Lower', $valueStart );
		$valueStartUpper   = $this->GetTimeValueWithDefault('PropertyVariableID1Upper', $valueStart );
		$this->SendDebug('Evaluate', "Read Start Time $valueStart with Limits  $valueStartLower and $valueStartUpper", 0);
		if ($valueStart < $valueStartLower) {
			$valueStart = $valueStartLower;
		} else if ($valueStart > $valueStartUpper) {
			$valueStart = $valueStartUpper;
		} else {
		}

		$valueEnd          = $this->GetTimeValue('PropertyVariableID2Value');
		$valueEndLower     = $this->GetTimeValueWithDefault('PropertyVariableID2Lower', $valueEnd );
		$valueEndUpper     = $this->GetTimeValueWithDefault('PropertyVariableID2Upper', $valueEnd );
		$this->SendDebug('Evaluate', "Read Start Time $valueEnd with Limits  $valueEndLower and $valueEndUpper", 0);
		if ($valueEnd < $valueEndLower) {
			$valueEnd = $valueEndLower;
		} else if ($valueEnd > $valueEndUpper) {
			$valueEnd = $valueEndUpper;
		} else {
		}
		
		$valueCurrent = date('H:i', time());
		$evaluated  = $valueStart < $valueCurrent  && $valueCurrent < $valueEnd;
		$this->SendDebug('Evaluate', "Evaluate $valueCurrent in $valueStart - $valueEnd, Evaluated=".($evaluated?'Yes':'No'), 0);

		$this->SetValue('Evaluated', $evaluated);
		if ($evaluated) {
			if ($this->ReadPropertyString('PropertyStatusMessageTrue') === '') {
				$this->SetValue('StatusMessage', "$valueCurrent in $valueStart-$valueEnd");
			}else {
				$this->SetValue('StatusMessage', $this->ReadPropertyString('PropertyStatusMessageTrue'));
			}
		} else {
			if ($this->ReadPropertyString('PropertyStatusMessageFalse') === '') {
				$this->SetValue('StatusMessage', "$valueCurrent not in $valueStart-$valueEnd");
			}else {
				$this->SetValue('StatusMessage', $this->ReadPropertyString('PropertyStatusMessageFalse'));
			}
		}
	}

}

?>