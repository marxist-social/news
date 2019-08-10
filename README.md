# IMT RSS Aggregator

## Requirements

* PHP `>7`
* composer (PSR-4(?) `/src`, and PHPMailer off of Packagist)

Simple RSS aggregator that renders to HTML output. `sites.json` contains information on websites which the scripts will reference when pulling data.

Lots of room for improvement!

## Todo

* Actually write the Joomla aggregator
* Maybe a table of contents at top of homepage, with anchors to sections. Fixed on mobile?
* Socialist appeal is only returning five articles, see if there's a way to get more
* Add Lal Salaam hacks (encoding/related) to atom feed (or api better? idk, do api first)
* add wordpress api aggregator
* Remove fightback aggregator
* Add fightback hacks to wordpress api aggregator (author)
* Make the jsondatabaseprocessor read {'keys' => [...], 'data' => [...]} (default null basically. Is a bit tough...)
