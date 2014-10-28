elFinder Roundcube Plugin
-------------------------

This roundcube plugin integrates elFinder File Manager to Roundcube.
It has only been tested on Roundcube 1.0.0 and is based on elFinder 2.x as-up
2014-10-09.

**Please note this plugin is not intended to be used in a multi-user RC
installation. There are several security issues using it that way. Use at your
own risk!**

Installation
------------
    $ cd /path/to/roundcube/plugins
    $ git clone http://github.com/puzzle1536/elfinder-roundcube-plugin.git elfinder

The plugin folder must be named `elfinder`.

Add `elfinder` to `$rcmail_config['plugins']` in `config/config.inc.php`.

You can use config.inc.php.dist as an example for configuration.
Update the file and rename it to `config.inc.php`.

Usage
-----
When listing or reading messages. you'll be able to save attachments to the
server filesystem.  When composing messages, you'll be able to save attachments
to the server filesystem.  The `File Manager` tab allows you to browse these
files later.

You may reconfigure elFinder for your own requirements through `php/` subfolder.

Contact
-------

Please report any bug or enhancement requests to the [github project page](https://github.com/puzzle1536/elfinder-roundcube-plugin/issues?q-is%3Aopen+is%3Aissue)

License
-------

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

