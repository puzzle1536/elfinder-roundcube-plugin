if (window.rcmail) {
    briefcase_load = function(elfinder_function) {
        var fm = $('#elfinder').elfinder({
            url : 'plugins/elfinder/php/connector.php',
            lang : 'en',
            width : 800,
            rememberLastDir: true,
//            onlyMimes: ['text'],
            getFileCallback : function(files, fm) {

                ts = new Date().getTime()
                rcmail.http_request(elfinder_function,
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
        });
    }

    briefcase_save = function(elfinder_function) {
        var fm = $('#elfinder').elfinder({
            url : 'plugins/elfinder/php/connector.php',
            lang : 'en',
            width : 800,
            rememberLastDir: true,
//            onlyMimes: ['directory'],
            getFileCallback : function(folder, fm) {
                rcmail.http_request(elfinder_function,
                                    { _dirpath:folder,
                                      _mbox:rcmail.env.mailbox,
                                      _uid:rcmail.env.uid });
            },
            commandsOptions : {
                getfile : {
                    oncomplete : 'destroy',
                    folders : true
                }
            }
        });
    }
}
