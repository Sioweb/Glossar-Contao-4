# Glossar für Contao 4

## Release der Version 2

In den nächsten Tagen wird Version 2 verwendet. Die neue Version wird weitgehends kompatibel zur Version 1 funktionieren. Ich prüfe derzeit noch, ob Contao Version 4.4 genutzt werden kann.

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

- Inhalte werden schneller durchsucht
- Es kann eingestellt werden, wie oft ein Begriff auf einer Seite ersetzt werden kann
- Contao 3 API durch Symfony ersetzen
- Die Vorschau kann nun ein Bild enthalten
- Titel, Legenden und Beschreibungen im Backend, sind nun eindeutiger