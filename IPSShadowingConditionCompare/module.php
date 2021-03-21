<?php

class IPSShadowingConditionCompare extends IPSModule
{

	// -------------------------------------------------------------------------
	public function Create() {
		parent::Create();

		$this->RegisterPropertyInteger ('PropertyVariableID1',           0);
		$this->RegisterPropertyString  ('PropertyComparator',            '=');
		$this->RegisterPropertyInteger ('PropertyVariableID2',           0);
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
		IPS_SetVariableProfileAssociation('ShdCond.Evaluated', 0, $this->Translate('false'), '', -1);
		IPS_SetVariableProfileAssociation('ShdCond.Evaluated', 1, $this->Translate('true'), '', -1);
		
		$this->RegisterVariableBoolean('Evaluated', 'Evaluated', 'ShdCond.Evaluated');
		$this->RegisterVariableString('StatusMessage', 'StatusMessage', '~TextBox');

		$this->ValidateSettings();
	}
	
	// -------------------------------------------------------------------------
	private function ValidateSettings() {
		if ($this->ReadPropertyInteger('PropertyVariableID1') == 0) {
			$this->SetStatus(200);
			return;
		} else if ($this->ReadPropertyInteger('PropertyVariableID2') == 0) {
			$this->SetStatus(201);
			return;
		} else {
			$this->SetStatus(102);
		}
	}

	// -------------------------------------------------------------------------
	public function Evaluate() {
		$valueLink1 = GetValue($this->ReadPropertyInteger('PropertyVariableID1'));
		$valueLink2 = GetValue($this->ReadPropertyInteger('PropertyVariableID2'));
		$comparator = $this->ReadPropertyString("PropertyComparator");
		if ($comparator == '>') {
			$evaluated = $valueLink1 > $valueLink2;
		} else if ($comparator == '>=') {
			$evaluated = $valueLink1 >= $valueLink2;
		} else if ($comparator == '=') {
			$evaluated = $valueLink1 == $valueLink2;
		} else if ($comparator == '<=') {
			$evaluated = $valueLink1 <= $valueLink2;
		} else if ($comparator == '<') {
			$evaluated = $valueLink1 < $valueLink2;
		} else {
			throw new Exception('Unknown Comparator '.$comparator);
		}
		$this->SetValue('Evaluated', $evaluated);
		if ($evaluated) {
			if ($this->ReadPropertyString('PropertyStatusMessageTrue') === '') {
				$this->SetValue('StatusMessage', "$valueLink1$comparator$valueRef2");
			}else {
				$this->SetValue('StatusMessage', $this->ReadPropertyString('PropertyStatusMessageTrue'));
			}
		} else {
			if ($this->ReadPropertyString('PropertyStatusMessageFalse') === '') {
				$this->SetValue('StatusMessage', "$valueLink1$comparator$valueRef2");
			}else {
				$this->SetValue('StatusMessage', $this->ReadPropertyString('PropertyStatusMessageFalse'));
			}
		}
	}

}

?>