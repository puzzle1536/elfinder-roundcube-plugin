<?php

/**
 * elFinder plugin for RC
 *
 * plugin that includes elFinder into Roundcube for various purpose
 *
 * @version 0.1
 * @author Puzzle
 * @licence GNU GPL
 * @url http://github.com/puzzle1536/elfinder-roundcube-plugin
 *
 */

//require_once('plugins/filesystem_attachments/filesystem_attachments.php');

//class elfinder extends filesystem_attachments
class elfinder extends rcube_plugin
{
  public $task = 'mail';

  function init()
  {
      $rcmail = rcmail::get_instance();

      // Include elfinder js & css
      $this->include_stylesheet($this->local_skin_path() . "/css/elfinder.min.css");
      $this->include_stylesheet($this->local_skin_path() . "/css/theme.css");
      $this->include_script("js/elfinder.min.js");
      $this->include_script("elfinder.js");

      // Register hooks and actions
//      $this->add_hook('template_object_messageattachments', array($this, 'attachment_elfinder'));
      $this->add_hook('template_object_composeattachmentlist', array($this, 'attachment_elfinder'));
      $this->register_action('plugin.elfinder.elfinder_attachments', array($this, 'save_attachements'));

  }

    /**
     * Place a link/button after attachments listing to trigger download
     */
    public function attachment_elfinder($p)
    {
        $rcmail = rcmail::get_instance();

        $link = html::a(array(
//            'href' => rcmail_url('plugin.elfinder.elfinder_attachments', array('_mbox' => $rcmail->output->env['mailbox'], '_uid' => $rcmail->output->env['uid'])),
            'value' => 'briefcase',
            'type' => 'button',
            'class' => 'button',
            'onclick' => 'load_briefcase();',
            'style' => 'text-align:center',
            ),
            Q('briefcase')
        );

#        $p['content'] = str_replace('</li>', '</li>' .$link , $p['content']);
        $p['content'] .= $link;

        return $p;
    }

    /**
     * Handler for attachment download action
     */
    public function save_attachements()
    {
        $rcmail = rcmail::get_instance();
//        $imap = $rcmail->storage;
//        $temp_dir = $rcmail->config->get('temp_dir');
//        $tmpfname = tempnam($temp_dir, 'zipdownload');
//        $tempfiles = array($tmpfname);
//        $message = new rcube_message(get_input_value('_uid', RCUBE_INPUT_GET));
//        $attachments = new rcube_message(get_input_value('_uploadid', RCUBE_INPUT_GET));
        $filepath = get_input_value('_filepath', RCUBE_INPUT_GET);
        $uploadid = get_input_value('_uploadid', RCUBE_INPUT_GET);

        $COMPOSE_ID = get_input_value('_id', RCUBE_INPUT_GPC);
        $COMPOSE    = null;
        
        if ($COMPOSE_ID && $_SESSION['compose_data_' . $COMPOSE_ID]) {
          $SESSION_KEY = 'compose_data_' . $COMPOSE_ID;
          $COMPOSE =& $_SESSION[$SESSION_KEY];
        }
        
        if (!$COMPOSE) {
          die("Invalid session var!");
        }


        $rcmail->output->reset();

        if (is_file($filepath)) {
            $attachment = array(
              'path' => $filepath,
              'size' => filesize($filepath),
              'name' => basename($filepath),
              'mimetype' => rc_mime_content_type($filepath, basename($filepath)),
              'group' => $COMPOSE_ID,
            );

//            $attachment = $rcmail->plugins->exec_hook('attachment_save', $attachment);

        $group = $args['group'];

        $userid = rcmail::get_instance()->user->ID;
        list($usec, $sec) = explode(' ', microtime());
        $attachment['id'] = preg_replace('/[^0-9]/', '', $userid . $sec . $usec);
        $attachment['status'] = true;


            $id = $attachment['id'];

            // store new attachment in session
            unset($attachment['status'], $attachment['abort']);
            $rcmail->session->append($SESSION_KEY.'.attachments', $id, $attachment);
   
//        rcmail::raise_error(array(
//            'code' => 520, 'type' => 'php',
//            'file' => __FILE__, 'line' => __LINE__,
//            'message' => "Cannot load file !!!".$attachment['id'].$attachment['path']), true, false);

            if (($icon = $COMPOSE['deleteicon']) && is_file($icon)) {
              $button = html::img(array(
                'src' => $icon,
                'alt' => rcube_label('delete')
              ));
            }
            else if ($COMPOSE['textbuttons']) {
              $button = Q(rcube_label('delete'));
            }
            else {
              $button = '';
            }
   
            $content = html::a(array(
              'href' => "#delete",
              'onclick' => sprintf("return %s.command('remove-attachment','rcmfile%s', this)", JS_OBJECT_NAME, $id),
              'title' => rcube_label('delete'),
              'class' => 'delete',
            ), $button);
   
            $content .= Q($attachment['name']);
   
            $rcmail->output->command('add2attachment_list', "rcmfile$id", array(
              'html' => $content,
              'name' => $attachment['name'],
              'mimetype' => $attachment['mimetype'],
              'classname' => rcmail_filetype2classname($attachment['mimetype'], $attachment['name']),
              'complete' => true), $uploadid);
   
            // send html page with JS calls as response
            $rcmail->output->command('auto_save_start', false);
            $rcmail->output->send('iframe');

        }

/*
        // open zip file
        $zip = new ZipArchive();
        $zip->open($tmpfname, ZIPARCHIVE::OVERWRITE);

        foreach ($message->attachments as $part) {
            $pid = $part->mime_id;
            $part = $message->mime_parts[$pid];
            $disp_name = $this->_convert_filename($part->filename);

            if ($part->body) {
                $orig_message_raw = $part->body;
                $zip->addFromString($disp_name, $orig_message_raw);
            }
            else {
                $tmpfn = tempnam($temp_dir, 'zipattach');
                $tmpfp = fopen($tmpfn, 'w');
                $imap->get_message_part($message->uid, $part->mime_id, $part, null, $tmpfp, true);
                $tempfiles[] = $tmpfn;
                fclose($tmpfp);
                $zip->addFile($tmpfn, $disp_name);
            }

        }

        $zip->close();

        $filename = ($message->subject ? $message->subject : 'roundcube') . '.zip';
        $this->_deliver_zipfile($tmpfname, $filename);

        // delete temporary files from disk
        foreach ($tempfiles as $tmpfn)
            unlink($tmpfn);

        exit;*/
    }


}
