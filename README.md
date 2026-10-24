# PS Mitgliedschaften

**Deutsch** | [English](README.en.md)

[![Version](https://img.shields.io/badge/Version-1.0.3-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-bis%207.1.0-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.4-03768e?style=flat-square)
[![Lizenz](https://img.shields.io/badge/Lizenz-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)


**Deine Inhalte. Deine Community. Deine Mitgliedschaften.**

Baue eine Mitgliederseite, die zu Deinem Projekt passt: vom kostenlosen Community-Bereich über ein digitales Magazin bis zum kostenpflichtigen Zugang zu exklusiven Inhalten. PS Mitgliedschaften verbindet flexible Zugriffsregeln, Mitgliederverwaltung und Zahlungsabwicklung in einem Plugin für ClassicPress und WordPress.

## Was Du damit aufbauen kannst

- **Eine Community mit exklusivem Bereich:** Stelle ausgewählte Inhalte nur registrierten Mitgliedern zur Verfügung.
- **Ein digitales Magazin:** Kombiniere frei zugängliche Beiträge mit geschützten Artikeln und Downloads.
- **Eine Lern- oder Wissensplattform:** Gib Inhalte zeitversetzt frei, statt alles auf einmal bereitzustellen.
- **Ein Mitgliederangebot mit Bezahlung:** Verwalte kostenlose Zugänge, einmalige Zahlungen und wiederkehrende Beiträge.
- **Ein geschütztes Multisite-Netzwerk:** Verwalte Zugriffsregeln bei aktivierter netzwerkweiter Schutzfunktion zentral.

## Die wichtigsten Funktionen

| Bereich | Möglichkeiten |
| --- | --- |
| Mitgliedschaften | Standard-, Intervall-, Gast- und Keine-Mitgliedschaft; kostenlose und kostenpflichtige Angebote |
| Inhaltsschutz | Beiträge, Seiten, Kategorien, Beitragstypen, Medien, URLs und einzelne Inhaltsbereiche |
| Zeit & Abrechnung | Befristete Zugänge, wiederkehrende Zahlungen und Testzeiträume |
| Mitgliederverwaltung | Mitglieder zuordnen, Abonnements verwalten und Status sowie Zahlungen überblicken |
| Kommunikation | Automatisierte E-Mails und individuelle Schutzmeldungen |
| Erweiterungen | Gutscheine, Einladungscodes, Profilfelder, Attribute und anteilige Verrechnung |
| Integrationen | Unter anderem BuddyPress, bbPress, Mailchimp, WPML und reCaptcha |
| Multisite | Netzwerkweiter Inhaltsschutz und zentrale Verwaltung |

Viele Funktionen werden über die integrierten Add-ons aktiviert. So kannst Du den Umfang gezielt auf Deine Website abstimmen. Externe Integrationen benötigen das jeweilige zusätzliche Plugin oder ein Konto beim betreffenden Dienst.

### Vier Mitgliedschaftsarten

- **Standardmitgliedschaft:** Geschützte Inhalte für die zugeordneten Mitglieder.
- **Intervall-Mitgliedschaft:** Inhalte zeitversetzt freigeben, beispielsweise für aufeinander aufbauende Lektionen.
- **Gastmitgliedschaft:** Inhalte ausschließlich für abgemeldete Besucher.
- **Keine-Mitgliedschaft:** Inhalte für angemeldete Benutzer, die noch keiner anderen Mitgliedschaft beigetreten sind.

### Zahlungen nach Deinem Modell

Das Plugin enthält Anbindungen für **Stripe**, **PayPal** und **Authorize.net** sowie **manuelle Zahlungen**. Kostenlose Mitgliedschaften benötigen keinen externen Zahlungsanbieter.

Welche Abrechnungsmodelle verfügbar sind, hängt vom gewählten Gateway und seiner Konfiguration ab. Für externe Anbieter benötigst Du eigene Zugangsdaten; deren Gebühren und Bedingungen gelten unabhängig vom Plugin.

> Bei manuellen Zahlungen wird die Zahlung nicht automatisch bestätigt. Prüfe den Zahlungseingang und markiere die Rechnung anschließend als bezahlt.

## Installation

### Voraussetzungen

- Eine ClassicPress- oder WordPress-Installation.
- **PHP 8.0 oder neuer** gemäß den aktuellen Projektanforderungen.
- Für Zahlungsanbieter: die passenden Konten, API-Zugangsdaten und gegebenenfalls Webhooks.
- Für einen produktiven Mitglieder- und Zahlungsbereich: HTTPS.

Die Versionsangaben und Anforderungen findest Du in der [Plugin-Readme](readme.txt). Prüfe Updates und die gewünschten Integrationen vor dem Produktiveinsatz in einer Testumgebung.

### Plugin installieren

1. Lade ein verfügbares Plugin-Paket aus den [Releases](https://github.com/Power-Source/ps-mitgliedschaften/releases) herunter oder klone dieses Repository.
2. Lege die Plugin-Dateien unter `wp-content/plugins/ps-mitgliedschaften/` ab. Die Datei `membership2.php` muss direkt in diesem Verzeichnis liegen.
3. Aktiviere **PS Mitgliedschaften** in der Plugin-Verwaltung.
4. Folge der Einrichtung und kontrolliere die angelegten beziehungsweise zugewiesenen Mitgliedschaftsseiten.
5. Erstelle Deine erste Mitgliedschaft und lege die Schutzregeln fest.

Für die Nutzung der mitgelieferten JavaScript- und CSS-Dateien ist **kein Node.js-Build erforderlich**.

## In wenigen Schritten startklar

### 1. Mitgliedschaften definieren

Lege fest, welche Angebote es geben soll, wer ihnen beitreten darf und ob der Zugang kostenlos, befristet oder kostenpflichtig ist.

### 2. Inhalte schützen

Wähle die passenden Schutzregeln für Seiten, Beiträge, Kategorien oder weitere Inhaltstypen. Aktiviere zusätzliche Schutzfunktionen nur dort, wo Du sie benötigst.

### 3. Registrierung und Mitgliedsseiten einrichten

Prüfe die Seiten für Anmeldung, Registrierung, Mitgliedschaftsauswahl und Konto. Passe Schutzmeldungen und E-Mail-Texte an Deine Community an.

### 4. Zahlungsanbieter verbinden

Konfiguriere für kostenpflichtige Angebote ein passendes Gateway. Teste den gesamten Ablauf zunächst mit den Testmöglichkeiten des jeweiligen Anbieters.

### 5. Aus Sicht Deiner Mitglieder testen

Kontrolliere den Zugriff als Gast, als angemeldeter Benutzer ohne Mitgliedschaft und als Mitglied. Prüfe außerdem Registrierung, Zahlung, Ablauf und Kündigung.

> **Wichtig bei Caching und Medienschutz:** Stelle sicher, dass Seiten-Caches und CDNs keine geschützten Inhalte an Besucher ausliefern. Teste bei Downloads auch den direkten Dateiaufruf; eine geschützte Inhaltsseite allein garantiert keinen Schutz der verlinkten Datei.

## Multisite

PS Mitgliedschaften unterstützt Multisite-Umgebungen und bietet eine netzwerkweite Schutzfunktion. Aktiviere und konfiguriere diese bewusst passend zu Deinem Netzwerk.

Prüfe die Regeln auf jeder betroffenen Website und mit unterschiedlichen Benutzerkonten. Eine Netzwerkinstallation ersetzt nicht die Kontrolle der einzelnen Zugriffswege.

## Sprachen & Übersetzungen

Die Originaltexte sind überwiegend **Deutsch**. Zusätzlich werden Sprachdateien für folgende Sprachvarianten mitgeliefert:

- Englisch (USA und Großbritannien)
- Französisch
- Italienisch
- Spanisch

Die englischen Kataloge wurden mit dem aktuellen Quellcode abgeglichen und vollständig als PO- und MO-Dateien erstellt. Der Übersetzungsstand der anderen Sprachen kann abweichen.

Sprachdateien liegen in [`languages/`](languages/); die Textdomain lautet `membership2`. Übersetzungsverbesserungen sind willkommen. Eine Anleitung zur Pflege und Prüfung findest Du in der [Build-Dokumentation](docs/BUILD.md#english-translations).

## Für Entwickler

### Repository einrichten

```bash
git clone https://github.com/Power-Source/ps-mitgliedschaften.git
cd ps-mitgliedschaften
npm ci
```

Für das Asset-Tooling wird **Node.js 18 oder neuer** benötigt.

### Assets bauen & Tests ausführen

```bash
# JavaScript und CSS minimieren
npm run build

# Entwicklungsmodus mit Dateibeobachtung und Sourcemaps
npm run dev

# Regressionstests für Weiterleitungen
npm test
```

Der Build schreibt die vom Plugin verwendeten minimierten Dateien direkt neben die Quelldateien. Baue sie nach Änderungen neu, damit Quell- und Auslieferungsstand zusammenpassen.

Die englischen Übersetzungen prüfst Du mit:

```bash
php scripts/check-translations.php
```

Die Prüfung nutzt die POMO-Klassen einer umgebenden WordPress-/ClassicPress-Installation und benötigt keine Datenbankverbindung. Bei einem eigenständigen Checkout übergibst Du den Installationspfad:

```bash
php scripts/check-translations.php /pfad/zu/wordpress
```

Weitere Details zu Build, Distribution und Sprachdateien stehen in [`docs/BUILD.md`](docs/BUILD.md).

### Orientierung im Projekt

| Pfad | Inhalt |
| --- | --- |
| [`membership2.php`](membership2.php) | Plugin-Einstiegspunkt |
| [`app/`](app/) | Kernfunktionen, Verwaltung, Ansichten, Regeln und Assets |
| [`app/addon/`](app/addon/) | Integrierte Erweiterungen |
| [`app/gateway/`](app/gateway/) | Zahlungsanbindungen |
| [`premium/`](premium/) | Weitere mitgelieferte Erweiterungen und Gateways |
| [`languages/`](languages/) | Übersetzungsvorlage sowie PO- und MO-Dateien |
| [`scripts/`](scripts/) | Build-Werkzeuge und Prüfungen |
| [`docs/`](docs/) | Projekt- und Build-Dokumentation |

Der Verzeichnisname `premium/` stammt aus der Projektstruktur. Er ist kein Hinweis auf einen zusätzlichen Kaufzwang: PSOURCE stellt seine Projekte gemäß der [Projektbeschreibung](docs/about.md) kostenlos und quelloffen bereit.

## Mitmachen & Hilfe

Du hast einen Fehler entdeckt, eine Idee oder eine bessere Übersetzung?

- **Fehler und Vorschläge:** Öffne ein [Issue](https://github.com/Power-Source/ps-mitgliedschaften/issues).
- **Code und Übersetzungen:** Reiche einen [Pull Request](https://github.com/Power-Source/ps-mitgliedschaften/pulls) mit einer nachvollziehbaren Beschreibung ein.
- **Bei Fehlerberichten:** Nenne Plugin-, PHP- und CMS-Version, relevante Integrationen, Schritte zur Reproduktion sowie erwartetes und tatsächliches Verhalten.

Veröffentliche keine Passwörter, API-Schlüssel, Zahlungsdaten oder personenbezogenen Mitgliederdaten in Issues, Screenshots oder Logs.

## Änderungen & Lizenz

Die Versionshistorie wird zentral im [Changelog der Plugin-Readme](readme.txt) gepflegt.

PS Mitgliedschaften steht unter der **GNU General Public License, Version 2 oder später (GPL-2.0-or-later)**. Du darfst das Plugin unter den Bedingungen dieser Lizenz verwenden, verändern und weitergeben. Für mitgelieferte Drittanbieter-Bibliotheken gelten deren jeweilige Lizenzhinweise.

[Vollständiger GPL-v2-Lizenztext](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)

---

**Ein Projekt von PSOURCE – für Websites, die aus Besuchern eine Community machen.**
