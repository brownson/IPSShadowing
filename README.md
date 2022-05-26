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
* Beschattungsregel - ein oder mehrere Bedingungen können zu einer Regel gruppiert werden
* Beschattungsgerät - Verknüpfung von Regeln mit Aktionen

Typische Anwendungsbeispiele:

* Beschattung in Abhängigkeit von Temperatur, Sonnenstand und Anwesenheit
* Schließen einer Beschattung in Abhängigkeit der Dämmerung oder einer bestimmter Uhrzeit
* Bei einer manuellen Ansteuerung der Beschattung keine automatisierte Regel mehr anwenden
