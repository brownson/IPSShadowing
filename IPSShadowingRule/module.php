<?php

class IPSShadowingRule extends IPSModule
{

	// -------------------------------------------------------------------------
	public function Create() {
		parent::Create();

		$this->RegisterPropertyString  ('PropertyConditions',          '[]');
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

		if (!IPS_VariableProfileExists('ShdRule.Evaluated')) {
			IPS_CreateVariableProfile('ShdRule.Evaluated', 0);
		} 
		IPS_SetVariableProfileIcon('ShdRule.Evaluated', 'Information');
		IPS_SetVariableProfileText('ShdRule.Evaluated', '' /*Prefix*/, '' /*Suffix*/);
		IPS_SetVariableProfileDigits('ShdRule.Evaluated', 0 /*Digits*/); 
		IPS_SetVariableProfileValues('ShdRule.Evaluated', 0 /*Min*/, 1 /*Max*/, 0 /*Step*/); 
		IPS_SetVariableProfileAssociation('ShdRule.Evaluated', 0, $this->Translate('No'), '', 255*256);
		IPS_SetVariableProfileAssociation('ShdRule.Evaluated', 1, $this->Translate('Yes'), '', 255*256*256);
		
		$this->RegisterVariableBoolean('Evaluated', $this->Translate('Evaluated'), 'ShdRule.Evaluated');
		$this->RegisterVariableString('StatusMessage', $this->Translate('StatusMessage'), '~TextBox');
		
       $conditions = json_decode($this->ReadPropertyString('PropertyConditions'));
	}


	// -------------------------------------------------------------------------
	public function Evaluate() {
		$evaluated  = true;
		$statusMessage  = '';

		$conditions = json_decode($this->ReadPropertyString('PropertyConditions'));
		foreach ($conditions as $condition) {
			// Evaluate Condition
			$this->SendDebug('Evaluate', "Evaluate Condition ".$condition->ConditionID.", Name=".IPS_GetName($condition->ConditionID), 0);
			ShdRule_Evaluate($condition->ConditionID);
			$conditionResult  = GetValue(IPS_GetStatusVariableID($condition->ConditionID, 'Evaluated'));
			$conditionMessage = GetValue(IPS_GetStatusVariableID($condition->ConditionID, 'StatusMessage'));

			// Build Rule Result
			$evaluated     = $evaluated && ($condition->Evaluated == $conditionResult);
			if ($statusMessage != '') {
				$statusMessage = $statusMessage.', ';
			}
			$statusMessage = $statusMessage.$conditionMessage;
			$this->SendDebug('Evaluate', "Evaluated Condition=".($conditionResult?'Yes':'No').", StatusMessage=$conditionMessage", 0);
			$this->SendDebug('Evaluate', "Calc Rule Result=".($evaluated?'Yes':'No'), 0);
		}
		$this->SetValue('Evaluated', $evaluated);
		$this->SendDebug('Evaluate', "Evaluated Rule=".($evaluated?'Yes':'No').", StatusMessage=$statusMessage", 0);
		if ($evaluated) {
			if ($this->ReadPropertyString('PropertyStatusMessageTrue') === '') {
				$this->SetValue('StatusMessage', $statusMessage);
			}else {
				$this->SetValue('StatusMessage', $this->ReadPropertyString('PropertyStatusMessageTrue'));
			}
		} else {
			if ($this->ReadPropertyString('PropertyStatusMessageFalse') === '') {
				$this->SetValue('StatusMessage', $statusMessage);
			}else {
				$this->SetValue('StatusMessage', $this->ReadPropertyString('PropertyStatusMessageFalse'));
			}
		}
	}

}

?>