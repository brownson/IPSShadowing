<?php

class IPSShadowingDevice extends IPSModule
{

	// -------------------------------------------------------------------------
	public function Create() {
		parent::Create();

		$this->RegisterPropertyInteger ('PropertyLevelID',       0);
		//ToDo Add BladeLevel
		$this->RegisterPropertyInteger ('PropertyType',          0);
		$this->RegisterPropertyInteger ('PropertyTimeMove',      60);
		$this->RegisterPropertyFloat   ('PropertyTimeBladeUp',   2);
		$this->RegisterPropertyFloat   ('PropertyTimeBladeDown', 1);
		$this->RegisterPropertyInteger ('PropertyTimeManual',    60);
		$this->RegisterPropertyInteger ('PropertyTimer',         300);
		$this->RegisterPropertyString  ('PropertyRules',         '');
		$this->RegisterPropertyString  ('PropertyReset',         '');

		$this->RegisterAttributeInteger('Movement',              0);
		$this->RegisterAttributeFloat  ('PositionValue',         0);
		$this->RegisterAttributeInteger('PositionTime',          0);
		$this->RegisterAttributeFloat  ('DimoutUpValue',         0);
		$this->RegisterAttributeInteger('DimOutUpTime',          0);
		$this->RegisterAttributeFloat  ('DimoutDownValue',       0);
		$this->RegisterAttributeInteger('DimOutDownTime',        0);

		$this->RegisterTimer('EvaluateRulesTimer',               0, 'ShdDev_EvaluateRules($_IPS[\'TARGET\']);');
		$this->RegisterTimer('DimOutTimer',                      0, 'ShdDev_DimOut($_IPS[\'TARGET\']);');

		if ($this->ReadPropertyInteger('PropertyLevelID') > 0) {
			$this->RegisterMessage($this->ReadPropertyInteger('PropertyLevelID'), VM_UPDATE);
			$this->SetTimerInterval('EvaluateRulesTimer', $this->ReadPropertyInteger('PropertyTimer') * 1000);
		}
	}

	// -------------------------------------------------------------------------
	public function ApplyChanges() {
		parent::ApplyChanges();

		if (IPS_GetKernelRunlevel() !== KR_READY) {
			return;
		}

		if (!IPS_VariableProfileExists('ShdDev.ControlBlind')) {
			IPS_CreateVariableProfile('ShdDev.ControlBlind', 1);
		} 
		IPS_SetVariableProfileIcon('ShdDev.ControlBlind', 'Information');
		IPS_SetVariableProfileText('ShdDev.ControlBlind', '' /*Prefix*/, '' /*Suffix*/);
		IPS_SetVariableProfileDigits('ShdDev.ControlBlind', 0 /*Digits*/); 
		IPS_SetVariableProfileValues('ShdDev.ControlBlind', 0 /*Min*/, 5 /*Max*/, 0 /*Step*/); 
		IPS_SetVariableProfileAssociation('ShdDev.ControlBlind', 0, $this->Translate('Down'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlBlind', 1, $this->Translate('Stop'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlBlind', 2, $this->Translate('Up'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlBlind', 3, $this->Translate('Open'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlBlind', 4, $this->Translate('Close'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlBlind', 5, $this->Translate('Shadowing'), '', -1);

		if (!IPS_VariableProfileExists('ShdDev.ControlShutter')) {
			IPS_CreateVariableProfile('ShdDev.ControlShutter', 1);
		} 
		IPS_SetVariableProfileIcon('ShdDev.ControlShutter', 'Information');
		IPS_SetVariableProfileText('ShdDev.ControlShutter', '' /*Prefix*/, '' /*Suffix*/);
		IPS_SetVariableProfileDigits('ShdDev.ControlShutter', 0 /*Digits*/); 
		IPS_SetVariableProfileValues('ShdDev.ControlShutter', 0 /*Min*/, 4 /*Max*/, 0 /*Step*/); 
		IPS_SetVariableProfileAssociation('ShdDev.ControlShutter', 0, $this->Translate('Down'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlShutter', 1, $this->Translate('Stop'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlShutter', 2, $this->Translate('Up'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlShutter', 3, $this->Translate('Open'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlShutter', 4, $this->Translate('Close'), '', -1);

		if (!IPS_VariableProfileExists('ShdDev.ControlMarquee')) {
			IPS_CreateVariableProfile('ShdDev.ControlMarquee', 1);
		} 
		IPS_SetVariableProfileIcon('ShdDev.ControlMarquee', 'Information');
		IPS_SetVariableProfileText('ShdDev.ControlMarquee', '' /*Prefix*/, '' /*Suffix*/);
		IPS_SetVariableProfileDigits('ShdDev.ControlMarquee', 0 /*Digits*/); 
		IPS_SetVariableProfileValues('ShdDev.ControlMarquee', 0 /*Min*/, 4 /*Max*/, 0 /*Step*/); 
		IPS_SetVariableProfileAssociation('ShdDev.ControlMarquee', 0, $this->Translate('Out'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlMarquee', 1, $this->Translate('Stop'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlMarquee', 2, $this->Translate('In'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlMarquee', 3, $this->Translate('Moved In'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.ControlMarquee', 4, $this->Translate('Moved Out'), '', -1);


		if (!IPS_VariableProfileExists('ShdDev.Program')) {
			IPS_CreateVariableProfile('ShdDev.Program', 1);
		} 
		IPS_SetVariableProfileIcon('ShdDev.Program', 'Information');
		IPS_SetVariableProfileText('ShdDev.Program', '' /*Prefix*/, '' /*Suffix*/);
		IPS_SetVariableProfileDigits('ShdDev.Program', 0 /*Digits*/); 
		IPS_SetVariableProfileValues('ShdDev.Program', 0 /*Min*/, 12 /*Max*/, 0 /*Step*/); 
		IPS_SetVariableProfileAssociation('ShdDev.Program', 0,  $this->Translate('Do Nothing'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 1,  $this->Translate('Open'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 2,  $this->Translate('Close'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 3,  $this->Translate('Shadowing'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 4,  $this->Translate('Open or Shadowing'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 10, $this->Translate('10%'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 20, $this->Translate('20%'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 30, $this->Translate('30%'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 40, $this->Translate('40%'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 50, $this->Translate('50%'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 60, $this->Translate('60%'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 70, $this->Translate('70%'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 80, $this->Translate('80%'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 90, $this->Translate('90%'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 98,  $this->Translate('Automatic Off'), '', -1);
		IPS_SetVariableProfileAssociation('ShdDev.Program', 99,  $this->Translate('Error'), '', -1);
		
		if ($this->ReadPropertyInteger("PropertyType") == 0 /*Shutter*/) {
			$this->RegisterVariableInteger('Control',       $this->Translate('Control'),       'ShdDev.ControlShutter');
		} else if ($this->ReadPropertyInteger("PropertyType") == 1 /*BlindUpDown*/) {
			$this->RegisterVariableInteger('Control',       $this->Translate('Control'),       'ShdDev.ControlBlind');
		} else if ($this->ReadPropertyInteger("PropertyType") == 3 /*Marquee*/) {
			$this->RegisterVariableInteger('Control',       $this->Translate('Control'),       'ShdDev.ControlMarquee');
		} else {
		}
		$this->EnableAction("Control");
		$this->RegisterVariableInteger('Program',       $this->Translate('Program'),       'ShdDev.Program');
		$this->EnableAction("Program");
		$this->RegisterVariableBoolean('ManualMode',    $this->Translate('Manual Mode'),   '~Switch');
		$this->EnableAction("ManualMode");
		$this->RegisterVariableBoolean('Automatic',     $this->Translate('Automatic'),     '~Switch');
		$this->EnableAction("Automatic");
		$this->RegisterVariableString ('StatusMessage', $this->Translate('StatusMessage'), '~TextBox');
		
		$this->SetTimerInterval('EvaluateRulesTimer', $this->ReadPropertyInteger('PropertyTimer') * 1000);
		$this->SetTimerInterval('DimOutTimer',        0);

		IPS_SetName($this->GetIDForIdent('StatusMessage'),  $this->Translate('StatusMessage'));
		IPS_SetName($this->GetIDForIdent('Automatic'),  $this->Translate('Automatic'));
		IPS_SetName($this->GetIDForIdent('ManualMode'),  $this->Translate('Manual Mode'));
		IPS_SetName($this->GetIDForIdent('Control'),  $this->Translate('Control'));
		IPS_SetName($this->GetIDForIdent('Program'),  $this->Translate('Program'));

		//Add references
		foreach ($this->GetReferenceList() as $referenceID) {
			$this->UnregisterReference($referenceID);
		}
		$rules = json_decode($this->ReadPropertyString('PropertyRules'));
		if ($rules != null) {
			foreach ($rules as $rule) {
				$this->RegisterReference($rule->RuleID);
			}
		}

		//Unregister all messages
		$messageList = array_keys($this->GetMessageList());
		foreach ($messageList as $message) {
			$this->UnregisterMessage($message, VM_UPDATE);
		}

		//Register messages if neccessary
		$this->RegisterMessage($this->ReadPropertyInteger('PropertyLevelID'), VM_UPDATE);

		$this->ValidateSettings();
	}
	
	// -------------------------------------------------------------------------
	private function ValidateSettings() {
		$rules = json_decode($this->ReadPropertyString('PropertyRules'));
		if ($this->ReadPropertyInteger('PropertyLevelID') == 0) {
			$this->SetStatus(200);
			return;
		} else if ($rules == null && count($rules) <= 0) {
			$this->SetStatus(201);
			return;
		} else if ($this->ReadPropertyInteger('PropertyTimeMove') <= 0) {
			$this->SetStatus(202);
			return;
		} else {
			$this->SetStatus(102);
		}
	}

	// -------------------------------------------------------------------------
	public function RequestAction($Ident, $Value) {
		$this->SetValue($Ident, $Value);
		switch($Ident) {
			case 'Control':
				$this->Move($Value);
				break;
			case 'Program':
				$this->Program($Value);
				break;
			case 'ManualMode':
				$this->SetValue($Ident, $Value);
				$this->EvaluateRules();
				break;
			case 'Automatic':
				$this->SetValue($Ident, $Value);
				$this->EvaluateRules();
				break;
			default:
				throw new Exception("Invalid ident");
		}
	}

	// -------------------------------------------------------------------------
	public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
		if ($Message == VM_UPDATE) {
			$programID = $this->GetIDForIdent('Program');
			$manualSec = $this->ReadPropertyInteger('PropertyTimeManual');
			$programTS = IPS_GetVariable($programID)['VariableUpdated'];
			$this->SendDebug('MessageSink', "Received VM_UPDATE Value=".$Data[0].", PrgTS=".date('H:i:s', $programTS).", CurrTS="
			                               .date('H:i:s', time()).", RefTS=".date('H:i:s', $programTS + $manualSec), 0);
			if ($programTS + $manualSec < time() and !$this->GetValue('ManualMode') and $manualSec > 0) {
				$this->SetValue('ManualMode', true /*ManualMode*/);
				$this->SetValue('Control',    1    /*Stop*/);
				$this->SendDebug('MessageSink', "Apply ManualMode=Y", 0);
			}
		}
	}

	// -------------------------------------------------------------------------
	public function GetConfigurationForm() {
		// Read Configuration Form and Inject available Options
		$data = json_decode(file_get_contents(__DIR__ . "/form.json"), true);

		$ruleInstanceIDs = IPS_GetInstanceListByModuleID("{4C45FA90-26A9-4BCA-A6C9-0C45B4AE6D57}");
		$rules                = Array();
		$rules[0]['caption']  = 'Manual Mode';
		$rules[0]['value']    = -1;
		$idx = 1;
		foreach ($ruleInstanceIDs as $ruleInstanceID) {
			$rules[$idx]['caption']  = IPS_GetName($ruleInstanceID);
			$rules[$idx]['value']    = $ruleInstanceID;
			$idx++;
		}
		$data['elements']['7']['columns'][0]['edit']['options'] = $rules;

		$rules                = Array();
		$idx = 0;
		foreach ($ruleInstanceIDs as $ruleInstanceID) {
			$rules[$idx]['caption']  = IPS_GetName($ruleInstanceID);
			$rules[$idx]['value']    = $ruleInstanceID;
			$data['elements']['10']['columns'][0]['add'] = $ruleInstanceID;
			$idx++;
		}
		$data['elements']['10']['columns'][0]['edit']['options'] = $rules;

		return json_encode($data);
	}

	// -------------------------------------------------------------------------
	private function GetCurrentPositionPercentage() { 
		$variableID  = $this->ReadPropertyInteger('PropertyLevelID');
		$variable    = IPS_GetVariable($variableID);
		$profileName = $variable['VariableCustomProfile'] == '' ? $variable['VariableProfile'] : $variable['VariableCustomProfile'];
		$profile     = IPS_GetVariableProfile($profileName);
		$valueOld    = GetValue($variableID);
		$valueMax    = $profile['MaxValue'];
		$valueMin    = $profile['MinValue'];

		// $valueMax-$valueMin  ... 100%
		// $valueOld            ...   x%
		$result      = $valueOld * 100 / ($valueMax-$valueMin);

		if (strpos($profileName, '.Reversed') !== false) {
			return 100 - $result;
		} else {
			return $result;
		}
	}


	// -------------------------------------------------------------------------
	private function CalcMovementParameters($program) {
		$variableID  = $this->ReadPropertyInteger('PropertyLevelID');
		$variable    = IPS_GetVariable($variableID);
		$profileName = $variable['VariableCustomProfile'] == '' ? $variable['VariableProfile'] : $variable['VariableCustomProfile'];
		$profile     = IPS_GetVariableProfile($profileName);
		$valueOld    = GetValue($variableID);
		$valueMax    = $profile['MaxValue'];
		$valueMin    = $profile['MinValue'];
		$programName = $this->ProgramToName($program);
		$this->SendDebug('CalcMovementParameters', "Calculated Movement Parameters for Program=$programName"
		                                           .", LevelID=$variableID, Profile=$profileName", 0);
		$percTarget = 0;
		switch($program) {
			case 0 /*DoNothing*/:
				throw new Exception('No Position can be calculated for "DoNothing"');
				break;
			case 1 /*Open*/:
				$percTarget = 0;
				break;
			case 2 /*Close*/:
			case 3 /*Shadowing*/:
				$percTarget = 100;
				break;
			case 4 /*OpenOrShadowing*/:
				$percTarget = $this->GetCurrentPositionPercentage() > 50 ? 100 : 0; 
				break;
			default:
				$percTarget = $program;
				break;
		}
		
		// Handle Reversed Profiles
		$this->SendDebug('CalcMovementParameters', "Calculated Target Postion=$percTarget%, Max=$valueMax, Min=$valueMin", 0);
		if (strpos($profileName, '.Reversed') !== false) {
			$percTarget = 100 - $percTarget;
			$this->SendDebug('CalcMovementParameters', "Calculated Reversed Target Postion=$percTarget%", 0);
		} else {
		}
		// Calculate new Position
		// $valueMax-$valueMin ... 100%
		// $valueNew-$valueMin ... Target%
		$valueNew = ($valueMax-$valueMin) * $percTarget / 100;

		// Calculate Seconds (only valid Values for Blinds with linear movement)
		// $valueMax-$valueMin ... timeTotal
		// $valueOld-$valueMin ... timeOld
		// $valueNew-$valueMin ... timeNew
		$timeTotal = $this->ReadPropertyInteger('PropertyTimeMove');
		$timeOld   = ($valueOld-$valueMin) * $timeTotal / ($valueMax-$valueMin);
		$timeNew   = ($valueNew-$valueMin) * $timeTotal / ($valueMax-$valueMin);
		$timeDelta = round(abs($timeOld - $timeNew));
		
		$this->SendDebug('CalcMovementParameters', "Calculated Postion=$valueNew, Time=$timeDelta "
		                                          ."(Time Old=$timeOld, New=$timeNew, Total=$timeTotal)", 0);
		$this->WriteAttributeFloat('PositionValue', $valueNew);
		$this->WriteAttributeInteger('PositionTime', $timeDelta);
		
		// Calc Movement Up or Down
		$movement = ( $valueNew > $valueOld ? 0 : 2);
		$this->WriteAttributeInteger('Movement', $movement);

		// Calc DimOut Parameters
		$dimoutUpValue   = 0;
		$dimoutUpTime    = 0;
		$dimoutDownValue = 0;
		$dimoutDownTime  = 0;
		if ($this->ReadPropertyInteger("PropertyType") == 1 /*BlindUpDown*/) {
			// $timeTotal    ... $valueMax-$valueMin
			// $dimoutUpTime ... $dimoutUpValue-$valueMin
			if ($program == 2 /*Close*/) {
				$dimoutUpTime    = $this->ReadPropertyFloat('PropertyTimeBladeUp');
				$dimoutUpDelta   = round(( $dimoutUpTime * ($valueMax-$valueMin) / $timeTotal ) + $valueMin, 2);
				$dimoutUpValue   = (strpos($profileName, '.Reversed') !== false) ? $dimoutUpDelta 
				                                                                 : $valueMax - $dimoutUpDelta;
				
				$dimoutDownTime  = $this->ReadPropertyFloat('PropertyTimeBladeDown');
				$dimoutDownValue = round(( $dimoutDownTime * ($valueMax-$valueMin) / $timeTotal ) + $valueMin, 2);
				$dimoutDownValue = (strpos($profileName, '.Reversed') !== false) ? max($dimoutDownDelta - $dimoutDownValue, $valueMin) 
				                                                                 : min($valueMax - $dimoutUpDelta + $dimoutDownValue, $valueMax);

			} else if ($program == 3 /*Shadowing*/ or $program == 4 /*OpenOrShadowing*/) {
				$dimoutUpTime    = $this->ReadPropertyFloat('PropertyTimeBladeUp');
				$dimoutUpDelta   = round(( $dimoutUpTime * ($valueMax-$valueMin) / $timeTotal ) + $valueMin, 2);
				$dimoutUpValue   = (strpos($profileName, '.Reversed') !== false) ? $dimoutUpDelta 
				                                                                 : $valueMax - $dimoutUpDelta;

			} else {
			}
			
			$this->SendDebug('CalcMovementParameters', "Calculated DimoutUpPosition=$dimoutUpValue, Time=$dimoutUpTime", 0);
			$this->SendDebug('CalcMovementParameters', "Calculated DimoutDownPosition=$dimoutDownValue, Time=$dimoutDownTime", 0);
		}
		$this->WriteAttributeFloat('DimoutUpValue',   $dimoutUpValue);
		$this->WriteAttributeInteger('DimOutUpTime',    $dimoutUpTime);
		$this->WriteAttributeFloat('DimoutDownValue', $dimoutDownValue);
		$this->WriteAttributeInteger('DimOutDownTime',  $dimoutDownTime);
	}

	// -------------------------------------------------------------------------
	private function ProgramToName($program) {
		switch($program) {
			case 99 /*Error*/:
				$result = 'Error';
				break;
			case 98 /*AutomaticOff*/:
				$result = 'AutomaticOff';
				break;
			case 0 /*DoNothing*/:
				$result = 'Do Nothing';
				break;
			case 1 /*Open*/:
				$result = 'Open';
				break;
			case 2 /*Close*/:
				$result = 'Close';
				break;
			case 3 /*Shadowing*/:
				$result = 'Shadowing';
				break;
			case 4 /*Open or Shadowing*/:
				$result = 'Open or Shadowing';
				break;
			default:
				$result = "$program%";
				break;
		}
		return $result;
	}

	// -------------------------------------------------------------------------
	private function ProgramToMovement($program) {
		switch($program) {
			case 0 /*DoNothing*/:
				$result = 1 /*Stop*/;
				break;
			case 1 /*Open*/:
				$result = 3;
				break;
			case 2 /*Close*/:
				$result = 4;
				break;
			case 3 /*Shadowing*/:
				$result = 5;
				break;
			case 4 /*OpenOrShadowing*/:
				$result = $this->GetCurrentPositionPercentage() < 10 ? 3 /*Open*/ : 5 /*Shadowing*/; 
				break;
			default:
				$result = 1 /*Stop*/;
				break;
		}
		return $result;
	}

	// -------------------------------------------------------------------------
	private function MovementToProgram($movement) {
		switch($movement) {
			case 0 /*Down*/:
				$result = 3 /*Shadowing*/;
				break;
			case 1 /*Stop*/:
				throw new Exception('Program can be calculated for "Stop"');
			case 2 /*Up*/:
				$result = 1 /*Open*/;
				break;
			case 3 /*Open*/:
				$result = 1;
				break;
			case 4 /*Close*/:
				$result = 2;
				break;
			case 5 /*Shadowing*/:
				$result = 3;
				break;
			default:
				throw new Exception("Unknown Movement $movement");
				break;
		}
		return $result;
	}

	// -------------------------------------------------------------------------
	private function MoveDeviceToPosition($position) {
		$variableLevelID  = $this->ReadPropertyInteger('PropertyLevelID');
		
		IPS_Sleep(200);
		$this->SendDebug('MoveDeviceToPosition', "Execute RequestAction for $variableLevelID=$position", 0);
		$result = @RequestAction($variableLevelID, $position);
		if ($result === false) {
			$this->SendDebug('MoveDeviceToPosition', "Error executing RequestAction for $variableLevelID=$position", 0);
			$this->SetValue('Program', 99);
		}
	}

	// -------------------------------------------------------------------------
	private function EvaluateReset() {
		$resetStore      = $this->GetBuffer("BufferReset");
		if ($resetStore != '') {
			$resetStore      = json_decode($resetStore, true);
		}
		if ($resetStore == null) {
			$resetStore      = json_decode('{}', true);
		}

		$rules      = json_decode($this->ReadPropertyString('PropertyReset'));
		$resetFound = false;
		foreach ($rules as $rule) {
			if (!$resetFound) {
				ShdRule_Evaluate($rule->RuleID);
				$evaluated     = GetValue(IPS_GetStatusVariableID($rule->RuleID, 'Evaluated'));
				if (!array_key_exists($rule->RuleID, $resetStore)) {
					$resetStore[$rule->RuleID] = $evaluated;
				}
				$this->SendDebug('EvaluateReset', "Evaluate Rule ".$rule->RuleID.", Action=".$rule->Action
				                                  .", Store=".($resetStore[$rule->RuleID]?'Yes':'No'), 0);
				$resetFound =    ($rule->Action == 0 && $resetStore[$rule->RuleID] != $evaluated)
							  || ($rule->Action == 1 && $resetStore[$rule->RuleID] != $evaluated && $evaluated)
							  || ($rule->Action == 2 && $resetStore[$rule->RuleID] != $evaluated && !$evaluated);
				$statusMessage = GetValue(IPS_GetStatusVariableID($rule->RuleID, 'StatusMessage'));
				$this->SendDebug('EvaluateReset', "Evaluated Rule ".$rule->RuleID.", Message=$statusMessage, Evaluated="
				                                  .($evaluated?'Yes':'No').", Reset=".($resetFound?'Yes':'No'), 0);
				$resetStore[$rule->RuleID] = $evaluated;
			}
		}
		if ($resetFound) {
			$this->SendDebug('EvaluateReset', "Reset of ManualMode", 0);
			$this->SetValue('ManualMode', false /*ManualMode*/);
		}
		
		$this->SetBuffer('BufferReset', json_encode($resetStore));
	}

	// -------------------------------------------------------------------------
	public function EvaluateRules() {
		$this->SendDebug('EvaluateRules', "======= Evaluate Rules ===================================================", 0);
		
		// AutomaticOff
		if (!$this->GetValue('Automatic')) {
			$this->SetValue('Program', 98 /*Automatic Off*/);
			$this->SendDebug('EvaluateRules', "Automatic Off", 0);
			return;
		}
		
		$program        = 0 /*Do Nothing*/;
		$evaluated      = false;
		$statusMessage  = $this->Translate('No Rule evaluated');

		// Device is currently Moving
		if (   $this->ReadAttributeFloat('PositionValue') > 0
		    || $this->ReadAttributeInteger('DimOutUpTime') > 0 
		    || $this->ReadAttributeInteger('DimOutDownTime') > 0) {
			$statusMessage  = $this->Translate('Device is currenty Moving');
			$this->SetValue('StatusMessage', $statusMessage);
			$this->SendDebug('EvaluateRules', "Device is currenty Moving, Pos=".$this->ReadAttributeFloat('PositionValue')
			                                  .", Up=".$this->ReadAttributeInteger('DimOutUpTime')
			                                  .", Down=".$this->ReadAttributeInteger('DimOutDownTime'), 0);
			return;
		}
		// Reset of ManualMode
		$this->EvaluateReset();
		
		// Evaluate all Rules
		$rules = json_decode($this->ReadPropertyString('PropertyRules'));
		foreach ($rules as $rule) {
			if (!$evaluated) {
				if ($rule->RuleID == -1 /*ManualMode*/) {
					$evaluated     = ($rule->Evaluated == $this->GetValue('ManualMode'));
					$statusMessage = 'Manual Mode';
					$this->SendDebug('EvaluateRules', "Evaluated Rule 'ManualMode', Message=$statusMessage, Evaluated="
					                                  .($evaluated?'Yes':'No').", Program="
					                                  .$this->ProgramToName($rule->Program), 0);
				} else {
					ShdRule_Evaluate($rule->RuleID);
					$evaluated     = ($rule->Evaluated == GetValue(IPS_GetStatusVariableID($rule->RuleID, 'Evaluated')));
					$statusMessage = GetValue(IPS_GetStatusVariableID($rule->RuleID, 'StatusMessage'));
					$this->SendDebug('EvaluateRules', "Evaluated Rule ".$rule->RuleID." (".IPS_GetName($rule->RuleID)
					                                  ."), Message=$statusMessage, Evaluated=".($evaluated?'Yes':'No')
					                                  .", Program=".$this->ProgramToName($rule->Program), 0);
				}

				// We have found the current Program
				if ($evaluated) {
					$program = $rule->Program;
				}
			}
		}
		if ($program != $this->GetValue('Program')) {
			$this->SetValue('StatusMessage', $statusMessage);
			$this->Program($program);
		}
	}

	// -------------------------------------------------------------------------
	public function DimOut() {
		
		//We have reached Target Position -> Set Movement Control
		if ($this->ReadAttributeFloat('PositionValue') >  0) {
			$this->WriteAttributeFloat('PositionValue', 0);

			$program = $this->GetValue('Program');
			$this->SendDebug('DimOut', "Set Control=".$this->ProgramToMovement($program), 0);
			$this->SetValue('Control', $this->ProgramToMovement($program));
		}

		// Check for Next Steps
		if ($this->ReadAttributeInteger('DimOutUpTime') > 0) {
			$this->MoveDeviceToPosition($this->ReadAttributeFloat('DimoutUpValue'));

			$this->SendDebug('DimOut', "Start DimOutDown Timer=".$this->ReadAttributeInteger('DimOutDownTime'), 0);
			$this->SetTimerInterval('DimOutTimer', $this->ReadAttributeInteger('DimOutDownTime') * 1000);

			$this->WriteAttributeFloat('DimoutUpValue', 0);
			$this->WriteAttributeInteger('DimOutUpTime', 0);
			return;
		}

		if ($this->ReadAttributeInteger('DimOutDownTime') > 0) {
			$this->SetTimerInterval('DimOutTimer', 0);
			$this->SendDebug('DimOut', "Reset DimOutDown Timer=0", 0);
			$this->MoveDeviceToPosition($this->ReadAttributeFloat('DimoutDownValue'));

			$this->WriteAttributeFloat('DimoutDownValue', 0);
			$this->WriteAttributeInteger('DimOutDownTime', 0);
			return;
		}
	}

	// -------------------------------------------------------------------------
	public function Program($program) {
		$this->SendDebug('Program', "Execute Program ".$this->ProgramToName($program), 0);
		
		if ($program == 0 /*Do Nothing*/) {
			$this->SetValue('Program', $program);
			return;
		}
		
		// Calculate Parameters
		$this->CalcMovementParameters($program);

		// Set new Program/Movement Value
		$this->SendDebug('Program', "Set Program=$program (".$this->ProgramToName($program).")", 0);
		$this->SetValue('Program', $program);

		$this->SendDebug('Program', "Set Movement=".$this->ReadAttributeInteger('Movement'), 0);
		$this->SetValue('Control', $this->ReadAttributeInteger('Movement'));

		// Move Device to Target Position
		$this->MoveDeviceToPosition($this->ReadAttributeFloat('PositionValue'));

		// Start Timer for Dimout and final Movement Value
		if ($this->ReadAttributeInteger('PositionTime') == 0) {
			$this->SendDebug('Program', "Call Dimout (PositionTime=0)", 0);
			$this->DimOut();
		} else {
			$this->SendDebug('Program', "Set Movement Timer=".$this->ReadAttributeInteger('PositionTime'), 0);
			$this->SetTimerInterval('DimOutTimer', $this->ReadAttributeInteger('PositionTime') * 1000);
		}
	}

	// -------------------------------------------------------------------------
	public function Move($movement) {
		$this->SendDebug('Move', "Execute Movement $movement", 0);
		$this->SetValue('ManualMode', true /*ManualMode*/);
		if ($movement != 1) {
			$this->Program($this->MovementToProgram($movement));
		}
	}

}

?>