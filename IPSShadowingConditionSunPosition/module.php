<?php

class IPSShadowingConditionSunPosition extends IPSModule
{

	// -------------------------------------------------------------------------
	public function Create() {
		parent::Create();

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
		IPS_SetVariableProfileAssociation('ShdCond.Evaluated', 0, $this->Translate('No'), '', 255*256);
		IPS_SetVariableProfileAssociation('ShdCond.Evaluated', 1, $this->Translate('Yes'), '',255*256*256);
		
		$this->RegisterVariableBoolean('Evaluated', $this->Translate('Evaluated'), 'ShdCond.Evaluated');
		$this->RegisterVariableString('StatusMessage', $this->Translate('StatusMessage'), '~TextBox');
		$this->RegisterVariableFloat('AzimuthStart',   $this->Translate('Azimuth Start'),   '~SunAzimuth.F');
		$this->EnableAction("AzimuthStart");
		$this->RegisterVariableFloat('AzimuthEnd',     $this->Translate('Azimuth End'),     '~SunAzimuth.F');
		$this->EnableAction("AzimuthEnd");
		$this->RegisterVariableFloat('Altitude',       $this->Translate('Altitude Start'),  '~SunAltitude.F');
		$this->EnableAction("Altitude");
	}

	// -------------------------------------------------------------------------
	public function RequestAction($Ident, $Value) {
		$this->SetValue($Ident, $Value);
		switch($Ident) {
			case 'AzimuthStart':
				$this->SetValue($Ident, $Value);
				break;
			case 'AzimuthEnd':
				$this->SetValue($Ident, $Value);
				break;
			case 'Altitude':
				$this->SetValue($Ident, $Value);
				break;
			default:
				throw new Exception("Invalid ident");
		}
	}
	
	// -------------------------------------------------------------------------
	public function Evaluate() {
		$ids = IPS_GetInstanceListByModuleID("{45E97A63-F870-408A-B259-2933F7EABF74}");
		if (sizeof($ids) == 0) {
			throw new Exception("Location Modul could NOT be found!");
		}
		$locationInstanceID = $ids[0];
		$azimuth       = round(GetValue(IPS_GetStatusVariableID($locationInstanceID, 'Azimuth')), 1); 
		$azimuthStart  = $this->GetValue('AzimuthStart');
		$azimuthEnd    = $this->GetValue('AzimuthEnd');
		$altitude      = round(GetValue(IPS_GetStatusVariableID($locationInstanceID, 'Altitude')), 1); 
		$altitudeStart = $this->GetValue('Altitude');
		$this->SendDebug('Evaluate', "Evaluate Azimuth $azimuth in $azimuthStart-$azimuthEnd", 0);
		$this->SendDebug('Evaluate', "Evaluate Azimuth $altitude above $altitudeStart", 0);

		$evaluated = $azimuthStart < $azimuth && $azimuth < $azimuthEnd && $altitudeStart < $altitude;
		$this->SetValue('Evaluated', $evaluated);

		$azimuthLog  = ($azimuthStart < $azimuth && $azimuth < $azimuthEnd) ? "$azimuth in $azimuthStart-$azimuthEnd" : "$azimuth NOT in $azimuthStart-$azimuthEnd";
		$altitudeLog = ($altitudeStart < $altitude) ? "$altitude > $altitudeStart" : "$altitude <= $altitudeStart";
		$this->SendDebug('Evaluate', "Evaluated: $azimuthLog, $altitudeLog, Evaluated=".($evaluated?'Yes':'No'), 0);
		
		if ($evaluated) {
			if ($this->ReadPropertyString('PropertyStatusMessageTrue') === '') {
				$this->SetValue('StatusMessage', $azimuthLog.', '.$altitudeLog);
			}else {
				$this->SetValue('StatusMessage', $this->ReadPropertyString('PropertyStatusMessageTrue'));
			}
		} else {
			if ($this->ReadPropertyString('PropertyStatusMessageFalse') === '') {
				$this->SetValue('StatusMessage', $azimuthLog.', '.$altitudeLog);
			}else {
				$this->SetValue('StatusMessage', $this->ReadPropertyString('PropertyStatusMessageFalse'));
			}
		}
	}

}

?>