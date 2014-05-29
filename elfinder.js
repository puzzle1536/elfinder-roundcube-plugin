var dialog;
var displayed;
var selected_attachment_id = 0;
var selected_message_id = 0;

var briefcase_init = function() {

    // Add <div> element for elfinder
    $( "#mainscreen" ).append( "<div id=\"elfinder\" class=\"popupmenu elfinder-popup\"></div>" );

    selected_message_id = rcmail.env.uid;

    // Register function for clicks on message menu 'Briefcase'
    rcmail.register_command('briefcase-save-all', function() {
         // We set selected_attachment_id to 0, to save all attachments
         selected_attachment_id =  0;
         window.parent.briefcase_save();
    }, !rcmail.message_list);

    // Register function for clicks on attachment menu 'Briefcase'
    rcmail.register_command('briefcase-save', function() {
         window.parent.briefcase_save();
    }, true);

    // Add event listener on attachement menu open to store the correct attachment id
    rcmail.addEventListener('menu-open', function(p) {
        window.parent.selected_attachment_id = p.props.id;
    });

    // Add event listener to enable/disable 'briefcase' menu in message menu
    if (rcmail.message_list) {
        rcmail.message_list.addEventListener('select', function(list) {
            rcmail.enable_command('briefcase-save-all', list.get_selection().length == 1);
            selected_message_id = list.get_selection()[0];
        });
    }
}

var briefcase_load = function(elfinder_function) {
   if (!dialog) {
       // Somebody clicked on the "briefcase" button for the 1st time
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
                       ts = new Date().getTime()
                       rcmail.http_request('plugin.elfinder.load_attachments',
                                           { _id:rcmail.env.compose_id,
                                             _uploadid:ts,
                                             _filepath:files[id].url,
                                           });
                   }
                   displayed = false;
            }
        });
        displayed = true;
    } else {
       // Somebody clicked on the "briefcase" button, toggle display
       if (!displayed) {
           dialog.elfinder('show');
           displayed = true;
       } else {
           dialog.elfinder('hide');
           displayed = false;
       }
    }
}

var briefcase_save = function() {
   if (!dialog) {
       // Somebody clicked on the "briefcase" button for the 1st time
       // Just open the elfinder window

       dialog = $('#elfinder').elfinder({
               url : 'plugins/elfinder/php/connector.php',
               lang : 'en',
               width:  $("#mainscreen").innerWidth()-100,
               height: $("#mainscreen").innerHeight()-100,
               defaultView : 'list',
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
                
               rcmail.http_request('plugin.elfinder.save_attachments',
                                    { _dirpath:folder.url,
                                      _uid:selected_message_id,
                                      _attachment_id:selected_attachment_id });
               displayed = false;

            }
        });
        displayed = true;
    } else {
       // Somebody clicked on the "briefcase" button, toggle display
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
        // Register briefcase_init function and hook keypress event
        rcmail.addEventListener('init', briefcase_init);
        window.onkeypress = grab_excape_key;
    }
});
