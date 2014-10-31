elFinder File Manager plugin for RoundCube
==========================================

This software integrates the elFinder File Manager to Roundcube. 
It has only been tested on Roundcube 1.0 and is based on elFinder 2.x git
version as-up 2014-10-09, with few modifications available at
[github:puzzle1536/elFinder]([https://github.com/puzzle1536/elFinder)

This plugin enables browsing files available on the server within RoundCube.
It also enables therefore the possibility to add attachments from the server
filesystem and save mails or attachments to the server filesystem.

**Please note this plugin is not yet intended to be used in a multi-user RC
installation. This would lead to serious security issue using it that way.
Use at your own risk!**

Installation
============
```
$ cd /path/to/roundcube/plugins
$ git clone http://github.com/puzzle1536/elfinder-roundcube-plugin.git elfinder
```

The plugin folder must be named `elfinder`.

Add `elfinder` to `$rcmail_config['plugins']` in `config/config.inc.php`.

You can use config.inc.php.dist as an example for configuration.
Update the file and rename it to `config.inc.php`.

Usage
=====
This plugin enables the following actions :

* `Save message(s) (elFinder)` : Save mails to elFinder when reading or listing mails,
* `Save attachment(s) (elFinder)` : Save attachments to elFinder when reading or listing mails,
* Load attachments from elFinder when composing mail,
* Browse server files from a dedicated `File Manager` tab,
* Online Edit text files within elFinder tab.
* Online html files within elFinder tab (with tinyMCE 4.x) .

You may reconfigure elFinder for your own requirements through
`php/connector.php` subfolder.  However please note only LocalFileSystem driver
is supported by this plugin.

Contact
=======

Please report any bug or enhancement requests to the [github project page](https://github.com/puzzle1536/elfinder-roundcube-plugin/issues?q-is%3Aopen+is%3Aissue)

Licenses
========

elFinder Roundcube plugin license
---------------------------------

`elFinder roundcube plugin` is issued under GPLv3+ license

```
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
```

elFinder license
----------------

`elFinder` is issued under a 3-clauses BSD license.
This concern all files under `php/`, `js/` and `skins/larry/css/`

```
Copyright (c) 2009-2012, Studio 42
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
* Redistributions of source code must retain the above copyright notice, this
list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice,
this list of conditions and the following disclaimer in the documentation
and/or other materials provided with the distribution.
* Neither the name of the Studio 42 Ltd. nor the names of its contributors may
be used to endorse or promote products derived from this software without
specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL "STUDIO 42" BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
```

tinyMCE license
----------------

`tinyMCE` is issued under [LGPLv2.1 license](https://www.gnu.org/licenses/lgpl-2.1.html).
This concerns all files under `js/tinymce`

