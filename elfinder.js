/**
 * elFinder plugin for RoundCube
 *
 * This plugin integrates elFinder into Roundcube for various purpose.
 *
 * Copyright 2014 - Puzzle <puzzle1536@gmail.com>
 *
 * @version 0.1
 * @author puzzle1536@gmail.com
 * @licence GNU GPLv3+
 * @url http://github.com/puzzle1536/elfinder-roundcube-plugin.git
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

var dialog;
var displayed;
var selected_attachment_id = 0;
var selected_message_id = 0;
var selected_function = 'plugin.elfinder.save_attachments';

var elfinder_init = function() {

    // Add <div> element for elfinder
    $( "#mainscreen" ).append( "<div id=\"elfinder\" class=\"popupmenu elfinder-popup\"></div>" );

    selected_message_id = rcmail.env.uid;

    // Register function for clicks on message menu 'elFinder (save msg)'
    rcmail.register_command('elfinder-save-msg', function() {
         selected_function = 'plugin.elfinder.save_messages';
         window.parent.elfinder_save();
    }, !rcmail.message_list);

    // Register function for clicks on message menu 'elFinder (attachments)'
    rcmail.register_command('elfinder-save-all', function() {
         // We set selected_attachment_id to 0, to save all attachments
         selected_function = 'plugin.elfinder.save_attachments';
         selected_attachment_id =  0;
         window.parent.elfinder_save();
    }, !rcmail.message_list);

    // Register function for clicks on attachment menu 'elFinder'
    rcmail.register_command('elfinder-save', function() {
         selected_function = 'plugin.elfinder.save_attachments';
         window.parent.elfinder_save();
    }, true);

    // Add event listener on attachement menu open to store the correct attachment id
    rcmail.addEventListener('menu-open', function(p) {
        window.parent.selected_attachment_id = p.props.id;
    });

    // Add event listener to enable/disable 'elfinder' menu in message menu
    if (rcmail.message_list) {
        rcmail.message_list.addEventListener('select', function(list) {
            rcmail.enable_command('elfinder-save-all', list.get_selection().length > 0);
            rcmail.enable_command('elfinder-save-msg', list.get_selection().length > 0);
            selected_message_id = list.get_selection();
        });
    }

}

var elfinder_load = function(elfinder_function) {
   if (!dialog) {
       // Somebody clicked on the "elfinder" button for the 1st time
       // Just open the elfinder window

       dialog = $('#elfinder').elfinder({
               url : 'plugins/elfinder/php/connector.php',
               lang : 'en',
               width:  $("#mainscreen").innerWidth()-100,
               height: $("#mainscreen").innerHeight()-100,
               commands : [
                    'open', 'reload', 'home', 'up', 'back', 'forward', 'getfile',
                    'upload', 'edit', 'search', 'info', 'view', 'help', 'sort'
               ],
               defaultView : 'list',
               contextmenu : {
                   // navbarfolder menu
                   navbar : ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'info'],
               
                   // current directory menu
                   cwd    : ['reload', 'back', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|', 'info'],
               
                   // current directory file menu
                   files  : [
                       'getfile', '|','open', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
                       'rm', '|', 'edit', 'rename', 'resize', '|', 'archive', 'extract', '|', 'info'
                   ]
               },
               commandsOptions : {
                   getfile : {
                       oncomplete : 'close',
                       multiple : true,
                       folders : false,
                   },
                   upload : {
                       ui : 'uploadbutton'
                   },

               },
               getFileCallback : function(files, fm) {
                
                   for (id in files) {
                       // Lock interface while saving
                       lock = rcmail.set_busy(true, 'elfinder.wait_load');
                       ts = new Date().getTime();
                       rcmail.http_request('plugin.elfinder.load_attachments',
                                           { _id:rcmail.env.compose_id,
                                             _uploadid:ts,
                                             _filepath:files[id].url,
                                           }, lock);
                   }
                   displayed = false;
            }
        });
        displayed = true;
    } else {
       // Somebody clicked on the "elfinder" button, toggle display
       if (!displayed) {
           dialog.elfinder('show');
           displayed = true;
       } else {
           dialog.elfinder('hide');
           displayed = false;
       }
    }
}

var elfinder_save = function() {
   if (!dialog) {
       // Somebody clicked on the "elfinder" button for the 1st time
       // Just open the elfinder window

       dialog = $('#elfinder').elfinder({
               url : 'plugins/elfinder/php/connector.php',
               lang : 'en',
               width:  $("#mainscreen").innerWidth()-100,
               height: $("#mainscreen").innerHeight()-100,
               defaultView : 'list',
               uiOptions: {
                   cwd : {oldSchool : true}
               },
               commandsOptions : {
                   getfile : {
                       oncomplete : 'close',
                       folders : true,
                   }
               },
               commands : [
                    'open', 'reload', 'home', 'up', 'back', 'forward', 'getfile',
                    'upload', 'edit', 'search', 'info', 'view', 'help', 'sort'
               ],
               contextmenu : {
                   // navbarfolder menu
                   navbar : ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'info'],
               
                   // current directory menu
                   cwd    : ['reload', 'back', '|', 'getfile', 'mkdir', '|', 'info'],
               
                   // current directory file menu
                   files  : [
                       'open', '|','getfile', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
                       'rm', '|', 'edit', 'rename', 'resize', '|', 'archive', 'extract', '|', 'info'
                   ]
               },
               getFileCallback : function(folder, fm) {
                
               // Lock interface while saving
               lock = rcmail.set_busy(true, 'elfinder.wait_save');
               rcmail.http_request(selected_function,
                                    { _dirpath:folder.url,
                                      _uid:selected_message_id,
                                      _attachment_id:selected_attachment_id }, lock);
               displayed = false;

            }
        });
        displayed = true;
    } else {
       // Somebody clicked on the "elfinder" button, toggle display
       if (!displayed) {
           dialog.elfinder('show');
           displayed = true;
       } else {
           dialog.elfinder('hide');
           displayed = false;
       }
    }
}
    
// This function close elFinder on escape keypress
var grab_excape_key = function(evt) {
    // This could be called from both main window and iframe
    if (window.parent.displayed && evt.keyCode == 27) {
        window.parent.dialog.elfinder('hide');
        window.parent.displayed = false;
    }
}
    
$(document).ready(function() {
    if (window.rcmail) {
        // Register elfinder_init function and hook keypress event
        rcmail.addEventListener('init', elfinder_init);
        window.onkeypress = grab_excape_key;
    }
});
