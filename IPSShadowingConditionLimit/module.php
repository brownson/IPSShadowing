<?php

class IPSShadowingConditionLimit extends IPSModule
{

	// -------------------------------------------------------------------------
	public function Create() {
		parent::Create();

		$this->RegisterPropertyInteger ('PropertyVariableID',            0);
		$this->RegisterPropertyString  ('PropertyStatusMessageTrue',     '');
		$this->RegisterPropertyString  ('PropertyStatusMessageFalse',    '');
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
		IPS_SetVariableProfileAssociation('ShdCond.Evaluated', 0, $this->Translate('false'), '', -1);
		IPS_SetVariableProfileAssociation('ShdCond.Evaluated', 1, $this->Translate('true'), '', -1);
		
		$this->RegisterVariableBoolean('Evaluated', 'Evaluated', 'ShdCond.Evaluated');
		$this->RegisterVariableString('StatusMessage', 'StatusMessage', '~TextBox');
		$this->RegisterVariableFloat('LimitUpper', 'Limit Activate',    '~Temperature');
		$this->EnableAction("LimitUpper");
		$this->RegisterVariableFloat('LimitLower', 'Limit Deactivate',  '~Temperature');
		$this->EnableAction("LimitLower");
		
		$this->ValidateSettings();
	}
	
	// -------------------------------------------------------------------------
	private function ValidateSettings() {
		 if ($this->ReadPropertyInteger('PropertyVariableID') == 0) {
			$this->SetStatus(200);
			return;
		} else {
			$this->SetStatus(102);
		}
	}

	// -------------------------------------------------------------------------
	public function RequestAction($Ident, $Value) {
		$this->SetValue($Ident, $Value);
		switch($Ident) {
			case 'LimitUpper':
				$this->SetValue($Ident, $Value);
				break;
			case 'LimitLower':
				$this->SetValue($Ident, $Value);
				break;
			default:
				throw new Exception("Invalid ident");
		}
	}

	// -------------------------------------------------------------------------
	public function Evaluate() {
		$valueLink = GetValue($this->ReadPropertyInteger('PropertyVariableID'));
		if ($this->GetValue('Evaluated')) {
			$evaluated = $valueLink >= $this->GetValue('LimitLower');
			$this->SendDebug('Evaluate', "Evaluate Lower Limit $valueLink >= ".$this->GetValue('LimitLower').", Evaluated=".($evaluated?'Yes':'No'), 0);
		} else {
			$evaluated = $valueLink >= $this->GetValue('LimitUpper');
			$this->SendDebug('Evaluate', "Evaluate Upper Limit $valueLink >= ".$this->GetValue('LimitUpper').", Evaluated=".($evaluated?'Yes':'No'), 0);
		}

		$this->SetValue('Evaluated', $evaluated);
		if ($evaluated) {
			if ($this->ReadPropertyString('PropertyStatusMessageTrue') === '') {
				$this->SetValue('StatusMessage', "$valueLink>=".$$this->GetValue('LimitLower').'/'.$$this->GetValue('LimitUpper'));
			}else {
				$this->SetValue('StatusMessage', $this->ReadPropertyString('PropertyStatusMessageTrue'));
			}
		} else {
			if ($this->ReadPropertyString('PropertyStatusMessageFalse') === '') {
				$this->SetValue('StatusMessage', "$valueLink<".$$this->GetValue('LimitLower').'/'.$$this->GetValue('LimitUpper'));
			}else {
				$this->SetValue('StatusMessage', $this->ReadPropertyString('PropertyStatusMessageFalse'));
			}
		}
	}

}

?>