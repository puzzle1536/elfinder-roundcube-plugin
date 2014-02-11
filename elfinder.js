if (window.rcmail) {
    load_briefcase = function() {
        var fm = $('#elfinder').elfinder({
            url : 'plugins/elfinder/php/connector.php',
            lang : 'en',
            width : 800,
//            height: document.height,
            rememberLastDir: true,
            defaultView: 'list',
//            ui: ['toolbar', 'tree'],
            getFileCallback : function(files, fm) {

                ts = new Date().getTime()
                rcmail.http_request('plugin.elfinder.elfinder_attachments',
                                    { _id:rcmail.env.compose_id,
                                      _uploadid:ts, 
                                      _filepath:files
                                    });

            },
            commandsOptions : {
                getfile : {
                    oncomplete : 'destroy',
                    folders : false
                }
            }
        }).dialogelfinder('instance');
    }
}
