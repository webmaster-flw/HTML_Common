HTML_Common
==============

The HTML_Common package provides methods for html code display and attributes handling.

* Methods to set, remove, update html attributes.
* Handles comments in HTML code.
* Handles layout, tabs, line endings for nicer HTML code.

This package is based in the [original PEAR library](http://pear.php.net/package/HTML_Common), but the code has been 
updated to be compatible with newer versions of PHP.

The intention of this project is not to work as a modern alternative, but to provide support for legacy projects still
using this library. Please, consider migrating to newer alternatives if possible.

### Version 1.3 notes

HTML_Common was designed for PHP version 4, where all class methods and properties were public.

Even when the elements could be marked as protected or private in comments or using naming conventions, they were still
accessible. Version 1.3 keeps those elements public for compatibility reasons, while version 2 will apply the correct
visibility modifiers.

Version 1.3 has also been marked as compatible with PHP 5.4, while version 2 will only be compatible with version 7.

* Compatible with PHP 5.4 and newer versions, including version 7
* Use of composer autoloader to replace includes.
