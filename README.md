# IPSShadowing Module

Folgende Module beinhaltet das IPSShadowing Repository:

- __IPSShadowingConditionLimit__ ([Dokumentation](IPSShadowingConditionLimit))  
	Beschattungsbedingung zum Vergleich einer Variable mit einem Limit

- __IPSShadowingConditionEquals__ ([Dokumentation](IPSShadowingConditionEquals))  
	Beschattungsbedingung zum Vergleich einer Variable mit einem Referenz Wert

- __IPSShadowingConditionCompare__ ([Dokumentation](IPSShadowingConditionCompare))  
	Beschattungsbedingung zum Vergleich von 2 Variablen

- __IPSShadowingConditionTimeInRange__ ([Dokumentation](IPSShadowingConditionTimeInRange))  
	Beschattungsbedingung zum Vergleich von Zeitpunkten

- __IPSShadowingConditionSunPosition__ ([Dokumentation](IPSShadowingConditionSunPosition))  
	Beschattungsbedingung zur Auswertung der Sonnenposition

- __IPSShadowingRule__ ([Dokumentation](IPSShadowingRule))  
	Gruppierung von mehreren Beschattungsbedingungen zur einer Beschattungsregel

- __IPSShadowingDevice__ ([Dokumentation](IPSShadowingDevice))  
	Evaluierung von Beschattungsregeln und Ansteuerung einer Beschattung 

### Übersicht

IPSShadowing besteht aus mehreren Modulen:

* Beschattungsbedingung - Auswertung von Bedingungen wie Vergleich von Werten, Uhrzeiten, Sonnenposition usw.
* Beschattungsregeln - ein oder mehrere Bedingungen können zu einer Regel gruppiert werden
* Beschattungsdevice - Verknüpfung von Regeln mit Aktionen
