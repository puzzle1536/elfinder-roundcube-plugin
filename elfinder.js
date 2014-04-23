var dialog;
var displayed;
var attachment_id = 0;

if (window.rcmail) {

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
                                                 _filepath:files[id],
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

    var briefcase_save = function(msg_uid) {
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
                                        { _dirpath:folder,
                                          _uid:msg_uid,
                                          _attachment_id:attachment_id });
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

    $(document).ready(function() {
        $( "#mainscreen" ).append( "<div id=\"elfinder\" class=\"popupmenu elfinder-popup\"></div>" );

        rcmail.register_command('briefcase-save-all', function() {
             attachment_id =  0;
             window.parent.briefcase_save(rcmail.env.uid);
        }, true);

        rcmail.register_command('briefcase-save', function() {
             window.parent.briefcase_save(rcmail.env.uid);
        }, true);

        // Add event Listener to retrieve attachment id
        rcmail.addEventListener('menu-open', function(p) {attachment_id = p.props.id;});

    })


    // Note this function will be called from both main window and iframe
    var grab_excape_key = function(evt) {
        if (window.parent.displayed && evt.keyCode == 27) {
            window.parent.dialog.elfinder('hide');
            window.parent.displayed = false;
        }
    }

    window.onkeypress = grab_excape_key;

}
