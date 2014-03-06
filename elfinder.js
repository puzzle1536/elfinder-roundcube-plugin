var dialog;
var displayed;

if (window.rcmail) {

    var briefcase_load = function(elfinder_function) {
       if (!dialog) {

           dialog = $('#elfinder').elfinder({
                   url : 'plugins/elfinder/php/connector.php',
                   lang : 'en',
                   width:  $("#mainscreen").innerWidth()-100,
                   height: $("#mainscreen").innerHeight()-100,
                   commands : [
                        'open', 'reload', 'home', 'up', 'back', 'forward', 'getfile', 'quicklook', 
                        'upload', 'edit', 'search', 'info', 'view', 'help', 'sort'
                   ],
                   commandsOptions : {
                       getfile : {
                           oncomplete : 'close',
                           folders : false,
                       }
                   },
                   getFileCallback : function(files, fm) {
                    
                       ts = new Date().getTime()
                       rcmail.http_request(elfinder_function,
                                           { _id:rcmail.env.compose_id,
                                             _uploadid:ts,
                                             _filepath:files,
                                           });
                }
            });
            displayed = true;
        } else {
           if (!displayed) {
               dialog.elfinder('show');
               displayed = true;
           } else {
               dialog.elfinder('hide');
               displayed = false;
           }
        }
    }

    var briefcase_save = function(elfinder_function) {
       if (!dialog) {

           dialog = $('#elfinder').elfinder({
                   url : 'plugins/elfinder/php/connector.php',
                   lang : 'en',
                   width:  $("#mainscreen").innerWidth()-100,
                   height: $("#mainscreen").innerHeight()-100,
                   commandsOptions : {
                       getfile : {
                           oncomplete : 'close',
                           folders : true,
                       }
                   },
                   commands : [
                        'open', 'reload', 'home', 'up', 'back', 'forward', 'getfile', 'quicklook', 
                        'upload', 'edit', 'search', 'info', 'view', 'help', 'sort'
                   ],
                   getFileCallback : function(folder, fm) {
                    
                    rcmail.http_request(elfinder_function,
                                        { _dirpath:folder,
                                          _mbox:rcmail.env.mailbox,
                                          _uid:rcmail.env.uid });
    
                }
            });
            displayed = true;
        } else {
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
    })
}
