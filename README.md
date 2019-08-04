# IMT RSS Aggregator

## Requirements

* PHP `>7`
* composer

Simple RSS aggregator that renders to HTML output. `sites.json` contains information on websites which the scripts will reference when pulling data.

Lots of room for improvement!

## Todo

* Some kind of caching, it takes a while to run with four sites
* Prettier CSS
* Actually write the Joomla aggregator
* Maybe a table of contents at top of homepage, with anchors to sections
* Socialist appeal is only returning five articles, see if there's a way to get more

## Low-priority todo's

* Move the code that decides which RSS aggregator to construct out of `index.php`
