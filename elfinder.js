var dialog;
var displayed;

if (window.rcmail) {

    var briefcase_load = function(elfinder_function) {
       if (!dialog) {

           dialog = $('#elfinder').elfinder({
                   url : 'plugins/elfinder/php/connector.php',
                   lang : 'en',
                   width : 800,
                   commandsOptions : {
                       getfile : {
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
                   width : 800,
                   commandsOptions : {
                       getfile : {
                           folders : true,
                       }
                   },
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
        $( "#mainscreen" ).append( "<div id=elfinder class=popupmenu></div>" );
    })
}
