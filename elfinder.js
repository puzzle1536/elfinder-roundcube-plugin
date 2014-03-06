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
                           rcmail.http_request(elfinder_function,
                                               { _id:rcmail.env.compose_id,
                                                 _uploadid:ts,
                                                 _filepath:files[id],
                                               });
                       }
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
