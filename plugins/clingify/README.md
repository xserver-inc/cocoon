Clingify
========

I wrote a jQuery plugin for sticky / clingy page elements with lots of cool options. 

Requires jQuery 1.7+.

Documentation and demo here: [http://theroux.github.io/clingify/](http://theroux.github.io/clingify/)

Known issues:
- There is a bug with jQuery's wrap method. If your clingy element contains a script tag, jQuery will attempt to evaluate it, and the plugin will fail.
