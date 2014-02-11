if (window.rcmail) {
    load_briefcase = function() {
    	var fm = $('#elfinder').elfinder({
    		url : 'plugins/elfinder/php/connector.php',
    		lang : 'en',
    		width : 800,
    		destroyOnClose : true,
    		getFileCallback : function(files, fm) {

    			console.log(files);

                ts = new Date().getTime()

//                $.ajax({
//                    url: rcmail.url('plugin.elfinder.elfinder_attachments', { _id:rcmail.env.compose_id, _uploadid:ts, _filepath:files }),
//                    type: 'GET',
//                    headers: {'X-Roundcube-Request': rcmail.env.request_token},
//                    success: function(data){ console.log(data); eval(data);},
//                    error: function(o, status, err) { rcmail.http_error(o, status, err, null, 'attachment'); }
//                });
                rcmail.http_request('plugin.elfinder.elfinder_attachments', { _id:rcmail.env.compose_id, _uploadid:ts, _filepath:files });

    		},
    		commandsOptions : {
    			getfile : {
    				oncomplete : 'close',
    				folders : true
    			}
    		}
    	}).dialogelfinder('instance');
    }
}
