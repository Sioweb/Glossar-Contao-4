# Glossar für Contao 4

## Release der Version 2

Die neue Version ist online.

## Dokumentation

Die Dokumentation wird hier aufgebaut https://sioweb.github.io/glossar_c4/

### Short one / First steps

- Glossar unter Einstellungen aktivieren
- Glossar erstellen
- Begriffe hinterlegen
- Glossar in der Systemwartung aufbauen

## Install

Der Glossar kann mit Composer, oder dem Manager installiert werden.

```
composer req siwoeb/glossar
```

Im Contao Manager kann der Glossar ebenfalls mit `sioweb/glossar` gesucht und installiert werden.

### Glossar einrichten

Unter Themes/Seitenlayout muss nun das jQuery-Script `j_glossar` eingebunden werden.

## Update

### Von 1.x auf 2.x

Das Update kann mit Composer oder dem Contao Manager ausgeführt werden.

```
composer update
```

Im Contao Manager kann die Version ^2.0 hinterlegt und der Manager ausgeführt werden.

Nach dem Update, muss in jedem Fall geprüft werden, ob Templates im `/templates/` Verzeichnis aktualisiert werden müssen.

## Version 2

Die API für Contao 2 & 3 ist weitgehends durch Symfony-Technologien ersetzt. Ein interner Polyfill, macht den Glossar kompatibel ab Version 4.4.

### Anpassungen

- Damit die Seite nicht mit Glossar-Links vollgespamt wird, kann ausgewählt werden, wie oft ein Begriff pro Seite ersetzt werden darf
- Glossar neu Aufbauen ist nun schneller und kann besser mit mehreren Domains umgehen
- Labels und Bezeichnungen wurden verbessert, auch die englische Übersetzung - wenn auch nicht unbedingt schön - ist vollständig
- Ajax-Requests laufen nun über eigene Routen
- Alle Hooks werden über Services angesprochen
- Einige Methoden, wurden als Service registriert
- Glossar-Tabellen werden mit Doctrine-Entities erzeugt
- Für Contao 4.4 enthält der Glossar fallbacks
- Contao 4.4 - 4.7.1 enthält den Maintainance-Fix, damit der Glossar aufgebaut werden kann
- Diverse Bugs sollten nun endgültig behoben sein
- Much more geeky sparkling fun...
