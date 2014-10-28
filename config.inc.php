<?php

/**
 * elFinder plugin for RoundCube
 *
 * plugin that includes elFinder into Roundcube for various purpose
 *
 * Copyright 2014 - Puzzle <puzzle1536@gmail.com>
 *
 * @version 0.1
 * @author puzzle1536@gmail.com
 * @licence GNU GPLv3+
 * @url http://github.com/puzzle1536/elfinder-roundcube-plugin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 *
 *
 */

// Server Filesytem path to the files to display
$rcmail_config['files_path'] = '/var/www/webdav/Perso';
// "Public" URL to get the files from a web browser
$rcmail_config['files_url']  = '/files';
$rcmail_config['logs']  = '/files/elfinder.log';

