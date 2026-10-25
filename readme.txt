=== PS Mitgliedschaften ===
Contributors: PSOURCE
Tags: classicpress-plugin, classicpress, membership, multisite
Requires at least: 4.9
Tested up to: 7.1 
ClassicPress 2.7.4
Stable tag: 1.0.3
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Das leistungsstärkste, benutzerfreundlichste und flexibelste Mitgliedschafts-Plugin für ClassicPress-Seiten.

== Description ==

Erstelle mit diesem leistungsstarken und skalierbaren Mitgliedschafts-Plugin für ClassicPress ganz einfach eine beliebte und profitable Mitgliederseite

Entwerfe und baue jedes Mitgliedersystem, das Du dir mit PS Mitgliedschaften ausdenken kannst.
Richte alles von einer einfachen Mitgliederseite aus ein, um exklusiven Zugriff auf Deine Schokoladen-Brownie-Rezepte zu erhalten, oder versuch sogar, das nächste Facebook mit einer einzigartigen Multisite-Integration zu erstellen.

Einfach, intuitiv und noch leistungsfähiger
PS Mitgliederschaften ist der Nachfolger unseres äußerst beliebten Membership-Plugins, das von Grund auf neu geschrieben wurde und noch mehr Funktionen bietet – einschließlich der Möglichkeit, Inhalte über ein gesamtes Netzwerk von einem Ort aus zu verwalten.

PS Mitgliederschaften ist das benutzerfreundlichste Mitgliedschafts-Plugin für ClassicPress Multisite.

Du kannst uns gerne Deine optimierten .po/.mo Dateien für Deine Muttersprache zukommen lassen. 
Nutze die Möglichkeit dazu auf GitHub oder sende Deine Dateien an: webmaster@n3rds.work

== ChangeLog ==

= 1.0.4 =

* Fix: PHP-Deprecation-Warnungen bei Cron- und Besucheranfragen behoben, die durch null als Array-Index im Benutzer-Cache ausgelöst wurden.

= 1.0.3 =
* Sicherheit: Weiterleitungsvalidierung beim Ajax-Login, beim Löschen mehrerer Mitgliedschaften und bei der Netzwerkauswahl korrigiert. Nur HTTP(S)-Ziele derselben Herkunft werden zugelassen; Pfade mit doppeltem Schrägstrich können keine externe Weiterleitung mehr auslösen.
* Tests: Regressionstests für gültige und ungültige Weiterleitungen in Quell- und minimierten Skripten ergänzt.
* Übersetzungen: Englische Sprachkataloge (USA und Großbritannien) mit dem aktuellen Quellcode abgeglichen, Übersetzungen geprüft und ergänzt sowie die kompilierten Sprachdateien aktualisiert.
* Fix: Fehlerhafte Platzhalter in der Testzeitraum-Beschreibung und HTML-Tags in übersetzbaren Hilfs- und Einrichtungstexten korrigiert.
* Tests: Prüfung der englischen Sprachkataloge auf Vollständigkeit, Platzhalter, Pluralformen und Übereinstimmung mit den kompilierten Sprachdateien ergänzt.

= 1.0.2 =
* Fix: Konflikt im Registrierungsformular behoben, der in Kombination mit externen register_form-Hooks zu doppelten Passwortfeldern fuehren konnte.
* Fix: reCaptcha-Validierung im Registrierungsablauf stabilisiert und Integrationsverhalten zwischen reCaptcha-Add-ons verbessert.
* Fix: Pointer-Polyfill hinzugefügt und die Skripte und Stile für verbesserte Benutzerführung aktualisiert

= 1.0.1 =
* Security Fixes

= 1.0.0 =
* Release 